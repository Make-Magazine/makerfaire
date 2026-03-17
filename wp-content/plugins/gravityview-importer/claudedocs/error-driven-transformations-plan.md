# Error-Driven Transformations Implementation Plan

**Purpose**: Add bulk data cleaning capabilities surfaced inline from validation errors, inspired by Dromo's error-driven approach.

---

## Executive Summary

GravityImport already has significant infrastructure for error-driven transformations. The implementation requires:
1. **Connecting existing callbacks** (`onApplyDateFormat`, `onCleanNumberFormat`, `onAddUrlProtocol`) to actual handlers
2. **Adding transformation preview** in the validation feedback UI
3. **Expanding the transformation catalog** with additional cleaning operations
4. **Implementing bulk "Fix All" for each transformation type**

Estimated scope: **Medium** (leverages 70% existing infrastructure)

---

## Current Architecture Analysis

### Component Hierarchy

```
MapFields (Parent - state holder)
├── importData[]         ← CSV data (state.importData)
├── selectedColumnFields[] ← Mapping config with _data.filter, _data.filterData
├── formFields{}         ← Form field definitions
│
├── EnhancedMappingUI    ← Smart suggestions, error panel, fix buttons
│   ├── renderMappingCard()     → Per-column UI with error display
│   ├── renderEnhancedErrorPanel() → Grouped errors with "Fix All"
│   └── handleFixError()        → Individual error fixing
│
├── RealTimeValidator    ← Validation engine with suggestions
│   ├── validateColumn()        → Email, date, number, phone, URL validation
│   ├── generateSuggestions()   → Creates actionable suggestions with callbacks
│   └── onApplyDateFormat?()    → CALLBACK NOT CONNECTED TO PARENT
│
└── DateFormatFilter     ← Modal with live preview (transformation pattern)
    ├── detectDateFormat()      → Auto-detect from sample
    └── Live preview UI         → "12/31/2024 → December 31, 2024"
```

### Key Findings

| Component | Status | Gap |
|-----------|--------|-----|
| Validation engine | ✅ Exists | - |
| Suggestion generation | ✅ Exists | Callbacks not connected |
| Error grouping | ✅ Exists | - |
| "Fix" buttons | ✅ Exists in UI | Limited error types supported |
| Date format preview | ✅ Exists | Only in modal, not inline |
| Number cleaning | ⚠️ Stub only | `onCleanNumberFormat` callback exists but no handler |
| URL protocol fix | ⚠️ Stub only | `onAddUrlProtocol` callback exists but no handler |
| Bulk transformation | ❌ Missing | No batch apply for transformations |
| Client-side data modification | ❌ Missing | All transforms are server-side during import |

### Data Flow

```
Current Flow (Filter Configuration):
┌─────────────────────────────────────────────────────────────────────┐
│ User selects field → DateFormatFilter modal → Saves to _data.filterData │
│ → Server reads filterData during import → Transforms on server-side    │
└─────────────────────────────────────────────────────────────────────┘

Proposed Flow (Error-Driven Transformation):
┌─────────────────────────────────────────────────────────────────────────┐
│ Validation detects issue → Shows inline suggestion with preview          │
│ → User clicks "Apply" → Updates _data.filterData + shows preview        │
│ → Re-validates → Shows success confirmation                             │
│ → Server applies during import (same as before)                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Full-Data Validation Architecture

**Goal**: Validate the entire CSV dataset (not just the preview) without performance degradation.

### Current Data Flow

```
CSV Upload → File stored on server filesystem
           ↓
Parsing Phase (status = 'parsing')
           ↓
ALL rows inserted into `{$prefix}gv_importentry_rows` table
           ↓
Only ~50 rows stored in batch['meta']['excerpt'] for UI preview
           ↓
Current validation ONLY uses excerpt data (50 rows)
```

### Database Schema (Already Exists)

The `gv_importentry_rows` table stores every CSV row:

```sql
CREATE TABLE `{prefix}gv_importentry_rows` (
    `id`         BIGINT UNSIGNED AUTO_INCREMENT,
    `batch_id`   BIGINT UNSIGNED NOT NULL,
    `entry_id`   BIGINT UNSIGNED,           -- Set after import
    `number`     INT UNSIGNED NOT NULL,      -- CSV row number
    `status`     ENUM('new','processing', 'processed', 'skipped', 'error'),
    `error`      LONGTEXT,
    `data`       LONGTEXT,                   -- JSON encoded row data

    PRIMARY KEY  (`id`),
    KEY batch_id (`batch_id`),
    KEY batch_status (`batch_id`, `status`)
)
```

### Solution: Optimized Batched PHP Validation (MySQL 5.6 Compatible)

**Key Insight**: MySQL 5.6 lacks JSON functions, so we use optimized PHP batching with MySQL for counting and row fetching only.

#### Performance Comparison

| Approach | 10K rows | 100K rows | 1M rows |
|----------|----------|-----------|---------|
| **Naive PHP (load all)** | ~10s | Memory limit | N/A |
| **Batched PHP (2K/batch)** | ~2s | ~20s | Timeout |
| **Optimized Batched** | ~1s | ~10s | ~100s |

#### Strategy: Batched PHP with MySQL Optimization

```
1. Use MySQL for COUNT and paginated fetching only
2. Decode JSON and validate in PHP (no MySQL JSON functions)
3. Process in batches of 2000-5000 rows
4. Cache results aggressively
5. Return aggregated errors, not individual row errors
```

#### Optimized PHP Validation Implementation

```php
/**
 * AJAX handler for full-data validation (MySQL 5.6 compatible)
 * Uses batched processing with intelligent caching
 */
public function AJAX_validate_all_data() {
    $batch_id = absint( $_POST['batch_id'] );
    $schema   = json_decode( stripslashes( $_POST['schema'] ), true );
    $offset   = absint( $_POST['offset'] ?? 0 );
    $limit    = absint( $_POST['limit'] ?? 2000 ); // Rows per batch

    // Check cache first
    $cache_key = 'gv_validation_' . $batch_id . '_' . md5( json_encode( $schema ) );
    if ( $offset === 0 && ( $cached = get_transient( $cache_key ) ) ) {
        wp_send_json_success( $cached );
        return;
    }

    global $wpdb;
    $tables = gv_import_entries_get_db_tables();

    // Get batch of rows (MySQL handles pagination efficiently)
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, number, data FROM {$tables['rows']}
         WHERE batch_id = %d AND status = 'new'
         ORDER BY number ASC
         LIMIT %d OFFSET %d",
        $batch_id, $limit, $offset
    ) );

    // Get total count (cached after first call)
    static $total_cache = [];
    if ( ! isset( $total_cache[ $batch_id ] ) ) {
        $total_cache[ $batch_id ] = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$tables['rows']}
             WHERE batch_id = %d AND status = 'new'",
            $batch_id
        ) );
    }
    $total = $total_cache[ $batch_id ];

    // Build validation map for mapped fields only
    $validators = $this->build_validators( $schema );

    // Process rows with PHP validation
    $error_counts = [];
    $sample_errors = [];
    $max_samples_per_type = 5;

    foreach ( $rows as $row ) {
        $row_data = json_decode( $row->data, true );
        if ( ! is_array( $row_data ) ) continue;

        foreach ( $validators as $column_index => $validator ) {
            $value = $row_data[ $column_index ] ?? '';
            $result = $validator['validate']( $value );

            if ( ! $result['valid'] ) {
                $error_type = $result['error_type'];

                // Increment count
                if ( ! isset( $error_counts[ $column_index ][ $error_type ] ) ) {
                    $error_counts[ $column_index ][ $error_type ] = 0;
                }
                $error_counts[ $column_index ][ $error_type ]++;

                // Collect samples (first N only)
                $sample_key = "{$column_index}_{$error_type}";
                if ( ! isset( $sample_errors[ $sample_key ] ) ) {
                    $sample_errors[ $sample_key ] = [];
                }
                if ( count( $sample_errors[ $sample_key ] ) < $max_samples_per_type ) {
                    $sample_errors[ $sample_key ][] = [
                        'row'        => $row->number,
                        'value'      => $value,
                        'suggestion' => $result['suggestion'] ?? null,
                    ];
                }
            }
        }
    }

    $is_complete = ( $offset + count( $rows ) >= $total );

    $response = [
        'processed'     => $offset + count( $rows ),
        'total'         => intval( $total ),
        'complete'      => $is_complete,
        'error_counts'  => $error_counts,
        'sample_errors' => $sample_errors,
    ];

    // Cache complete results
    if ( $is_complete ) {
        set_transient( $cache_key, $response, 300 ); // 5 minute cache
    }

    wp_send_json_success( $response );
}

