# CSV Data Flow Architecture

## Overview
Understanding how CSV data flows from upload to import is essential for implementing Error-Driven Transformations. This document traces the complete data lifecycle.

## Step 1: CSV Upload

### Frontend (upload.jsx:30-67)
```javascript
uploadCSV(file) {
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('action', 'gv_import_csv_upload');
    // AJAX POST to WordPress
}
```

### Backend (UI.php:1180-1243)
```php
function AJAX_csv_upload() {
    $file = wp_handle_upload($_FILES['csv_file']);
    // Generates: /uploads/gravityview-import/{md5_hash}.csv
    wp_send_json(['path' => $file['file']]);
}
```

**Key Insight**: CSV file stored on server with unique hash filename. Path returned to JavaScript for subsequent operations.

## Step 2: Batch Creation

### Batch CPT Record (Batch.php:100-145)
When mapping begins, a batch record is created:
```php
$batch = Batch::create([
    'form_id'     => $form_id,
    'source'      => $csv_file_path,
    'status'      => 'pending',
    'progress'    => 0,
    'meta'        => [
        'excerpt'     => [],  // First 50 rows (populated after parsing)
        'columns'     => [],  // Auto-detected column mappings
        'total_rows'  => 0,
    ]
]);
```

**Storage**: WordPress CPT `gv_importentry_batch`

## Step 3: CSV Parsing

### Parsing Process (Processor.php:400-893)
```php
function parse_csv($batch_id) {
    $batch = Batch::get($batch_id);
    $csv_path = $batch->source;
    
    // Parse using goodby-csv library
    $rows = $this->read_csv_file($csv_path);
    
    // Extract excerpt (first 50 rows)
    $excerpt = array_slice($rows, 0, 50);
    
    // Auto-detect column metadata
    $columns = [];
    foreach ($headers as $index => $header) {
        $columns[$index] = [
            'label'       => $header,
            '_is_numeric' => $this->detect_numeric($excerpt, $index),
            '_is_email'   => $this->detect_email($excerpt, $index),
            '_is_date'    => $this->detect_date($excerpt, $index),
        ];
    }
    
    // Update batch with parsed data
    Batch::update($batch_id, [
        'meta' => [
            'excerpt'    => $excerpt,
            'columns'    => $columns,
            'total_rows' => count($rows),
        ]
    ]);
}
```

**Key Insight**: Only 50 rows stored for preview. Full CSV remains on disk until import.

## Step 4: React State Management

### Field Mapping UI (map-fields.jsx:150-312)
```javascript
// State structure after parsing
{
    importData: [...],           // Array of 50 preview rows
    columnsMetadata: {           // Per-column detection results
        0: { label: 'Email', _is_email: true },
        1: { label: 'First Name', _is_numeric: false }
    },
    selectedColumnFields: {      // User's mapping choices
        0: {
            _source: 'field',
            _data: {
                gf_field: { id: '1', type: 'email' },
                filterData: {}   // ← Transformations stored here
            }
        }
    }
}
```

### Local Storage Persistence
```javascript
// Key: gv_import_entries
localStorage.setItem('gv_import_entries', JSON.stringify({
    batch_id: 123,
    form_id: 45,
    mappings: selectedColumnFields,
    step: 3
}));
```

## Step 5: Transformation Storage

### Filter Configuration Structure
Transformations are NOT applied to preview data. They're stored as configuration:
```javascript
selectedColumnFields[columnIndex]._data.filterData = {
    type: 'date_transform',
    params: {
        sourceFormat: 'mdy',
        targetFormat: 'ymd_dash'
    }
};
```

### Server-Side Application (Processor.php)
```php
// During actual import, not preview
function apply_column_transform($value, $filter_data) {
    switch ($filter_data['type']) {
        case 'date_transform':
            return $this->transform_date(
                $value,
                $filter_data['params']['sourceFormat'],
                $filter_data['params']['targetFormat']
            );
    }
    return $value;
}
```

**Key Insight**: Preview shows original data. Transformations only applied during row-by-row import.

## Step 6: Import Execution

### Processing Loop
```php
function process_import($batch_id) {
    $batch = Batch::get($batch_id);
    $csv = $this->read_csv_file($batch->source);
    
    foreach ($csv as $row_index => $row) {
        $entry_data = [];
        
        foreach ($mappings as $col_index => $mapping) {
            $value = $row[$col_index];
            
            // Apply transformation if configured
            if (!empty($mapping['filterData'])) {
                $value = $this->apply_column_transform(
                    $value,
                    $mapping['filterData']
                );
            }
            
            $entry_data[$mapping['gf_field']['id']] = $value;
        }
        
        // Create Gravity Forms entry
        GFAPI::add_entry($entry_data);
        
        // Update progress
        Batch::update_progress($batch_id, $row_index / $total);
    }
}
```

## Data Flow Diagram

```
┌─────────────┐    ┌──────────────┐    ┌─────────────────┐
│  CSV File   │───▶│  wp_upload   │───▶│ /uploads/{hash} │
│  (Browser)  │    │  (UI.php)    │    │   (Server)      │
└─────────────┘    └──────────────┘    └────────┬────────┘
                                                │
                                                ▼
┌─────────────┐    ┌──────────────┐    ┌─────────────────┐
│   React     │◀───│  parse_csv   │◀───│  Batch CPT      │
│   State     │    │ (Processor)  │    │  (50 rows)      │
└──────┬──────┘    └──────────────┘    └─────────────────┘
       │
       │ User maps fields + configures transforms
       ▼
┌─────────────┐    ┌──────────────┐    ┌─────────────────┐
│  Mappings   │───▶│ process_row  │───▶│  GF Entry       │
│  + Filters  │    │ (transform)  │    │  (GFAPI)        │
└─────────────┘    └──────────────┘    └─────────────────┘
```

## Key File References

| Component | File | Lines |
|-----------|------|-------|
| CSV Upload AJAX | `src/UI.php` | 1180-1243 |
| Batch CPT Management | `src/Batch.php` | 100-145 |
| CSV Parsing | `src/Processor.php` | 400-893 |
| React Upload Component | `assets/js/src/components/step1/upload.jsx` | 30-67 |
| Field Mapping UI | `assets/js/src/components/step3/map-fields.jsx` | 150-312 |
| Transform Application | `src/Processor.php` | `apply_column_transform()` |

## Error-Driven Transformations Integration

For the Error-Driven Transformations feature:
1. **Validation Layer**: Add client-side validators that check `importData[]` against target field types
2. **Error Detection**: Surface validation errors with row/column references
3. **Transform Suggestions**: Generate `filterData` configurations based on detected mismatches
4. **Preview Updates**: Show transformed values in preview (computed, not stored)
5. **Bulk Operations**: Apply transformations to all rows with same error type
