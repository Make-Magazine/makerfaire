# Importer Feature Implementation Comparison

*Deep-dive analysis of how each service implements key features, with best-in-class recommendations for GravityImport*

---

## Feature 1: Sample Data Preview During Mapping

### How Each Service Implements It

#### Flatfile (Best in Class)
- **Implementation**: Tabular preview showing source data → destination data side-by-side
- **Interaction**: Hover over a field to auto-scroll to its preview
- **Search**: Can search for any column and preview dynamically updates
- **Format**: Shows data "exactly as it will appear in its final form"
- **Location**: Enhanced Data Preview panel alongside mapping interface

**Screenshot Description**: Left panel shows uploaded CSV columns with sample values; right panel shows how mapped data will look in the template. Real-time sync between the two.

#### Airtable
- **Implementation**: Right-side panel shows "samples of the records that will be created"
- **Interaction**: Preview updates as you alter field mappings
- **Timing**: Final step before import confirmation
- **Format**: Record-based preview (shows entire rows, not just columns)

**Strength**: Shows full record context, not isolated column values.

#### Mailchimp
- **Implementation**: Preview panel on the right side during mapping step
- **Interaction**: Shows "preview data is importing the correct information"
- **Format**: Column-by-column with sample values in gray text
- **Timing**: Part of the "Match" step in import flow

**Strength**: Clear visual separation between headers and sample data.

#### OneSchema
- **Implementation**: Review & Finalize screen with spreadsheet-style grid
- **Interaction**: Full data grid showing all imported rows
- **Format**: Excel-like interface with cell-level visibility
- **Filtering**: Can filter to show only rows with errors

**Strength**: Full visibility into entire dataset, not just samples.

#### Dromo
- **Implementation**: "Familiar workbook interface" showing mapped data
- **Interaction**: Can edit any cell directly in the preview
- **Format**: Spreadsheet-style with error annotations
- **Timing**: Review step before final import

**Strength**: Preview is also the editing interface - single unified view.

#### HubSpot
- **Implementation**: Basic preview during mapping
- **Interaction**: Shows sample values per column
- **Format**: Dropdown-based column matching with sample values visible

**Weakness**: Less sophisticated than specialized importers.

### Winner: Flatfile

**Why Flatfile Wins**:
1. Side-by-side source → destination view
2. Real-time sync as mappings change
3. Search functionality within preview
4. Hover-to-scroll interaction
5. Shows data in final format

### How to Apply to GravityImport

**Current State**: `enhanced-mapping-ui.jsx` shows column headers in dropdowns but not sample values.

**Recommended Implementation**:

```jsx
// In the mapping dropdown, show 2-3 sample values
<div className="gvi-column-option">
  <span className="column-header">Email</span>
  <span className="sample-values">
    john@example.com, jane@test.org, ...
  </span>
</div>
```

**UI Changes**:
1. Add sample values below each column header in mapping dropdown (Mailchimp style)
2. Add a side panel showing full record preview as mappings change (Flatfile style)
3. Show "Preview of row 1:" with live-updating field values

**Priority**: High - This is the single most impactful UX improvement based on industry patterns.

---

## Feature 2: Remembering Previous Mappings

### How Each Service Implements It

#### Flatfile (Best in Class)
- **Storage**: ML model trained on 1.8 billion rows of mapping data
- **Scope**: Account-wide + global patterns from all users
- **Application**: Auto-applied at mapping step, >90% accuracy
- **Memory Types**:
  - Historical mappings from past imports
  - ML predictions from similar column names
  - Account-specific patterns

**Key Quote**: "Memory of your and your colleagues' past selections to accurately predict over 90% of matching actions"

#### OneSchema
- **Storage**: "Historical mappings" saved per account
- **Scope**: Account-level + AI suggestions for new patterns
- **Application**: Auto-applied, user confirms
- **Memory Types**:
  - Exact historical matches
  - Fuzzy matches from similar imports
  - AI-suggested mappings for unknown columns

**Key Feature**: Historical picklist mappings also saved (not just column→field mappings)