/**
 * Build validator closures for each mapped field
 * Pre-compiles validation logic for efficiency
 */
private function build_validators( $schema ) {
    $validators = [];

    foreach ( $schema as $column_index => $mapping ) {
        if ( empty( $mapping['field'] ) ) continue;

        $field_type = $mapping['field']['type'] ?? 'text';
        $field_settings = $mapping['field'];

        $validators[ $column_index ] = [
            'field_type' => $field_type,
            'validate'   => $this->get_validator_for_type( $field_type, $field_settings ),
        ];
    }

    return $validators;
}

/**
 * Get validation closure for a field type
 */
private function get_validator_for_type( $field_type, $field_settings ) {
    switch ( $field_type ) {
        case 'email':
            return function( $value ) {
                if ( empty( $value ) ) return [ 'valid' => true ];
                if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
                    return [
                        'valid'      => false,
                        'error_type' => 'invalid_email',
                        'suggestion' => null, // No auto-fix for emails
                    ];
                }
                return [ 'valid' => true ];
            };

        case 'number':
            return function( $value ) {
                if ( empty( $value ) ) return [ 'valid' => true ];

                // Check if it's already a valid number
                if ( is_numeric( $value ) ) return [ 'valid' => true ];

                // Check if it's a formatted number (currency, commas)
                $clean = preg_replace( '/[$€£,\s]/', '', $value );
                if ( is_numeric( $clean ) ) {
                    return [
                        'valid'      => false,
                        'error_type' => 'formatted_number',
                        'suggestion' => [
                            'action'   => 'clean_number',
                            'original' => $value,
                            'cleaned'  => $clean,
                            'preview'  => "{$value} → {$clean}",
                        ],
                    ];
                }

                return [
                    'valid'      => false,
                    'error_type' => 'non_numeric',
                    'suggestion' => null,
                ];
            };

        case 'date':
            $expected_format = $field_settings['dateFormat'] ?? 'mdy';
            return function( $value ) use ( $expected_format ) {
                if ( empty( $value ) ) return [ 'valid' => true ];

                $detected = $this->detect_date_format( $value );
                if ( ! $detected ) {
                    return [
                        'valid'      => false,
                        'error_type' => 'invalid_date',
                        'suggestion' => null,
                    ];
                }

                if ( $detected !== $expected_format ) {
                    $converted = $this->convert_date_format( $value, $detected, $expected_format );
                    return [
                        'valid'      => false,
                        'error_type' => 'date_format_mismatch',
                        'suggestion' => [
                            'action'          => 'convert_date',
                            'detected_format' => $detected,
                            'target_format'   => $expected_format,
                            'original'        => $value,
                            'converted'       => $converted,
                            'preview'         => "{$value} → {$converted}",
                        ],
                    ];
                }

                return [ 'valid' => true ];
            };

        case 'website':
            return function( $value ) {
                if ( empty( $value ) ) return [ 'valid' => true ];

                // Check if URL has protocol
                if ( preg_match( '/^https?:\/\//i', $value ) ) {
                    // Has protocol, validate URL
                    if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
                        return [ 'valid' => true ];
                    }
                    return [
                        'valid'      => false,
                        'error_type' => 'invalid_url',
                        'suggestion' => null,
                    ];
                }

                // Missing protocol - suggest adding https://
                $with_protocol = 'https://' . ltrim( $value, '/' );
                if ( filter_var( $with_protocol, FILTER_VALIDATE_URL ) ) {
                    return [
                        'valid'      => false,
                        'error_type' => 'missing_protocol',
                        'suggestion' => [
                            'action'   => 'add_protocol',
                            'original' => $value,
                            'fixed'    => $with_protocol,
                            'preview'  => "{$value} → {$with_protocol}",
                        ],
                    ];
                }

                return [
                    'valid'      => false,
                    'error_type' => 'invalid_url',
                    'suggestion' => null,
                ];
            };

        case 'phone':
            return function( $value ) {
                if ( empty( $value ) ) return [ 'valid' => true ];

                // Phone contains letters (excluding common extensions like 'ext')
                if ( preg_match( '/[a-zA-Z]/', preg_replace( '/\b(ext|x)\b/i', '', $value ) ) ) {
                    return [
                        'valid'      => false,
                        'error_type' => 'invalid_phone',
                        'suggestion' => null,
                    ];
                }

                return [ 'valid' => true ];
            };

        default:
            // Text and other fields - no validation
            return function( $value ) {
                return [ 'valid' => true ];
            };
    }
}

/**
 * Detect date format from a value
 */
private function detect_date_format( $value ) {
    $patterns = [
        'mdy'       => '/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',      // MM/DD/YYYY
        'dmy'       => '/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',      // DD/MM/YYYY (ambiguous)
        'ymd_dash'  => '/^(\d{4})-(\d{1,2})-(\d{1,2})$/',        // YYYY-MM-DD
        'dmy_dash'  => '/^(\d{1,2})-(\d{1,2})-(\d{4})$/',        // DD-MM-YYYY
        'dmy_dot'   => '/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/',      // DD.MM.YYYY
    ];

    foreach ( $patterns as $format => $pattern ) {
        if ( preg_match( $pattern, $value, $matches ) ) {
            // For ambiguous MM/DD vs DD/MM, check if first number > 12
            if ( in_array( $format, [ 'mdy', 'dmy' ] ) ) {
                $first = intval( $matches[1] );
                $second = intval( $matches[2] );

                if ( $first > 12 && $second <= 12 ) {
                    return 'dmy'; // First must be day
                } elseif ( $second > 12 && $first <= 12 ) {
                    return 'mdy'; // Second must be day, so first is month
                }
                // If ambiguous, default to mdy (US format)
                return 'mdy';
            }
            return $format;
        }
    }

    return null;
}

/**
 * Convert date from one format to another
 */
private function convert_date_format( $value, $from_format, $to_format ) {
    // Parse the date based on source format
    $parts = $this->parse_date_parts( $value, $from_format );
    if ( ! $parts ) return $value;

    // Rebuild in target format
    return $this->format_date_parts( $parts, $to_format );
}
```

#### i18n: Translatable Error Messages

```php
/**
 * Get localized error type labels and messages
 * All strings wrapped in i18n functions for translation
 */