#### Airtable
- **Storage**: Extension remembers mappings per table
- **Scope**: Per-table within workspace
- **Application**: Auto-applied on next CSV upload
- **Reset**: Explicit "Reset" button to clear stored mappings

**Key Quote**: "The extension will remember your field mappings the next time you import another CSV"

#### HubSpot
- **Storage**: "Repeat past import" feature saves full configuration
- **Scope**: Per-import configuration (not per-column pattern)
- **Application**: Manual - user selects "repeat past import"
- **Limitation**: Only exact file structure matches, not fuzzy

**Weakness**: Doesn't learn patterns, only repeats exact configurations.

#### Dromo
- **Storage**: AI-assisted memory + account patterns
- **Scope**: Account-wide
- **Application**: Auto-mapping powered by AI

**Details sparse** - focuses more on AI transformation than mapping memory.

#### Mailchimp
- **Storage**: Auto-matches common field names
- **Scope**: System-wide patterns only (Email, First Name, etc.)
- **Application**: Automatic for known patterns

**Weakness**: Only matches "obvious" fields, doesn't learn custom patterns.

### Winner: Flatfile (with OneSchema close second)

**Why Flatfile Wins**:
1. Global ML model (1.8B rows) provides intelligent suggestions
2. Account-specific memory for custom patterns
3. >90% accuracy claim with evidence
4. Auto-applies without user action
5. Learns from all team members' choices

### How to Apply to GravityImport

**Current State**: No mapping persistence between sessions.

**Recommended Implementation (Phased)**:

**Phase 1: Basic Mapping Memory** (Like Airtable)
```php
// Store mapping in wp_options keyed by form_id + column_hash
$mapping_key = 'gvi_mapping_' . $form_id . '_' . md5(implode(',', sort($column_names)));

// Structure
[
  'form_id' => 123,
  'column_hash' => 'abc123',
  'mappings' => [
    'Email' => 'field_1',
    'First Name' => 'field_2',
  ],
  'last_used' => '2025-01-20',
  'use_count' => 5
]
```

**Phase 2: Pattern Learning** (Like Flatfile)
```php
// Store successful column→field patterns globally
$pattern_key = 'gvi_patterns_' . $form_id;

// Learn that "email" variants map to email field
[
  'email_patterns' => ['email', 'e-mail', 'email_address', 'emailaddress'],
  'mapped_to' => 'field_1',
  'confidence' => 0.95,
  'success_count' => 47
]
```

**UI Changes**:
1. Show "Previous mapping available" banner when column structure matches
2. "Apply previous mapping" button with preview
3. Store mapping on successful import completion
4. Option to save named templates for complex mappings

---

## Feature 3: Pre-Import Validation Preview

### How Each Service Implements It

#### Dromo (Best in Class)
- **Timing**: Immediately after upload, before mapping finalized
- **Display**: Inline cell highlighting in spreadsheet view
- **Details**: Specific error messages per cell with suggestions
- **Actions**:
  - Fix inline directly
  - Apply bulk transformations
  - AI-assisted fixes ("standardize all dates to YYYY-MM-DD")
- **Download**: Can export current state with error annotations for offline fixing

**Key Quote**: "As soon as the CSV is uploaded, highlight any problems directly in the UI"

#### OneSchema
- **Timing**: Review & Finalize screen
- **Display**: Full spreadsheet grid with error highlighting
- **Filtering**: Can show "only rows with errors" or "clean rows only"
- **Actions**:
  - Bulk find & replace
  - Delete rows with errors in bulk
  - Partial import (accept only clean rows)
- **Details**: Improved error messaging for Number/Money types

**Key Feature**: "Partial imports" - import clean rows, skip bad ones.

#### Flatfile
- **Timing**: During mapping and in workbook view
- **Display**: Sparkle icons next to fields with suggestions
- **AI**: "Analyze" feature suggests cleanup actions
- **Actions**: Review suggestions before applying
- **Scope**: Validation based on schema + validation rules

**Key Feature**: AI-generated suggestions with preview before applying.

#### Mailchimp
- **Timing**: During mapping step
- **Display**: Flags potential issues like invalid email formats
- **Actions**: Must fix externally (no inline editing)
- **Scope**: Basic format validation only

**Weakness**: Can't fix issues within the interface.

#### Airtable
- **Timing**: After mapping, before import
- **Display**: Record preview shows issues
- **Actions**: Limited - mainly for review, not editing
- **Scope**: Type compatibility checks

**Weakness**: Preview-only, not actionable within interface.

#### HubSpot
- **Timing**: Post-upload validation
- **Display**: Error report
- **Actions**: Re-upload required for fixes

**Weakness**: No inline editing capability.

### Winner: Dromo

**Why Dromo Wins**:
1. Real-time validation as soon as file uploads
2. Inline editing directly in the preview
3. AI-assisted bulk transformations in natural language
4. Can download annotated file for offline editing
5. Clear, actionable error messages per cell

### How to Apply to GravityImport

**Current State**: Validation happens during import; errors shown in post-import error panel in `import-data.jsx`.

**Recommended Implementation (Phased)**:

**Phase 1: Pre-Import Validation Summary**
```jsx
// After mapping, analyze first 50-100 rows
const validationResults = analyzeData(csvData, mappings, form);

// Show summary badges
<div className="validation-summary">
  <Badge type="success">Email: 48/50 valid</Badge>
  <Badge type="warning">Date: 12 format issues</Badge>
  <Badge type="error">Required fields: 3 missing</Badge>
</div>
```

**Phase 2: Detailed Error Preview**
```jsx
// Show specific issues with row numbers
<ValidationPanel>
  <Issue
    row={15}
    column="Date"
    value="12/31/2024"
    expected="YYYY-MM-DD"
    suggestion="Convert to 2024-12-31?"
  />
</ValidationPanel>
```

**Phase 3: Inline Editing** (Like Dromo)
```jsx
// Editable cell in error review
<EditableCell
  row={15}
  column="Date"
  value={currentValue}
  onSave={(newValue) => updateCsvData(15, 'Date', newValue)}
  validation={dateValidator}
/>
```

**UI Changes**:
1. Add validation step between mapping and import
2. Show row-by-row issues with specific guidance
3. Allow "proceed with warnings" or "fix first" choice
4. For Phase 3: Add inline editing capability

---

## Feature 4: Bulk Data Cleaning / Transformations

### How Each Service Implements It

#### Dromo (Best in Class)
- **Natural Language**: User describes fix in plain English
- **Examples**: "Standardize all dates to YYYY-MM-DD", "trim whitespace"
- **AI**: AI interprets and applies transformation across dataset
- **Scope**: Column-wide transformations
- **Bulk Operations**: Find & replace, format conversion, null handling

**Key Quote**: "AI-assisted transformations... The user can describe a fix in natural language"

#### OneSchema
- **Find & Replace**: Full search/replace with regex support
- **Bulk Delete**: Delete selected rows or all error rows
- **Auto-fix**: "Fix all errors" button for common issues
- **Picklist Mapping**: Bulk remap values to valid options
- **Copy/Paste**: Multi-cell copy/paste for bulk editing

**Key Feature**: Picklist bulk find & replace saved for future imports.

#### Flatfile
- **AI Analyze**: Analyzes data and generates suggestions
- **Transform**: Inline Transform feature for bulk changes
- **Preview**: Review suggestions before applying
- **Scope**: Column-level and row-level transformations
- **Note**: Transform is an add-on (additional cost)

**Key Quote**: "Transform's Analyze feature reads your data and produces Suggestions for data cleanup"

#### Airtable
- **Limited**: No bulk transformation within import flow
- **External**: Must clean data before import
- **Auto-detect**: Can auto-select field types

**Weakness**: No in-app data cleaning.

#### Mailchimp
- **Limited**: Basic format guidelines provided
- **External**: Must format data correctly before upload
- **No Cleaning**: No transformation capabilities

**Weakness**: Zero data cleaning features.