private function get_error_messages() {
    return [
        'invalid_email' => [
            'label'   => __( 'Invalid email', 'gk-gravityimport' ),
            'message' => __( 'The email address format is invalid.', 'gk-gravityimport' ),
        ],
        'formatted_number' => [
            'label'   => __( 'Formatted number', 'gk-gravityimport' ),
            'message' => __( 'Number contains formatting that will be cleaned during import.', 'gk-gravityimport' ),
        ],
        'non_numeric' => [
            'label'   => __( 'Non-numeric value', 'gk-gravityimport' ),
            'message' => __( 'Expected a number but found text or invalid characters.', 'gk-gravityimport' ),
        ],
        'date_format_mismatch' => [
            'label'   => __( 'Date format mismatch', 'gk-gravityimport' ),
            'message' => __( 'Date format does not match the expected field format.', 'gk-gravityimport' ),
        ],
        'invalid_date' => [
            'label'   => __( 'Invalid date', 'gk-gravityimport' ),
            'message' => __( 'Could not recognize date format.', 'gk-gravityimport' ),
        ],
        'missing_protocol' => [
            'label'   => __( 'Missing URL protocol', 'gk-gravityimport' ),
            'message' => __( 'URL is missing https:// prefix.', 'gk-gravityimport' ),
        ],
        'invalid_url' => [
            'label'   => __( 'Invalid URL', 'gk-gravityimport' ),
            'message' => __( 'The URL format is invalid.', 'gk-gravityimport' ),
        ],
        'invalid_phone' => [
            'label'   => __( 'Invalid phone', 'gk-gravityimport' ),
            'message' => __( 'Phone number contains invalid characters.', 'gk-gravityimport' ),
        ],
    ];
}
```

#### JavaScript: Batched Validation with i18n

```javascript
import { __, sprintf } from '@wordpress/i18n';

/**
 * Validate all rows in batches with progress updates
 */
async function validateAllData(batchId, schema, onProgress) {
    let offset = 0;
    const batchSize = 2000;
    let aggregatedResults = {
        error_counts: {},
        sample_errors: {},
    };

    while (true) {
        onProgress({
            phase: 'validating',
            message: sprintf(
                /* translators: %1$d: number of rows processed, %2$d: total rows */
                __( 'Validating rows %1$d of %2$d…', 'gk-gravityimport' ),
                offset,
                aggregatedResults.total || '...'
            ),
        });

        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'gv_import_validate_all',
                batch_id: batchId,
                schema: JSON.stringify(schema),
                offset: offset,
                limit: batchSize,
                _wpnonce: gvImportNonce
            })
        });

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.data?.message || __( 'Validation failed', 'gk-gravityimport' ));
        }

        // Merge error counts
        for (const [colIdx, errors] of Object.entries(result.data.error_counts || {})) {
            if (!aggregatedResults.error_counts[colIdx]) {
                aggregatedResults.error_counts[colIdx] = {};
            }
            for (const [errType, count] of Object.entries(errors)) {
                aggregatedResults.error_counts[colIdx][errType] =
                    (aggregatedResults.error_counts[colIdx][errType] || 0) + count;
            }
        }

        // Keep first samples only
        for (const [key, samples] of Object.entries(result.data.sample_errors || {})) {
            if (!aggregatedResults.sample_errors[key]) {
                aggregatedResults.sample_errors[key] = samples;
            }
        }

        aggregatedResults.total = result.data.total;
        aggregatedResults.processed = result.data.processed;

        onProgress({
            phase: 'validating',
            processed: result.data.processed,
            total: result.data.total,
            percent: Math.round((result.data.processed / result.data.total) * 100),
        });

        if (result.data.complete) {
            break;
        }

        offset += batchSize;
    }

    const hasErrors = Object.keys(aggregatedResults.error_counts).length > 0;
    return {
        ...aggregatedResults,
        clean: !hasErrors,
    };
}
```

#### a11y-Compliant Validation UI Component

The validation UI must meet WCAG 2.1 AA requirements:
- **Live regions** for progress announcements (screen readers)
- **Focus management** for keyboard navigation
- **Meaningful labels** for interactive elements
- **Color-independent** status indicators

```jsx
import { useState, useRef, useEffect } from 'react';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Full-data validation progress panel with a11y support
 *
 * WCAG 2.1 AA Compliance:
 * - aria-live regions for progress updates
 * - aria-describedby for context
 * - Focus management after validation
 * - Keyboard accessible controls
 */
function ValidationProgressPanel({ batchId, schema, totalRows, onComplete }) {
    const [validating, setValidating] = useState(false);
    const [progress, setProgress] = useState({ processed: 0, total: 0, percent: 0 });
    const [results, setResults] = useState(null);
    const [error, setError] = useState(null);

    // Refs for focus management
    const resultsRef = useRef(null);
    const buttonRef = useRef(null);

    // Move focus to results when validation completes
    useEffect(() => {
        if (results && resultsRef.current) {
            resultsRef.current.focus();
        }
    }, [results]);

    const startValidation = async () => {
        setValidating(true);
        setError(null);

        try {
            const validationResults = await validateAllData(batchId, schema, setProgress);
            setResults(validationResults);
            onComplete(validationResults);
        } catch (err) {
            setError(err.message);
            // Return focus to button on error
            buttonRef.current?.focus();
        } finally {
            setValidating(false);
        }
    };

    const handleKeyDown = (e) => {
        // Allow Enter or Space to trigger validation
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            startValidation();
        }
    };

    return (
        <div
            className="gk-validation-panel"
            role="region"
            aria-label={__( 'Data validation', 'gk-gravityimport' )}
        >
            {/* Screen reader announcements via live region */}
            <div
                className="screen-reader-text"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            >
                {validating && sprintf(
                    /* translators: %1$d: rows processed, %2$d: total rows, %3$d: percentage */
                    __( 'Validating: %1$d of %2$d rows processed (%3$d percent complete)', 'gk-gravityimport' ),
                    progress.processed,
                    progress.total,
                    progress.percent
                )}
            </div>

            {/* Initial state: Prompt to validate */}
            {!validating && !results && !error && (
                <div className="gk-validation-prompt">
                    <p id="validation-description">
                        {sprintf(
                            /* translators: %d: number of preview rows */
                            __( 'Preview validation checked %d rows.', 'gk-gravityimport' ),
                            PREVIEW_SIZE
                        )}
                    </p>
                    <button
                        ref={buttonRef}
                        type="button"
                        className="button button-primary"
                        onClick={startValidation}
                        onKeyDown={handleKeyDown}
                        aria-describedby="validation-description"
                    >
                        {sprintf(
                            /* translators: %s: total number of rows */
                            __( 'Validate all %s rows', 'gk-gravityimport' ),
                            totalRows?.toLocaleString() || '...'
                        )}
                    </button>
                </div>
            )}

            {/* Validating state: Progress indicator */}
            {validating && (
                <div
                    className="gk-validation-progress"
                    role="progressbar"
                    aria-valuenow={progress.percent}
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label={__( 'Validation progress', 'gk-gravityimport' )}
                >
                    <div
                        className="gk-progress-bar"
                        style={{ width: `${progress.percent}%` }}
                    />
                    <span className="gk-progress-text">
                        {sprintf(
                            /* translators: %1$s: rows processed, %2$s: total rows, %3$d: percentage */
                            __( 'Validating: %1$s / %2$s rows (%3$d%%)', 'gk-gravityimport' ),
                            progress.processed.toLocaleString(),
                            progress.total.toLocaleString(),
                            progress.percent
                        )}
                    </span>
                </div>
            )}

            {/* Error state */}
            {error && (
                <div
                    className="gk-validation-error notice notice-error"
                    role="alert"
                >
                    <p>
                        <strong>{__( 'Validation error:', 'gk-gravityimport' )}</strong> {error}
                    </p>
                    <button
                        type="button"
                        className="button"
                        onClick={startValidation}
                    >
                        {__( 'Try again', 'gk-gravityimport' )}
                    </button>
                </div>
            )}

            {/* Results state */}
            {results && (
                <div
                    ref={resultsRef}
                    tabIndex="-1"
                    className="gk-validation-results"
                    aria-label={__( 'Validation results', 'gk-gravityimport' )}
                >
                    {results.clean ? (
                        <div className="notice notice-success" role="status">
                            <p>
                                <span className="dashicons dashicons-yes-alt" aria-hidden="true" />
                                {sprintf(
                                    /* translators: %s: total rows validated */
                                    __( 'All %s rows validated successfully!', 'gk-gravityimport' ),
                                    results.total?.toLocaleString()
                                )}
                            </p>
                        </div>
                    ) : (
                        <ValidationResults
                            errorCounts={results.error_counts}
                            sampleErrors={results.sample_errors}
                            total={results.total}
                        />
                    )}
                </div>
            )}
        </div>
    );
}