#### HubSpot
- **Third-Party**: Relies on tools like Insycle for data cleaning
- **Native**: No bulk cleaning in native import
- **Insycle**: "Clean, format, and validate all of your data at once"

**Weakness**: Requires external tool.

### Winner: Dromo

**Why Dromo Wins**:
1. Natural language transformations (game-changer for non-technical users)
2. AI interprets intent, applies appropriate transformation
3. Immediate feedback on changes
4. Doesn't require learning regex or special syntax
5. Handles common issues automatically

### How to Apply to GravityImport

**Current State**: No data cleaning features; users must fix CSV externally.

**Recommended Implementation (Phased)**:

**Phase 1: Common Transformers** (Quick wins)
```jsx
const transformers = {
  trimWhitespace: (value) => value.trim(),
  toUpperCase: (value) => value.toUpperCase(),
  toLowerCase: (value) => value.toLowerCase(),
  titleCase: (value) => value.replace(/\w\S*/g, txt =>
    txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
  ),
  normalizeDate: (value, fromFormat, toFormat) => /* date conversion */,
  normalizePhone: (value) => /* phone formatting */,
};

// UI: Dropdown per column with available transformers
<TransformDropdown
  column="Date"
  options={['None', 'Normalize Date (→ YYYY-MM-DD)', 'Trim Whitespace']}
  onSelect={applyTransformer}
/>
```

**Phase 2: Find & Replace**
```jsx
<FindReplacePanel>
  <input placeholder="Find..." value={find} />
  <input placeholder="Replace with..." value={replace} />
  <select>
    <option>All columns</option>
    <option>Selected column only</option>
  </select>
  <Button>Preview Changes</Button>
  <Button>Apply</Button>
</FindReplacePanel>
```

**Phase 3: Smart Suggestions** (Like Flatfile Analyze)
```jsx
// Analyze column and suggest transformations
const suggestions = analyzeColumn(columnData, targetFieldType);

// Display
<SuggestionCard>
  <p>12 dates are in MM/DD/YYYY format</p>
  <Button>Convert all to YYYY-MM-DD</Button>
  <Button>Keep original</Button>
</SuggestionCard>
```

**Priority Transformations**:
1. **Date normalization** (most common import issue)
2. **Whitespace trimming** (invisible but causes failures)
3. **Email validation** (can suggest fixes)
4. **Phone formatting** (country-specific)
5. **Case normalization** (for names, etc.)

---

## Feature 5: Error Resolution UX

### How Each Service Implements It

#### OneSchema (Best in Class)
- **Display**: Spreadsheet grid with error highlighting
- **Sidebar**: Issues summary with counts per error type
- **Navigation**: "Show error" button jumps to problematic cell
- **Bulk Actions**:
  - Delete all rows with errors
  - Delete selected rows via checkboxes
  - Find & replace to fix patterns
- **Filtering**: Show all / errors only / clean only
- **Partial Import**: Accept only clean rows

**Key Quote**: "Second to none... errors sidebar, find & replace functionality"

#### Dromo
- **Display**: Workbook interface with cell-level highlighting
- **Inline Editing**: Click any cell to fix directly
- **Annotations**: Error icons with hover explanations
- **Export**: Download Excel file with error annotations
- **Re-upload**: Upload fixed file, importer resumes where left off

**Key Feature**: Download → fix offline → re-upload continues automatically.

#### Flatfile
- **Display**: Sparkle icons on fields with issues
- **AI Suggestions**: Analyze generates fix recommendations
- **Preview**: See suggestions before applying
- **Validation Messages**: Contextual info on invalid cells

**Key Feature**: AI-powered fix suggestions.

#### GravityImport (Current)
- **Display**: Error panel with grouped errors by type
- **Navigation**: Can jump to error row (partially)
- **Fix-All**: Button to apply fix to all similar errors
- **Download**: Error report downloadable as CSV
- **Limitation**: Must fix externally and re-upload

### Winner: OneSchema (with Dromo close second)

**Why OneSchema Wins**:
1. Comprehensive spreadsheet interface for error review
2. Issues sidebar with clear counts and navigation
3. Multiple resolution paths (inline fix, bulk delete, find/replace)
4. Partial import option (proceed with clean rows)
5. Filtering makes it easy to focus on problems

### How to Apply to GravityImport

**Current Strengths to Preserve**:
- Error grouping by type (keep this)
- Fix-all-errors feature (enhance this)
- Error count indicators (improve visibility)

**Recommended Enhancements**:

**Enhancement 1: Issues Sidebar** (Like OneSchema)
```jsx
<ErrorSidebar>
  <ErrorGroup type="invalid_email" count={5}>
    <ErrorItem row={15} column="Email" value="not-an-email" />
    <ErrorItem row={23} column="Email" value="also-not-valid" />
    {/* Click to navigate */}
  </ErrorGroup>
  <ErrorGroup type="missing_required" count={3}>
    {/* ... */}
  </ErrorGroup>
  <BulkActions>
    <Button>Delete all error rows</Button>
    <Button>Export error report</Button>
  </BulkActions>
</ErrorSidebar>
```

**Enhancement 2: Error Filtering**
```jsx
<FilterBar>
  <FilterButton active={filter === 'all'}>All rows (500)</FilterButton>
  <FilterButton active={filter === 'errors'}>With errors (15)</FilterButton>
  <FilterButton active={filter === 'clean'}>Clean only (485)</FilterButton>
</FilterBar>
```

**Enhancement 3: Partial Import Option** (Like OneSchema)
```jsx
<ImportOptions>
  <Radio checked={importMode === 'all'}>
    Import all rows (errors will be skipped)
  </Radio>
  <Radio checked={importMode === 'clean'}>
    Import clean rows only (15 rows will be skipped)
  </Radio>
  <Radio checked={importMode === 'fix-first'}>
    Fix errors before importing
  </Radio>
</ImportOptions>
```

---

## Feature 6: Progress Indicators

### How Each Service Implements It

#### Dromo (Best in Class)
- **Multi-Stage**: Separate indicators for upload → validation → import
- **Row Counter**: "750,000 of 1,000,000 rows imported..."
- **Error Counter**: Real-time error count as processing happens
- **Background**: Can continue working while import processes
- **Notifications**: Alerts when complete or if issues arise

**Key Quote**: "Progress bar indicating percentage of file processed, or a simple counter of rows imported"

#### Flatfile
- **Auto-Save**: Work preserved even after closing browser
- **Step Indicators**: Clear step-by-step wizard progress
- **Visual**: Progress bar with current step highlighted

**Key Feature**: Auto-save means no lost work.

#### OneSchema
- **Performance**: Handles 1M+ rows with real-time progress
- **Backend**: High-memory Rust servers for speed
- **Visual**: Determinate progress bar with percentage

**Key Feature**: Massive file support with consistent progress feedback.

#### GravityImport (Current)
- **Display**: Circular progress indicator with percentage
- **Message**: "Do not close this page" warning
- **States**: preparing, importing, done, error
- **Polling**: API polling for status updates

### Winner: Dromo (for UX patterns)

**Why Dromo Wins**:
1. Row-by-row counter shows real progress
2. Multi-stage feedback (upload, validate, import)
3. Background processing option
4. Time estimates when possible
5. Clear error counting during process

### How to Apply to GravityImport

**Current Strengths**:
- Circular progress indicator (keep)
- Percentage display (keep)
- Status states (enhance)

**Recommended Enhancements**:

**Enhancement 1: Row Counter**
```jsx
<ProgressDetails>
  <CircularProgress percent={45} />
  <RowCounter>
    Processing row <strong>2,250</strong> of <strong>5,000</strong>
  </RowCounter>
  <TimeEstimate>
    Approximately 2 minutes remaining
  </TimeEstimate>
</ProgressDetails>
```

**Enhancement 2: Multi-Stage Progress**
```jsx
<ImportStages>
  <Stage completed>1. File uploaded</Stage>
  <Stage completed>2. Data validated</Stage>
  <Stage active>3. Importing entries (45%)</Stage>
  <Stage>4. Processing feeds</Stage>
  <Stage>5. Complete</Stage>
</ImportStages>
```