/**
 * Accessible validation results display
 */
function ValidationResults({ errorCounts, sampleErrors, total }) {
    const totalErrors = Object.values(errorCounts).reduce((sum, col) => {
        return sum + Object.values(col).reduce((s, c) => s + c, 0);
    }, 0);

    return (
        <div className="gk-validation-results-detail">
            <div className="notice notice-warning" role="status">
                <p>
                    <span className="dashicons dashicons-warning" aria-hidden="true" />
                    {sprintf(
                        /* translators: %1$d: error count, %2$s: total rows */
                        __( 'Found %1$d issues in %2$s rows', 'gk-gravityimport' ),
                        totalErrors,
                        total?.toLocaleString()
                    )}
                </p>
            </div>

            {/* Error summary table with a11y */}
            <table
                className="widefat gk-error-summary"
                role="table"
                aria-label={__( 'Validation errors by column', 'gk-gravityimport' )}
            >
                <thead>
                    <tr>
                        <th scope="col">{__( 'Column', 'gk-gravityimport' )}</th>
                        <th scope="col">{__( 'Issue', 'gk-gravityimport' )}</th>
                        <th scope="col">{__( 'Count', 'gk-gravityimport' )}</th>
                        <th scope="col">{__( 'Sample', 'gk-gravityimport' )}</th>
                    </tr>
                </thead>
                <tbody>
                    {Object.entries(errorCounts).map(([colIdx, errors]) =>
                        Object.entries(errors).map(([errType, count], i) => (
                            <tr key={`${colIdx}-${errType}`}>
                                {i === 0 && (
                                    <th
                                        scope="row"
                                        rowSpan={Object.keys(errors).length}
                                    >
                                        {sprintf(
                                            /* translators: %d: column number */
                                            __( 'Column %d', 'gk-gravityimport' ),
                                            parseInt(colIdx) + 1
                                        )}
                                    </th>
                                )}
                                <td>{getErrorLabel(errType)}</td>
                                <td>{count.toLocaleString()}</td>
                                <td>
                                    <code>{sampleErrors[`${colIdx}_${errType}`]?.[0] || '—'}</code>
                                </td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}

/**
 * Get localized error type label
 */
function getErrorLabel(errorType) {
    const labels = {
        invalid_email: __( 'Invalid email', 'gk-gravityimport' ),
        formatted_number: __( 'Formatted number', 'gk-gravityimport' ),
        non_numeric: __( 'Non-numeric value', 'gk-gravityimport' ),
        date_format_mismatch: __( 'Date format mismatch', 'gk-gravityimport' ),
        invalid_date: __( 'Invalid date', 'gk-gravityimport' ),
        missing_protocol: __( 'Missing URL protocol', 'gk-gravityimport' ),
        invalid_url: __( 'Invalid URL', 'gk-gravityimport' ),
        invalid_phone: __( 'Invalid phone', 'gk-gravityimport' ),
    };
    return labels[errorType] || errorType;
}
```

#### a11y CSS Considerations

```css
/* Screen reader only text */
.screen-reader-text {
    clip: rect(1px, 1px, 1px, 1px);
    clip-path: inset(50%);
    height: 1px;
    width: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
}

/* Focus visible styles for keyboard navigation */
.gk-validation-panel button:focus-visible {
    outline: 2px solid #2271b1;
    outline-offset: 2px;
}

/* Progress bar with sufficient color contrast */
.gk-progress-bar {
    background-color: #2271b1;
    height: 100%;
    transition: width 0.3s ease;
}

/* Results focus state (for screen reader focus management) */
.gk-validation-results:focus {
    outline: 2px solid transparent; /* Visual focus not needed but tabIndex allows programmatic focus */
}
```

### Performance Optimizations

| Technique | Impact | Implementation |
|-----------|--------|----------------|
| **Batch Processing** | Prevents timeout | Process 2000-5000 rows per AJAX request |
| **Indexed Queries** | Fast row fetching | Use existing `batch_status` index |
| **Error Aggregation** | Reduces payload | Group by error type, return summary + samples |
| **Caching** | Avoids revalidation | Cache results keyed by `batch_id + schema_hash` |
| **Early Termination** | UX improvement | Stop on >1000 errors with "too many errors" message |
| **Streaming Progress** | Responsive UI | Return progress after each batch |

### Caching Strategy

```php
/**
 * Cache validation results to avoid re-running on same schema
 */
private function get_cached_validation( $batch_id, $schema ) {
    $cache_key = 'gv_validation_' . $batch_id . '_' . md5( json_encode( $schema ) );
    return get_transient( $cache_key );
}

private function set_cached_validation( $batch_id, $schema, $results, $ttl = 300 ) {
    $cache_key = 'gv_validation_' . $batch_id . '_' . md5( json_encode( $schema ) );
    set_transient( $cache_key, $results, $ttl );
}
```

### Error Aggregation Format

```javascript
// Aggregated error format (returned to UI)
{
    "errors": {
        "date_format": {
            "count": 234,
            "columns": [2, 5],
            "sample_rows": [15, 23, 47, 89, 102],
            "message": "Date format doesn't match expected MM/DD/YYYY"
        },
        "email_invalid": {
            "count": 12,
            "columns": [3],
            "sample_rows": [8, 156, 342],
            "message": "Invalid email address format"
        }
    },
    "suggestions": {
        "date_format": {
            "count": 234,
            "columns": [2, 5],
            "detected_format": "DD/MM/YYYY",
            "target_format": "MM/DD/YYYY",
            "action": "convert_date_format"
        }
    },
    "summary": {
        "total_rows": 5000,
        "rows_with_errors": 246,
        "transformable_errors": 234,
        "blocking_errors": 12
    }
}
```

### UI Flow

```
Step 3: Field Mapping
           ↓
[Preview validation runs automatically - 50 rows]
           ↓
Shows: "Preview checked 50 of 5,000 rows"
           ↓
User clicks: [Validate All Rows]
           ↓
Progress bar: "Validating: 2,000 / 5,000 rows (40%)"
           ↓
Complete: "Validation complete. Found 234 date format issues, 12 invalid emails"
           ↓
[Apply Date Fix to All 234 Rows] [Review Invalid Emails]
```

---

## Implementation Plan

### Phase 1: Connect Existing Infrastructure (Low Effort)

**Goal**: Wire up the existing suggestion callbacks to actual handlers.

#### 1.1 Add Transformation Handlers to MapFields

**File**: `assets/js/src/components/step3/map-fields.jsx`

Add new methods to handle transformation callbacks:

```jsx
/**
 * Apply date format transformation to a column
 * @param {number} columnIndex Column index
 * @param {string} detectedFormat Detected date format (e.g., 'm/d/Y')
 */
handleApplyDateFormat = (columnIndex, detectedFormat) => {
    const { selectedColumnFields } = this.state;

    // Set the filter configuration
    selectedColumnFields[columnIndex]._data.filter = 'dateFormat';
    selectedColumnFields[columnIndex]._data.filterData = {
        format: detectedFormat,
        timezone: null // Optional: could auto-detect
    };

    this.setState({ selectedColumnFields }, () => {
        this.saveCurrentProgress();
        this.handleErrorFilterAfterFieldChange();

        // Dispatch event for RealTimeValidator to re-run validation
        window.dispatchEvent(new CustomEvent('fieldFilterChanged', {
            detail: { columnIndex, filter: 'dateFormat' }
        }));
    });
};

/**
 * Clean number formatting (remove currency symbols, commas)
 * @param {number} columnIndex Column index
 */
handleCleanNumberFormat = (columnIndex) => {
    const { selectedColumnFields } = this.state;

    // Store cleaning instruction in filterData
    selectedColumnFields[columnIndex]._data.filter = 'numberClean';
    selectedColumnFields[columnIndex]._data.filterData = {
        removeCurrency: true,
        removeCommas: true,
        removeSpaces: true
    };

    this.setState({ selectedColumnFields }, () => {
        this.saveCurrentProgress();
        this.handleErrorFilterAfterFieldChange();
        window.dispatchEvent(new CustomEvent('fieldFilterChanged', {
            detail: { columnIndex, filter: 'numberClean' }
        }));
    });
};

/**
 * Add https:// protocol to URLs
 * @param {number} columnIndex Column index
 */
handleAddUrlProtocol = (columnIndex) => {
    const { selectedColumnFields } = this.state;

    selectedColumnFields[columnIndex]._data.filter = 'urlProtocol';
    selectedColumnFields[columnIndex]._data.filterData = {
        protocol: 'https://',
        onlyMissing: true
    };

    this.setState({ selectedColumnFields }, () => {
        this.saveCurrentProgress();
        this.handleErrorFilterAfterFieldChange();
        window.dispatchEvent(new CustomEvent('fieldFilterChanged', {
            detail: { columnIndex, filter: 'urlProtocol' }
        }));
    });
};
```

#### 1.2 Pass Callbacks to RealTimeValidator

**File**: `assets/js/src/components/step3/map-fields.jsx`

When rendering RealTimeValidator (or wherever it's instantiated), pass the callbacks:

```jsx
<RealTimeValidator
    // ... existing props
    onApplyDateFormat={this.handleApplyDateFormat}
    onCleanNumberFormat={this.handleCleanNumberFormat}
    onAddUrlProtocol={this.handleAddUrlProtocol}
/>
```

#### 1.3 Update Schema Generation for New Filters

**File**: `assets/js/src/components/step3/map-fields.jsx`

In `goToNextStep()`, add handling for new filter types:

```jsx
// Add in the schema generation section (around line 909-920)
if (field._data.filter === 'numberClean' && field._data.filterData) {
    meta.number_clean = {
        remove_currency: field._data.filterData.removeCurrency,
        remove_commas: field._data.filterData.removeCommas,
        remove_spaces: field._data.filterData.removeSpaces
    };
}

if (field._data.filter === 'urlProtocol' && field._data.filterData) {
    meta.url_protocol = {
        protocol: field._data.filterData.protocol,
        only_missing: field._data.filterData.onlyMissing
    };
}
```

---

### Phase 1.5: Field Definitions Integration (Low Effort)

**Goal**: Create a field schema data file to enable intelligent validation based on Gravity Forms field type definitions.

**Reference**: See `claudedocs/field-definitions-analysis.md` for complete analysis.

#### 1.5.1 Create Field Definitions Utility

**File**: `assets/js/src/utils/gf-field-definitions.js`

This file provides schema knowledge for accurate validation:

```javascript
/**
 * Compound field sub-input definitions
 * Critical: Name field uses non-sequential IDs (2,3,4,6,8 NOT 1,2,3,4,5)
 */
export const compoundFieldSubInputs = {
    address: {
        '1': { label: 'Street Address', type: 'string' },
        '2': { label: 'Address Line 2', type: 'string' },
        '3': { label: 'City', type: 'string' },
        '4': { label: 'State/Province', type: 'string' },
        '5': { label: 'ZIP/Postal Code', type: 'string' },
        '6': { label: 'Country', type: 'string' }
    },
    name: {
        '2': { label: 'Prefix', type: 'string' },
        '3': { label: 'First', type: 'string', required: true },
        '4': { label: 'Middle', type: 'string' },
        '6': { label: 'Last', type: 'string', required: true },
        '8': { label: 'Suffix', type: 'string' }
    },
    time: {
        '1': { label: 'Hour', type: 'string' },
        '2': { label: 'Minute', type: 'string' },
        '3': { label: 'AM/PM', type: 'string' }
    }
};

/**
 * Date format patterns for detection and transformation
 */
export const dateFormatPatterns = {
    mdy: { pattern: 'MM/DD/YYYY', regex: /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/, order: ['month', 'day', 'year'], separator: '/' },
    dmy: { pattern: 'DD/MM/YYYY', regex: /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/, order: ['day', 'month', 'year'], separator: '/' },
    dmy_dash: { pattern: 'DD-MM-YYYY', regex: /^(\d{1,2})-(\d{1,2})-(\d{4})$/, order: ['day', 'month', 'year'], separator: '-' },
    dmy_dot: { pattern: 'DD.MM.YYYY', regex: /^(\d{1,2})\.(\d{1,2})\.(\d{4})$/, order: ['day', 'month', 'year'], separator: '.' },
    ymd_slash: { pattern: 'YYYY/MM/DD', regex: /^(\d{4})\/(\d{1,2})\/(\d{1,2})$/, order: ['year', 'month', 'day'], separator: '/' },
    ymd_dash: { pattern: 'YYYY-MM-DD', regex: /^(\d{4})-(\d{1,2})-(\d{1,2})$/, order: ['year', 'month', 'day'], separator: '-' },
    ymd_dot: { pattern: 'YYYY.MM.DD', regex: /^(\d{4})\.(\d{1,2})\.(\d{1,2})$/, order: ['year', 'month', 'day'], separator: '.' }
};

/**
 * Get validation configuration for a field type
 */
export function getFieldValidationConfig(fieldType, fieldSettings = {}) {
    const config = {
        type: fieldType,
        isCompound: false,
        subInputs: null,
        dateFormat: null
    };

    if (compoundFieldSubInputs[fieldType]) {
        config.isCompound = true;
        config.subInputs = compoundFieldSubInputs[fieldType];
    }

    if (fieldType === 'date' && fieldSettings.dateFormat) {
        config.dateFormat = dateFormatPatterns[fieldSettings.dateFormat];
    }

    return config;
}
```

#### 1.5.2 Integrate with RealTimeValidator

Update `RealTimeValidator` to use field definitions:

```javascript
import { getFieldValidationConfig, dateFormatPatterns } from '../../utils/gf-field-definitions';

// In validateColumn():
const fieldConfig = getFieldValidationConfig(field.type, field);

// Use for date validation
if (fieldConfig.dateFormat) {
    const targetPattern = fieldConfig.dateFormat;
    // Detect source format vs target format mismatch
}

// Use for compound field validation
if (fieldConfig.isCompound && fieldConfig.subInputs) {
    // Validate sub-input mappings using correct IDs
}
```

#### 1.5.3 Enhance Transformation Suggestions

With field definitions, suggestions become specific:

| Without | With Field Definitions |
|---------|------------------------|
| "Date format mismatch" | "Value '25/12/2024' is DD/MM/YYYY. Field expects MM/DD/YYYY. Convert to '12/25/2024'?" |
| "Invalid name field" | "Name sub-field 5 doesn't exist. Did you mean 6 (Last Name)?" |

---

### Phase 2: Add Inline Transformation Preview (Medium Effort)

**Goal**: Show before → after preview directly in the validation feedback, not just in modals.

#### 2.1 Create TransformationPreview Component

**File**: `assets/js/src/components/step3/partials/transformation-preview.jsx` (new)

```jsx
import _ from 'lodash';
import classNames from 'classnames/bind';
import appStyles from 'css/app';

const styles = classNames.bind(appStyles);

/**
 * Inline preview of data transformations
 * Shows "before → after" for a sample of values
 */
export default class TransformationPreview extends React.Component {
    static defaultProps = {
        sampleValues: [],
        transformFn: null,
        maxSamples: 3,
    };

    renderSampleTransform = (value, index) => {
        const { transformFn } = this.props;
        const transformed = transformFn ? transformFn(value) : value;
        const isChanged = transformed !== value;

        return (
            <div key={index} className={styles('transform-sample')}>
                <span className={styles('transform-before', { 'has-issue': isChanged })}>
                    {this.truncate(value)}
                </span>
                <span className={styles('transform-arrow')}>→</span>
                <span className={styles('transform-after', { 'is-fixed': isChanged })}>
                    {this.truncate(transformed)}
                </span>
            </div>
        );
    };

    truncate = (value, maxLength = 25) => {
        if (!value) return '(empty)';
        return value.length > maxLength
            ? value.substring(0, maxLength) + '...'
            : value;
    };

    render() {
        const { sampleValues, maxSamples, transformType, onApply, onDismiss } = this.props;
        const samples = sampleValues.slice(0, maxSamples).filter(Boolean);

        if (samples.length === 0) return null;

        return (
            <div className={styles('transformation-preview')}>
                <div className={styles('preview-header')}>
                    <span className={styles('preview-icon')}>✨</span>
                    <span className={styles('preview-title')}>
                        {this.getTransformTitle(transformType)}
                    </span>
                </div>

                <div className={styles('preview-samples')}>
                    {samples.map(this.renderSampleTransform)}
                </div>

                <div className={styles('preview-actions')}>
                    <button
                        className={styles('btn-apply')}
                        onClick={onApply}
                    >
                        Apply to All Rows
                    </button>
                    <button
                        className={styles('btn-dismiss')}
                        onClick={onDismiss}
                    >
                        Dismiss
                    </button>
                </div>
            </div>
        );
    }

    getTransformTitle = (type) => {
        const titles = {
            date_format: 'Convert Date Format',
            number_cleaning: 'Clean Number Formatting',
            url_protocol: 'Add URL Protocol',
            email_fix: 'Fix Email Format',
            phone_format: 'Standardize Phone Numbers',
            trim_whitespace: 'Remove Extra Whitespace',
        };
        return titles[type] || 'Transform Data';
    };
}
```

#### 2.2 Integrate Preview into RealTimeValidator Suggestions

**File**: `assets/js/src/components/step3/real-time-validator.jsx`

Update `renderSuggestions()` to show inline preview:

```jsx
renderSuggestion = (suggestion, columnIndex) => {
    const { importData } = this.props;
    const sampleValues = importData
        .slice(1, 6)  // Skip header, take 5 samples
        .map(row => row[columnIndex])
        .filter(Boolean);

    return (
        <div className={styles('suggestion-item')} key={suggestion.type}>
            <div className={styles('suggestion-content')}>
                <span className={styles('suggestion-icon')}>
                    {this.getSuggestionIcon(suggestion.type)}
                </span>
                <span className={styles('suggestion-message')}>
                    {suggestion.message}
                </span>
            </div>

            {/* Inline preview */}
            <TransformationPreview
                sampleValues={sampleValues}
                transformFn={this.getTransformFunction(suggestion.type, suggestion.data)}
                transformType={suggestion.type}
                onApply={suggestion.action}
                onDismiss={() => this.dismissSuggestion(columnIndex, suggestion.type)}
            />
        </div>
    );
};

getTransformFunction = (type, data) => {
    switch (type) {
        case 'date_format':
            return (value) => {
                // Use dayjs to parse and reformat
                const parsed = dayjs(value, data.detectedFormat);
                return parsed.isValid() ? parsed.format('YYYY-MM-DD') : value;
            };
        case 'number_cleaning':
            return (value) => {
                return value.replace(/[$,\s]/g, '');
            };
        case 'url_protocol':
            return (value) => {
                if (value && !value.startsWith('http')) {
                    return 'https://' + value;
                }
                return value;
            };
        default:
            return (value) => value;
    }
};
```

---

### Phase 3: Expand Transformation Catalog (Medium Effort)

**Goal**: Add more transformation types based on common import issues.

#### 3.1 New Transformation Types

| Type | Trigger | Transformation | Priority |
|------|---------|----------------|----------|
| `trim_whitespace` | Leading/trailing spaces detected | `value.trim()` | High |
| `standardize_boolean` | "Yes/No/True/False/1/0" inconsistency | Normalize to form's boolean format | High |
| `phone_format` | Various phone formats detected | Standardize to consistent format | Medium |
| `capitalize_name` | Inconsistent name capitalization | Title case conversion | Medium |
| `email_lowercase` | Mixed case emails | `value.toLowerCase()` | Medium |
| `date_ambiguous` | Ambiguous dates (e.g., 01/02/03) | Present options to user | High |
| `null_values` | "N/A", "null", "-" detected | Convert to empty or skip | Medium |
| `currency_symbol` | Multiple currency symbols | Standardize to single currency | Low |

#### 3.2 Update generateSuggestions in RealTimeValidator

**File**: `assets/js/src/components/step3/real-time-validator.jsx`

```jsx
generateSuggestions = (columnIndex, field, importData) => {
    const suggestions = [];
    const columnData = importData.slice(1).map(row => row[columnIndex]).filter(Boolean);

    // Existing: Date format
    if (field.type === 'date') {
        // ... existing date logic
    }

    // Existing: Number cleaning
    if (field.type === 'number') {
        // ... existing number logic
    }

    // Existing: URL protocol
    if (field.type === 'url' || field.type === 'website') {
        // ... existing URL logic
    }

    // NEW: Whitespace trimming
    const hasExtraWhitespace = columnData.some(
        value => value !== value.trim()
    );
    if (hasExtraWhitespace) {
        suggestions.push({
            type: 'trim_whitespace',
            message: 'Remove extra whitespace from values',
            action: () => this.props.onTrimWhitespace?.(columnIndex),
            data: { affected: columnData.filter(v => v !== v.trim()).length }
        });
    }

    // NEW: Boolean standardization
    if (field.type === 'checkbox' || field.type === 'consent') {
        const booleanPatterns = /^(yes|no|true|false|1|0|y|n)$/i;
        const hasMixedBooleans = columnData.some(
            value => booleanPatterns.test(value.trim())
        );
        if (hasMixedBooleans) {
            suggestions.push({
                type: 'standardize_boolean',
                message: 'Standardize Yes/No values',
                action: () => this.props.onStandardizeBoolean?.(columnIndex),
            });
        }
    }

    // NEW: Name capitalization
    if (field.type === 'name' || field.label?.toLowerCase().includes('name')) {
        const hasInconsistentCase = columnData.some(value => {
            const words = value.split(' ');
            return words.some(word =>
                word.length > 1 && word !== this.toTitleCase(word)
            );
        });
        if (hasInconsistentCase) {
            suggestions.push({
                type: 'capitalize_name',
                message: 'Standardize name capitalization',
                action: () => this.props.onCapitalizeName?.(columnIndex),
            });
        }
    }

    // NEW: Email lowercase
    if (field.type === 'email') {
        const hasUppercaseEmail = columnData.some(
            value => value !== value.toLowerCase()
        );
        if (hasUppercaseEmail) {
            suggestions.push({
                type: 'email_lowercase',
                message: 'Convert emails to lowercase',
                action: () => this.props.onLowercaseEmail?.(columnIndex),
            });
        }
    }

    // NEW: Null value handling
    const nullPatterns = /^(n\/a|na|null|none|-|--|empty)$/i;
    const hasNullValues = columnData.some(
        value => nullPatterns.test(value.trim())
    );
    if (hasNullValues) {
        suggestions.push({
            type: 'null_values',
            message: 'Handle N/A and null values',
            action: () => this.props.onHandleNullValues?.(columnIndex),
            data: { affected: columnData.filter(v => nullPatterns.test(v.trim())).length }
        });
    }

    return suggestions;
};
```

---

### Phase 4: Bulk "Fix All" Operations (Medium Effort)

**Goal**: Apply transformations to all columns with similar issues at once.

#### 4.1 Add Bulk Fix to Error Panel

**File**: `assets/js/src/components/step3/enhanced-mapping-ui.jsx`

Update `renderEnhancedErrorPanel()` to include transformation-specific bulk actions:

```jsx
renderEnhancedErrorPanel = () => {
    const { showErrorPanel } = this.state;
    const errors = this.props.getAllColumnErrors();
    const suggestions = this.props.getAllColumnSuggestions?.() || [];

    if (!showErrorPanel) return null;

    // Group suggestions by type for bulk actions
    const suggestionGroups = _.groupBy(suggestions, 'type');

    return (
        <div className={styles('error-panel-enhanced')}>
            {/* Existing error groups */}
            {/* ... */}

            {/* NEW: Transformation suggestions section */}
            {Object.keys(suggestionGroups).length > 0 && (
                <div className={styles('suggestion-groups')}>
                    <h4 className={styles('section-title')}>
                        Available Transformations
                    </h4>

                    {Object.entries(suggestionGroups).map(([type, typeSuggestions]) => (
                        <div key={type} className={styles('suggestion-group')}>
                            <div className={styles('group-header')}>
                                <span className={styles('group-icon')}>
                                    {this.getSuggestionIcon(type)}
                                </span>
                                <span className={styles('group-title')}>
                                    {this.getSuggestionTitle(type)}
                                </span>
                                <span className={styles('group-count')}>
                                    {typeSuggestions.length} columns
                                </span>
                            </div>

                            <button
                                className={styles('btn-fix-all-suggestions')}
                                onClick={() => this.handleApplyAllSuggestions(type, typeSuggestions)}
                            >
                                Apply to All {typeSuggestions.length} Columns
                            </button>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

handleApplyAllSuggestions = async (type, suggestions) => {
    this.setState({ bulkFixInProgress: type });

    for (const suggestion of suggestions) {
        if (suggestion.action) {
            await suggestion.action();
            // Small delay for visual feedback
            await new Promise(resolve => setTimeout(resolve, 100));
        }
    }

    this.setState({ bulkFixInProgress: null });
    this.showNotification(
        `Applied ${suggestions.length} ${this.getSuggestionTitle(type)} transformations`,
        'success'
    );
};
```

---

### Phase 5: Server-Side Transformation Support (Backend)

**Goal**: Ensure the PHP processor handles the new filter types.

#### 5.1 Update Processor to Handle New Filters

**File**: `includes/class-processor.php` (or equivalent)

```php
/**
 * Apply column transformations based on filter configuration
 *
 * @param string $value The original value
 * @param array $column_meta The column metadata including filter settings
 * @return string The transformed value
 */
private function apply_column_transform( $value, $column_meta ) {
    if ( empty( $column_meta ) ) {
        return $value;
    }

    // Existing: Date format transformation
    if ( ! empty( $column_meta['datetime_format'] ) ) {
        // ... existing date logic
    }

    // NEW: Number cleaning
    if ( ! empty( $column_meta['number_clean'] ) ) {
        $config = $column_meta['number_clean'];

        if ( ! empty( $config['remove_currency'] ) ) {
            $value = preg_replace( '/[$€£¥]/', '', $value );
        }
        if ( ! empty( $config['remove_commas'] ) ) {
            $value = str_replace( ',', '', $value );
        }
        if ( ! empty( $config['remove_spaces'] ) ) {
            $value = str_replace( ' ', '', $value );
        }
    }

    // NEW: URL protocol
    if ( ! empty( $column_meta['url_protocol'] ) ) {
        $config = $column_meta['url_protocol'];
        $protocol = $config['protocol'] ?? 'https://';

        if ( $config['only_missing'] && ! preg_match( '/^https?:\/\//', $value ) ) {
            $value = $protocol . ltrim( $value, '/' );
        }
    }

    // NEW: Trim whitespace
    if ( ! empty( $column_meta['trim_whitespace'] ) ) {
        $value = trim( $value );
    }

    // NEW: Boolean standardization
    if ( ! empty( $column_meta['standardize_boolean'] ) ) {
        $true_values = array( 'yes', 'y', 'true', '1' );
        $false_values = array( 'no', 'n', 'false', '0' );

        $normalized = strtolower( trim( $value ) );

        if ( in_array( $normalized, $true_values, true ) ) {
            $value = '1'; // Or form's configured true value
        } elseif ( in_array( $normalized, $false_values, true ) ) {
            $value = '0'; // Or form's configured false value
        }
    }

    // NEW: Name capitalization
    if ( ! empty( $column_meta['capitalize_name'] ) ) {
        $value = ucwords( strtolower( $value ) );
    }

    // NEW: Email lowercase
    if ( ! empty( $column_meta['email_lowercase'] ) ) {
        $value = strtolower( $value );
    }

    // NEW: Null value handling
    if ( ! empty( $column_meta['handle_null_values'] ) ) {
        $null_patterns = array( 'n/a', 'na', 'null', 'none', '-', '--', 'empty' );

        if ( in_array( strtolower( trim( $value ) ), $null_patterns, true ) ) {
            $value = ''; // Convert to empty string
        }
    }

    return $value;
}
```

---

## UI/UX Design Specifications

### Inline Suggestion Card

```
┌─────────────────────────────────────────────────────────────┐
│ ✨ Convert Date Format                                      │
│                                                             │
│   12/31/2024   →   2024-12-31                               │
│   01/15/2025   →   2025-01-15                               │
│   02/28/2024   →   2024-02-28                               │
│                                                             │
│   [Apply to 1,234 Rows]  [Dismiss]                          │
└─────────────────────────────────────────────────────────────┘
```

### Bulk Actions Panel

```
┌─────────────────────────────────────────────────────────────┐
│ AVAILABLE TRANSFORMATIONS                                   │
│                                                             │
│  📅 Date Format Conversion              3 columns           │
│     [Apply to All 3 Columns]                                │
│                                                             │
│  💰 Remove Currency Symbols             2 columns           │
│     [Apply to All 2 Columns]                                │
│                                                             │
│  🔗 Add URL Protocol                    1 column            │
│     [Apply]                                                 │
│                                                             │
│  [Apply All Transformations (6 total)]                      │
└─────────────────────────────────────────────────────────────┘
```

---

## CSS Requirements

**File**: `assets/css/components/_transformation-preview.scss` (new)

```scss
.transformation-preview {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 8px;

    .preview-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;

        .preview-icon {
            font-size: 1.2em;
        }

        .preview-title {
            font-weight: 600;
            color: #0369a1;
        }
    }

    .preview-samples {
        font-family: 'SF Mono', Monaco, monospace;
        font-size: 0.9em;
    }

    .transform-sample {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 0;

        .transform-before {
            color: #dc2626;
            text-decoration: line-through;

            &.has-issue {
                background: #fee2e2;
                padding: 2px 4px;
                border-radius: 3px;
            }
        }

        .transform-arrow {
            color: #9ca3af;
        }

        .transform-after {
            color: #16a34a;

            &.is-fixed {
                background: #dcfce7;
                padding: 2px 4px;
                border-radius: 3px;
            }
        }
    }

    .preview-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;

        .btn-apply {
            background: #0ea5e9;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;

            &:hover {
                background: #0284c7;
            }
        }

        .btn-dismiss {
            background: transparent;
            color: #64748b;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;

            &:hover {
                background: #f1f5f9;
            }
        }
    }
}
```

---

## Implementation Checklist

### Phase 0: Full-Data Validation Infrastructure (Foundational)
- [ ] Create `AJAX_validate_all_data()` handler in `src/UI.php`
- [ ] Add `validate_value()` method with field-type validation rules
- [ ] Implement `aggregate_errors()` for grouping by error type
- [ ] Implement `aggregate_suggestions()` for transformation recommendations
- [ ] Add validation caching via transients (`batch_id + schema_hash`)
- [ ] Create `validateAllData()` JavaScript function with progress polling
- [ ] Create `ValidationProgressPanel` React component
- [ ] Add "Validate All Rows" button to mapping UI
- [ ] Add progress bar with row count display
- [ ] Integrate validation results with error panel
- [ ] Test with 10K+ row CSV files for performance
- [ ] Add early termination on >1000 errors

### Phase 1: Connect Existing Infrastructure
- [ ] Add `handleApplyDateFormat()` to MapFields
- [ ] Add `handleCleanNumberFormat()` to MapFields
- [ ] Add `handleAddUrlProtocol()` to MapFields
- [ ] Pass callbacks as props to RealTimeValidator
- [ ] Update schema generation for new filter types
- [ ] Test date format auto-apply flow

### Phase 1.5: Field Definitions Integration
- [ ] Create `assets/js/src/utils/gf-field-definitions.js`
- [ ] Add `compoundFieldSubInputs` (address, name, time)
- [ ] Add `dateFormatPatterns` with regex and order
- [ ] Add `getFieldValidationConfig()` helper
- [ ] Integrate with RealTimeValidator
- [ ] Update suggestion messages to be field-definition-aware

### Phase 2: Add Inline Transformation Preview
- [ ] Create `TransformationPreview` component
- [ ] Integrate into RealTimeValidator suggestions
- [ ] Add transform functions for each type
- [ ] Style the preview cards
- [ ] Add animation for apply action

### Phase 3: Expand Transformation Catalog
- [ ] Add `trim_whitespace` suggestion
- [ ] Add `standardize_boolean` suggestion
- [ ] Add `email_lowercase` suggestion
- [ ] Add `null_values` suggestion
- [ ] Add corresponding handlers in MapFields
- [ ] Update RealTimeValidator `generateSuggestions()`

### Phase 4: Bulk Fix Operations
- [ ] Add suggestion grouping to error panel
- [ ] Create `handleApplyAllSuggestions()` method
- [ ] Add progress indicator for bulk operations
- [ ] Add success notification

### Phase 5: Server-Side Support
- [ ] Update `Processor` class for `number_clean`
- [ ] Update `Processor` class for `url_protocol`
- [ ] Update `Processor` class for `trim_whitespace`
- [ ] Update `Processor` class for `standardize_boolean`
- [ ] Update `Processor` class for `email_lowercase`
- [ ] Update `Processor` class for `handle_null_values`
- [ ] Add unit tests for each transformation

---

## Testing Plan

> **📋 Complete test specifications**: See `claudedocs/error-driven-transformations-tests.md` for full test code and data requirements.

### Critical Tests (🔴 MUST HAVE)

| Test | Type | Purpose |
|------|------|---------|
| Name field sub-input IDs (2,3,4,6,8) | Unit (JS) | Prevent data loss from incorrect mapping |
| Date format detection (all 7 formats) | Unit (JS) | Core transformation accuracy |
| Number cleaning transformations | Unit (PHP) | Server-side processing |
| Date transformation acceptance | E2E | Full user workflow validation |

### Unit Tests
- **JavaScript** (`assets/js/src/utils/__tests__/`):
  - `compoundFieldSubInputs` - Verify non-sequential Name field IDs
  - `dateFormatPatterns` - All 7 format regex and order validation
  - `getFieldValidationConfig()` - Field type configuration helper
  - `RealTimeValidator` - Suggestion generation logic

- **PHP** (`tests/test-transformations.php`):
  - `test_clean_number_format()` - Currency, comma, space removal
  - `test_add_url_protocol()` - Protocol prefix handling
  - `test_trim_whitespace()` - Whitespace normalization
  - `test_standardize_boolean()` - Yes/No/True/False conversion
  - `test_email_lowercase()` - Case normalization
  - `test_handle_null_values()` - N/A, null, none conversion
  - `test_combined_transformations()` - Multiple transforms on single value

### Acceptance Tests
- `DateTransformationCest` - Date format suggestion and application
- `NumberTransformationCest` - Currency/comma removal workflow
- `BulkTransformationCest` - Multi-column bulk operations
- `CompoundFieldMappingCest` - Name/Address sub-input mapping

### Test Data Requirements
- **CSVs**: dates_dmy.csv, numbers_with_currency.csv, names.csv, addresses.csv, etc.
- **Forms**: date_mdy.json, number_field.json, name_field.json, address_field.json

---

## Rollout Strategy

1. **Alpha**: Internal testing with complex CSV files
2. **Beta**: Limited rollout to power users with telemetry
3. **GA**: Full rollout with documentation

---

## Success Metrics

- **Reduction in import errors**: Track before/after error rates
- **Transformation adoption**: % of imports using auto-fix
- **User satisfaction**: Track "Dismiss" vs "Apply" ratio
- **Time to import**: Measure time from upload to complete import

---

*Document created: 2025-12-30*
*Based on analysis of: GravityImport codebase, Dromo/Flatfile patterns*