**Enhancement 3: Error Summary During Import**
```jsx
<ImportStats>
  <Stat label="Imported" value="2,250" color="green" />
  <Stat label="Skipped" value="12" color="yellow" />
  <Stat label="Errors" value="3" color="red" />
</ImportStats>
```

---

## Feature 7: Auto-Mapping Intelligence

### How Each Service Implements It

#### Flatfile (Best in Class)
- **ML Model**: Trained on 1.8 billion rows of mapping data
- **Accuracy**: >90% accurate predictions
- **Sources**:
  - Global ML model (all Flatfile users)
  - Account-specific history
  - Team member patterns
- **Categories**: Organizes unmapped columns into 5 smart categories
- **Application**: Auto-applied, user confirms

**Key Quote**: "Using mapping choices recorded from 1.8 billion rows... predict more than 90% of matching actions"

#### OneSchema
- **Strategies**: "exact", "fuzzy", "historical" matching
- **AI Suggestions**: For unmapped columns, AI suggests based on context
- **LLM Integration**: Uses LLM for complex mappings
- **Memory**: Remembers successful historical mappings

**Key Feature**: Multiple strategy combination for best results.

#### GravityImport (Current)
- **Algorithm**: Levenshtein distance for fuzzy matching
- **Patterns**: Regex patterns for common fields (email, phone, date, etc.)
- **Confidence**: Visual confidence scores
- **Auto-map**: Button to apply all suggestions at once
- **Keyboard**: Arrow key navigation in suggestions

**Current Implementation** in `enhanced-mapping-ui.jsx`:
```javascript
findBestFieldMatch(csvHeader) {
  // Pattern matching for known field types
  const patterns = {
    email: /email|e-mail|emailaddress/i,
    name: /name|first.?name|last.?name|full.?name/i,
    phone: /phone|tel|mobile|cell/i,
    // ...
  };

  // Levenshtein distance for fuzzy matching
  // Returns matches with confidence scores
}
```

#### Airtable
- **Exact Matching**: Auto-matches same-name columns
- **Manual**: User selects from dropdown for others
- **Memory**: Remembers per-table

**Weakness**: No fuzzy matching or intelligence.

#### Mailchimp
- **Known Fields**: Auto-matches common fields (Email, First Name, etc.)
- **System Patterns**: Only matches "obvious" fields
- **Manual**: Others require user selection

**Weakness**: Limited to system-defined patterns.

### Winner: Flatfile

**Why Flatfile Wins**:
1. Massive training dataset (1.8B rows)
2. Combined ML + historical + account patterns
3. >90% accuracy (documented)
4. Smart categorization of unmapped columns
5. Continuous learning from user corrections

### How to Apply to GravityImport

**Current Strengths to Preserve**:
- Levenshtein distance matching (good baseline)
- Pattern-based matching for common fields (solid)
- Confidence scores (excellent)
- Keyboard navigation (ahead of competitors)

**Recommended Enhancements**:

**Enhancement 1: Expanded Pattern Library**
```javascript
const enhancedPatterns = {
  email: /email|e-?mail|email.?addr|contact.?email/i,
  firstName: /first.?name|fname|given.?name|forename/i,
  lastName: /last.?name|lname|surname|family.?name/i,
  fullName: /full.?name|name|contact.?name|display.?name/i,
  phone: /phone|tel|mobile|cell|telephone|contact.?number/i,
  address: /address|street|addr|location|mailing/i,
  city: /city|town|municipality/i,
  state: /state|province|region/i,
  zip: /zip|postal|postcode|zip.?code/i,
  country: /country|nation/i,
  company: /company|org|organization|business|employer/i,
  title: /title|job.?title|position|role/i,
  website: /website|url|web|homepage|site/i,
  notes: /notes|comments|description|memo|remarks/i,
};
```

**Enhancement 2: Confidence Thresholds**
```javascript
// Auto-apply high confidence, highlight low confidence for review
const CONFIDENCE_THRESHOLDS = {
  AUTO_APPLY: 0.90,    // Auto-map without asking
  SUGGEST: 0.70,       // Show as primary suggestion
  SHOW: 0.50,          // Include in dropdown
  HIDE: 0.50           // Don't show (too low)
};
```

**Enhancement 3: Historical Learning** (From Phase 2 of Feature 2)
```javascript
// After successful import, store patterns
function learnFromImport(mappings, formId) {
  const patterns = getCurrentPatterns(formId);

  mappings.forEach(({ csvColumn, fieldId }) => {
    // Increment success count for this column→field mapping
    patterns.updatePattern(csvColumn, fieldId, {
      successCount: patterns.get(csvColumn, fieldId).successCount + 1,
      lastUsed: new Date()
    });
  });

  savePatterns(formId, patterns);
}
```

---

## Summary: Best-in-Class by Feature

| Feature | Best Implementation | Runner-Up | Key Differentiator |
|---------|--------------------|-----------|--------------------|
| Sample Data Preview | **Flatfile** | Dromo | Side-by-side source→destination view |
| Remember Mappings | **Flatfile** | OneSchema | 1.8B row ML model + account memory |
| Pre-Import Validation | **Dromo** | OneSchema | Real-time cell highlighting + AI fixes |
| Bulk Data Cleaning | **Dromo** | OneSchema | Natural language transformations |
| Error Resolution | **OneSchema** | Dromo | Issues sidebar + partial import |
| Progress Indicators | **Dromo** | Flatfile | Row counter + multi-stage feedback |
| Auto-Mapping | **Flatfile** | OneSchema | ML + historical + 90%+ accuracy |

---

## Implementation Priority for GravityImport

### Quick Wins (1-2 days each)
1. **Sample values in mapping dropdown** - Show 2-3 values per column
2. **Row counter in progress** - "Processing row X of Y"
3. **Error filtering** - All / Errors only / Clean only buttons

### Medium Effort (1 week each)
4. **Pre-import validation summary** - Badge showing issues per column
5. **Basic mapping memory** - Store/retrieve per form + column hash
6. **Common transformers** - Date normalization, whitespace trim

### Larger Investments (2-4 weeks each)
7. **Full mapping memory with patterns** - Learn from successful imports
8. **Inline error editing** - Fix cells without re-uploading
9. **Find & replace** - Bulk data cleaning
10. **Partial import option** - Import clean rows only

---

## Sources

- [Flatfile Blog: Building a Seamless CSV Import Experience](https://flatfile.com/blog/optimizing-csv-import-experiences-flatfile-portal/)
- [Flatfile: AI Data Mapping](https://flatfile.com/product/mapping/)
- [Flatfile: Analyze and Suggestions](https://support.flatfile.com/articles/7462518044)
- [Airtable: CSV Import Extension](https://support.airtable.com/docs/csv-import-extension)
- [OneSchema: Advanced CSV Import Features](https://www.oneschema.co/blog/advanced-csv-import-features)
- [OneSchema: Using AI](https://docs.oneschema.co/docs/ai-bundle)
- [Dromo: Building a Seamless CSV Importer](https://dromo.io/blog/building-a-seamless-csv-importer)
- [Dromo: Common Data Import Errors](https://dromo.io/blog/common-data-import-errors-and-how-to-fix-them)
- [Mailchimp: Import Contacts](https://mailchimp.com/help/import-contacts-mailchimp/)
- [HubSpot Community: Remembering Mapping Criteria](https://community.hubspot.com/t5/CRM/Remembering-the-Mapping-criteria-when-importing-contacts/m-p/355351)
- [Insycle: HubSpot Magical Import](https://support.insycle.com/hc/en-us/articles/6587330945431)
- [UX Design World: Progress Indicators](https://uxdworld.com/ux-design-of-creative-progress-indicators-with-examples/)
- [Benedict Roeser: CSV Import Best Practices](https://benedictroeser.de/2021/03/csv-import-best-practices/)

---

*Document generated: 2025-12-30*
