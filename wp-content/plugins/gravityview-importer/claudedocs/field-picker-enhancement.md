# Rich Field Picker Enhancement

*Transforming GravityImport's field selection from blind choice to informed decision*

---

## Executive Summary

**Problem**: Users mapping CSV columns to Gravity Forms fields don't understand what field types mean or what data format they expect. The current `<select>` dropdown shows only field labels with no context.

**Solution**: A rich field picker component that shows field type descriptions, available choices, format expectations, and compatibility indicators—modeled after industry leaders Airtable, Notion, Flatfile, and OneSchema.

**Impact**: Reduces mapping errors, eliminates trial-and-error, and transforms the import experience from confusing to confident.

---

## The Problem: Information Asymmetry

### Current State

Users see this when mapping:

```
[Select Field ▼]
├── Newsletter Preferences
├── Email Address
├── Date of Birth
├── Product Interest
└── ...
```

### What Users Need to Know

| Question | Current Answer | Needed Answer |
|----------|----------------|---------------|
| What does "Newsletter Preferences" expect? | ❓ Unknown | ☑️ Checkboxes: Weekly, Monthly, Products |
| Is "Date of Birth" MM/DD or DD/MM? | ❓ Unknown | 📅 Format: MM/DD/YYYY |
| Can "Product Interest" accept multiple values? | ❓ Unknown | 🔘 Single selection only |
| Will my CSV data work with this field? | ❓ Unknown | ✅ 95% compatible |

### The Cost of Guessing

- **Failed imports** due to format mismatches
- **Data loss** when multi-values go to single-select fields
- **User frustration** requiring multiple import attempts
- **Support burden** from preventable mapping errors

---

## Industry Leader Analysis

### Airtable Field Selector

**Pattern**: Visual grid with rich information

| Feature | Implementation |
|---------|----------------|
| Field type icons | Color-coded icons for each type |
| Descriptions | Brief text below each type name |
| Hover preview | Expanded card with examples |
| Categories | "Basic", "Advanced", "Relations" groups |
| Search | Type-ahead filtering |

**Key Insight**: Shows *what the field does* before you select it.

### Notion Property Selector

**Pattern**: Categorized list with inline examples

| Feature | Implementation |
|---------|----------------|
| Inline examples | "e.g., In Progress, Done" shown in dropdown |
| Help text | "Select from a list of options" description |
| Grouped sections | Logical categorization |
| Immediate config | Options shown right after selection |

**Key Insight**: Examples are shown *inline*, not hidden behind hover.

### Flatfile Import Mapper

**Pattern**: Compatibility-focused selection

| Feature | Implementation |
|---------|----------------|
| Match percentage | "95% match" badge on each option |
| Format badges | "[Date]" "[Email]" type indicators |
| Validation preview | Shows if data will pass validation |
| Suggested section | AI recommendations at top |

**Key Insight**: Tells you *if your data will work* before you commit.

### OneSchema Smart Mapper

**Pattern**: Explanation-driven interface

| Feature | Implementation |
|---------|----------------|
| "Why this mapping?" | Explains recommendation reasoning |
| Transformation preview | Shows how data will be converted |
| Warning indicators | Proactive problem detection |
| Alternative suggestions | "Did you mean...?" options |

**Key Insight**: Explains *why* a mapping is suggested, building user trust.

---

## Proposed Design

### Visual Mockup

```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 Search fields...                                         │
├─────────────────────────────────────────────────────────────┤
│ ✨ SUGGESTED FOR "newsletter_signup"                        │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ☑️ Newsletter Preferences          [Checkboxes]    95%  │ │
│ │    Multiple selections: Weekly Update, Monthly Digest   │ │
│ │    ✓ Your values match available choices                │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ ⊘ DO NOT IMPORT                                             │
├─────────────────────────────────────────────────────────────┤
│ TEXT FIELDS                                                 │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 📝 Full Name                       [Text]          72%  │ │
│ │    Single line of text, up to 255 characters            │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 📝 Comments                        [Textarea]      68%  │ │
│ │    Multi-line text, unlimited length                    │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ CHOICE FIELDS                                               │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🔘 Product Interest                [Radio]         45%  │ │
│ │    Single choice: Software, Hardware, Services          │ │
│ │    ⚠️ Your data has multiple values; only first imports │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ☑️ Features Used                   [Checkboxes]    88%  │ │
│ │    Multiple selections: Import, Export, Sync, API       │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 📋 Status                          [Dropdown]      82%  │ │
│ │    Single choice: Active, Pending, Cancelled            │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ DATE & TIME FIELDS                                          │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 📅 Date of Birth                   [Date]          72%  │ │
│ │    Format: MM/DD/YYYY (e.g., 12/25/2024)                │ │
│ │    ⚠️ Your dates appear to be DD/MM/YYYY format         │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🕐 Appointment Time                [Time]          65%  │ │
│ │    Format: HH:MM AM/PM (e.g., 2:30 PM)                  │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ NUMBER FIELDS                                               │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🔢 Quantity                        [Number]        90%  │ │
│ │    Numeric value, decimals allowed                      │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ COMPOUND FIELDS                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 👤 Contact Name                    [Name]          40%  │ │
│ │    Sub-fields: Prefix, First, Middle, Last, Suffix      │ │
│ │    ℹ️ Map to specific sub-field for best results        │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 📍 Shipping Address                [Address]       35%  │ │
│ │    Sub-fields: Street, Line 2, City, State, ZIP, Country│ │
│ │    ℹ️ Map to specific sub-field for best results        │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Component Anatomy

```
┌─────────────────────────────────────────────────────────────┐
│ [Icon] [Field Label]              [Type Badge]  [Score]     │
│        [Description / Choices Preview]                      │
│        [Warning or Success Indicator]                       │
└─────────────────────────────────────────────────────────────┘
  │        │                            │            │
  │        │                            │            └─ Compatibility %
  │        │                            └─ Visual type identifier
  │        └─ Primary label from form
  └─ Field type icon
```

### Field Categories

| Category | Field Types | Icon Theme |
|----------|-------------|------------|
| Text Fields | Text, Textarea, Website, Email | 📝 |
| Choice Fields | Radio, Checkboxes, Dropdown, Multi-Select | 🔘 ☑️ 📋 |
| Number Fields | Number, Price | 🔢 💰 |
| Date & Time | Date, Time | 📅 🕐 |
| Compound Fields | Name, Address | 👤 📍 |
| Upload Fields | File Upload | 📎 |
| Special Fields | Hidden, HTML, Consent | ⚙️ |

---

## Compatibility Scoring Algorithm

### Score Components

| Factor | Weight | Description |
|--------|--------|-------------|
| Value Match | 40% | Do CSV values match field choices? |
| Format Match | 30% | Does data format match expectations? |
| Cardinality Match | 20% | Single vs. multiple value alignment |
| Name Similarity | 10% | Column name to field label fuzzy match |

### Value Match Scoring

```javascript
function scoreValueMatch(csvValues, fieldChoices) {
    if (!fieldChoices || fieldChoices.length === 0) {
        return 40; // No choices to match = neutral score
    }

    const uniqueValues = [...new Set(csvValues)];
    const choiceValues = fieldChoices.map(c => c.value.toLowerCase());
    const choiceTexts = fieldChoices.map(c => c.text.toLowerCase());

    let matches = 0;
    for (const value of uniqueValues) {
        const normalized = value.toLowerCase().trim();
        if (choiceValues.includes(normalized) ||
            choiceTexts.includes(normalized)) {
            matches++;
        }
    }

    const matchRatio = matches / uniqueValues.length;
    return Math.round(matchRatio * 40);
}
```

### Format Match Scoring

| Field Type | Expected Format | Scoring Logic |
|------------|-----------------|---------------|
| Email | `*@*.*` | Regex match → 30pts |
| Phone | Digits + formatting | Phone pattern → 30pts |
| Date | Parseable date | Date.parse success → 30pts |
| Number | Numeric | isNaN check → 30pts |
| URL | http(s):// | URL pattern → 30pts |

### Warning Types

| Warning | Trigger | Message |
|---------|---------|---------|
| `value_mismatch` | <50% values match choices | "X of Y values don't match available choices" |
| `multi_to_single` | Multiple CSV values → single-select field | "Your data has multiple values; only first will import" |
| `format_mismatch` | Date/phone format differs | "Your dates appear to be DD/MM/YYYY; field expects MM/DD/YYYY" |
| `type_incompatible` | Text → Number field | "Text values cannot be imported to Number field" |
| `compound_hint` | Mapping to parent compound field | "Map to specific sub-field for best results" |

---

## Data Structure

### Current Field Data (Limited)

```php
// From UI.php get_form_fields()
$form_fields[ $field->id ] = [
    'label'    => $field_label,
    'type'     => $field->type,
    'id'       => $field->id,
    'required' => $field->isRequired,
    'default'  => $field->defaultValue !== '',
];
```

### Enhanced Field Data (Proposed)

```php
$form_fields[ $field->id ] = [
    // Existing
    'label'       => $field_label,
    'type'        => $field->type,
    'id'          => $field->id,
    'required'    => $field->isRequired,
    'default'     => $field->defaultValue !== '',
    'inputs'      => $inputs, // For compound fields

    // NEW: Type Information
    'typeLabel'       => self::get_field_type_label( $field->type ),
    'typeDescription' => self::get_field_type_description( $field->type ),
    'category'        => self::get_field_category( $field->type ),

    // NEW: Choice Fields
    'choices'         => $field->choices ?? [],
    'enableChoiceValue' => $field->enableChoiceValue ?? false,

    // NEW: Format Information
    'storageFormat'   => self::get_storage_format( $field ),
    'allowMultiple'   => self::field_allows_multiple( $field ),
    'dateFormat'      => $field->dateFormat ?? null,
    'phoneFormat'     => $field->phoneFormat ?? null,
    'numberFormat'    => $field->numberFormat ?? null,

    // NEW: Validation
    'validation' => [
        'maxLength'   => $field->maxLength ?? null,
        'rangeMin'    => $field->rangeMin ?? null,
        'rangeMax'    => $field->rangeMax ?? null,
        'inputMask'   => $field->inputMask ?? null,
    ],
];
```

### Field Type Metadata (Static JavaScript)

```javascript
// assets/js/src/utils/field-type-info.js

export const FIELD_TYPE_INFO = {
    text: {
        icon: '📝',
        label: 'Single Line Text',
        description: 'A single line of text, up to 255 characters',
        category: 'text',
        storageFormat: 'string',
        allowMultiple: false,
    },
    textarea: {
        icon: '📝',
        label: 'Paragraph Text',
        description: 'Multi-line text with unlimited length',
        category: 'text',
        storageFormat: 'string',
        allowMultiple: false,
    },
    select: {
        icon: '📋',
        label: 'Dropdown',
        description: 'Single selection from a dropdown list',
        category: 'choice',
        storageFormat: 'single_value',
        allowMultiple: false,
    },
    multiselect: {
        icon: '📋',
        label: 'Multi-Select',
        description: 'Multiple selections from a dropdown list',
        category: 'choice',
        storageFormat: 'json_array',
        allowMultiple: true,
    },
    checkbox: {
        icon: '☑️',
        label: 'Checkboxes',
        description: 'Multiple selections from a list of checkboxes',
        category: 'choice',
        storageFormat: 'pipe_separated',
        allowMultiple: true,
    },
    radio: {
        icon: '🔘',
        label: 'Radio Buttons',
        description: 'Single selection from a list of options',
        category: 'choice',
        storageFormat: 'single_value',
        allowMultiple: false,
    },
    number: {
        icon: '🔢',
        label: 'Number',
        description: 'Numeric value with optional decimal places',
        category: 'number',
        storageFormat: 'number',
        allowMultiple: false,
    },
    date: {
        icon: '📅',
        label: 'Date',
        description: 'Date value in configured format',
        category: 'datetime',
        storageFormat: 'date_string',
        allowMultiple: false,
    },
    time: {
        icon: '🕐',
        label: 'Time',
        description: 'Time value (HH:MM format)',
        category: 'datetime',
        storageFormat: 'time_string',
        allowMultiple: false,
    },
    name: {
        icon: '👤',
        label: 'Name',
        description: 'Compound field with name parts',
        category: 'compound',
        storageFormat: 'compound',
        allowMultiple: false,
        subInputs: {
            '2': 'Prefix',
            '3': 'First',
            '4': 'Middle',
            '6': 'Last',
            '8': 'Suffix',
        },
    },
    address: {
        icon: '📍',
        label: 'Address',
        description: 'Compound field with address parts',
        category: 'compound',
        storageFormat: 'compound',
        allowMultiple: false,
        subInputs: {
            '1': 'Street Address',
            '2': 'Address Line 2',
            '3': 'City',
            '4': 'State/Province',
            '5': 'ZIP/Postal Code',
            '6': 'Country',
        },
    },
    email: {
        icon: '✉️',
        label: 'Email',
        description: 'Email address with validation',
        category: 'text',
        storageFormat: 'string',
        allowMultiple: false,
    },
    phone: {
        icon: '📞',
        label: 'Phone',
        description: 'Phone number with optional formatting',
        category: 'text',
        storageFormat: 'string',
        allowMultiple: false,
    },
    website: {
        icon: '🌐',
        label: 'Website',
        description: 'URL with validation',
        category: 'text',
        storageFormat: 'string',
        allowMultiple: false,
    },
    fileupload: {
        icon: '📎',
        label: 'File Upload',
        description: 'File attachment (URL or path)',
        category: 'upload',
        storageFormat: 'url',
        allowMultiple: false,
    },
    hidden: {
        icon: '⚙️',
        label: 'Hidden',
        description: 'Hidden field, not visible to users',
        category: 'special',
        storageFormat: 'string',
        allowMultiple: false,
    },
    consent: {
        icon: '✓',
        label: 'Consent',
        description: 'Consent checkbox (1 = checked)',
        category: 'special',
        storageFormat: 'boolean',
        allowMultiple: false,
    },
};

export const FIELD_CATEGORIES = {
    text: { label: 'Text Fields', order: 1 },
    choice: { label: 'Choice Fields', order: 2 },
    number: { label: 'Number Fields', order: 3 },
    datetime: { label: 'Date & Time Fields', order: 4 },
    compound: { label: 'Compound Fields', order: 5 },
    upload: { label: 'Upload Fields', order: 6 },
    special: { label: 'Special Fields', order: 7 },
};
```

---

## Implementation Plan

### Phase 1: Enhanced Tooltips (Quick Win)

**Scope**: Add informative tooltips to existing `<select>` dropdown

**Effort**: 4-6 hours

**Changes**:
1. Create `field-type-info.js` with type metadata
2. Enhance `get_form_fields()` to include choices
3. Add tooltip component that shows on option hover/focus
4. Style tooltip with field info, choices preview

**User Experience**:
- Native `<select>` behavior preserved
- Hover on any option shows tooltip with details
- Mobile: Long-press shows tooltip

**Files Modified**:
- `src/UI.php` - Enhance field data
- `assets/js/src/utils/field-type-info.js` - New file
- `assets/js/src/components/step3/enhanced-mapping-ui.jsx` - Add tooltip

### Phase 2: Custom Dropdown Component

**Scope**: Replace native `<select>` with rich custom dropdown

**Effort**: 8-12 hours

**Changes**:
1. Create `RichFieldPicker` React component
2. Implement searchable dropdown with categories
3. Show full field info inline (not just tooltip)
4. Add compatibility score display
5. Preserve keyboard navigation

**User Experience**:
- Click opens rich dropdown overlay
- Search/filter fields by typing
- Fields grouped by category
- Full info visible without hovering
- Keyboard navigation (↑↓ Enter Esc)

**Files Created**:
- `assets/js/src/components/step3/rich-field-picker.jsx`
- `assets/js/src/components/step3/field-option.jsx`
- `assets/scss/components/_rich-field-picker.scss`

### Phase 3: Smart Compatibility Engine

**Scope**: Add intelligent compatibility scoring and warnings

**Effort**: 6-8 hours

**Changes**:
1. Create compatibility scoring algorithm
2. Analyze CSV data against field requirements
3. Show match percentage on each option
4. Display contextual warnings
5. Suggest transformations when applicable

**User Experience**:
- Each field shows compatibility percentage
- Warnings explain potential issues
- Suggested fields highlighted at top
- "Why?" explanations for recommendations

**Files Created**:
- `assets/js/src/utils/compatibility-scorer.js`
- `assets/js/src/utils/data-analyzer.js`

---

## Component Architecture for Independent Testing

### Design Goal: Drop-in Replacement

The `RichFieldPicker` component must maintain the **same interface** as the current native `<select>` so it can be swapped in without changing `map-fields.jsx` logic.

### Current Interface (to preserve)

```javascript
// Current usage in map-fields.jsx (lines 3155-3225)
<select
    ref={(el) => this.DOM[`column-${columnIndex}`] = el}
    id={`column-${columnIndex}`}
    data-automation-id={`existing-form-field-selection-column-${columnIndex}`}
    value={selectedValue}
    onChange={(e) => this.handleExistingFormColumnChange(columnIndex, e.target.value)}
>
    {/* options... */}
</select>
```

### New Component Interface (drop-in compatible)

```jsx
// assets/js/src/components/field-picker/RichFieldPicker.jsx

import PropTypes from 'prop-types';

/**
 * RichFieldPicker - Drop-in replacement for native <select>
 *
 * Maintains exact same interface as native select for zero-friction swap.
 * All new functionality is opt-in via additional props.
 */
const RichFieldPicker = React.forwardRef(({
    // === EXISTING INTERFACE (required for drop-in) ===
    id,
    value,
    onChange,           // Receives synthetic event with e.target.value
    'data-automation-id': automationId,

    // === REQUIRED DATA ===
    formFields,         // Object of form fields from pluginData
    columnIndex,        // Current column being mapped

    // === OPTIONAL ENHANCEMENTS ===
    csvSampleData,      // Sample values for compatibility scoring
    showCompatibility,  // Enable compatibility scores (default: true)
    showFieldInfo,      // Enable field descriptions (default: true)
    enableSearch,       // Enable search filtering (default: true)

    // === SPECIAL VALUES ===
    defaultValue,       // Placeholder value (DEFAULT_COLUMN_FIELD)
    ignoreValue,        // "Do Not Import" value (IGNORE_FIELD_ID)
    addFieldValue,      // "Add Form Field" value

    // === LOCALIZATION ===
    strings,            // Localized strings object

    // === CALLBACKS ===
    onAddField,         // Called when "Add Form Field" selected

    ...rest             // Pass through any other props
}, ref) => {
    // ... implementation
});

RichFieldPicker.propTypes = {
    id: PropTypes.string.isRequired,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    onChange: PropTypes.func.isRequired,
    formFields: PropTypes.object.isRequired,
    columnIndex: PropTypes.number.isRequired,
    csvSampleData: PropTypes.array,
    showCompatibility: PropTypes.bool,
    showFieldInfo: PropTypes.bool,
    enableSearch: PropTypes.bool,
    defaultValue: PropTypes.string,
    ignoreValue: PropTypes.string,
    addFieldValue: PropTypes.string,
    strings: PropTypes.object,
    onAddField: PropTypes.func,
};

RichFieldPicker.defaultProps = {
    showCompatibility: true,
    showFieldInfo: true,
    enableSearch: true,
    defaultValue: '',
    ignoreValue: '-1',
    addFieldValue: 'add_form_field',
};

export default RichFieldPicker;
```

### Public API (index.js)

```javascript
// assets/js/src/components/field-picker/index.js

// Main component
export { default as RichFieldPicker } from './RichFieldPicker';

// Sub-components (for advanced customization)
export { default as FieldOption } from './components/FieldOption';
export { default as FieldCategory } from './components/FieldCategory';
export { default as CompatibilityBadge } from './components/CompatibilityBadge';

// Hooks (for building custom field pickers)
export { useFieldSearch } from './hooks/useFieldSearch';
export { useCompatibility } from './hooks/useCompatibility';
export { useKeyboardNav } from './hooks/useKeyboardNav';

// Utilities
export { FIELD_TYPE_INFO, FIELD_CATEGORIES } from './utils/field-type-info';
export { calculateCompatibility } from './utils/compatibility-scorer';
```

### File Structure for Isolation

The component lives at the top level of `components/` for reusability and clean separation:

```
assets/js/src/components/field-picker/
├── index.js                     # Public API exports
├── RichFieldPicker.jsx          # Main component
├── components/
│   ├── FieldOption.jsx          # Single field option row
│   ├── FieldCategory.jsx        # Category grouping
│   ├── SearchInput.jsx          # Search/filter input
│   └── CompatibilityBadge.jsx   # Score indicator
├── hooks/
│   ├── useFieldSearch.js        # Search filtering logic
│   ├── useCompatibility.js      # Compatibility scoring
│   └── useKeyboardNav.js        # Keyboard navigation
├── utils/
│   ├── compatibility-scorer.js  # Scoring algorithm
│   └── field-type-info.js       # Field metadata
├── __tests__/
│   ├── RichFieldPicker.test.jsx # Unit tests
│   ├── FieldOption.test.jsx
│   ├── compatibility.test.js
│   └── integration.test.jsx     # Full integration tests
├── __stories__/
│   ├── RichFieldPicker.stories.jsx  # Storybook stories
│   └── FieldOption.stories.jsx
└── styles.scss                  # Component styles
```

**Import from step3:**
```jsx
// In map-fields.jsx
import { RichFieldPicker } from '../../field-picker';
```

**Why this structure?**
- **Reusable**: Can be used in other steps or future features
- **Testable**: Run `jest --testPathPattern=field-picker` in isolation
- **Maintainable**: Clear ownership, not coupled to step3 internals
- **Discoverable**: Top-level component is easy to find

### Swap-In Strategy

**Step 1: Feature Flag**

```jsx
// map-fields.jsx (in assets/js/src/components/step3/)
import { RichFieldPicker } from '../../field-picker';

// Use feature flag for gradual rollout
const useRichPicker = pluginData.features?.richFieldPicker ?? false;

// In render:
{useRichPicker ? (
    <RichFieldPicker
        ref={(el) => this.DOM[`column-${columnIndex}`] = el}
        id={`column-${columnIndex}`}
        data-automation-id={`existing-form-field-selection-column-${columnIndex}`}
        value={selectedValue}
        onChange={(e) => this.handleExistingFormColumnChange(columnIndex, e.target.value)}
        formFields={formFields}
        columnIndex={columnIndex}
        csvSampleData={csvData[columnIndex]}
        strings={pluginData.localization.map_fields}
    />
) : (
    <select /* existing implementation */ />
)}
```

**Step 2: Synthetic Event Compatibility**

The `onChange` callback must return an event-like object:

```jsx
// Inside RichFieldPicker
const handleSelect = (fieldId) => {
    // Create synthetic event matching native <select> behavior
    const syntheticEvent = {
        target: {
            value: fieldId,
            id: id,
        },
        currentTarget: {
            value: fieldId,
        },
        // Prevent default available for consistency
        preventDefault: () => {},
        stopPropagation: () => {},
    };

    onChange(syntheticEvent);
};
```

### Independent Testing Strategy

#### 1. Unit Tests (Jest)

```jsx
// __tests__/RichFieldPicker.test.jsx
import { render, screen, fireEvent } from '@testing-library/react';
import RichFieldPicker from '../RichFieldPicker';

const mockFormFields = {
    1: { id: 1, label: 'Email', type: 'email' },
    2: { id: 2, label: 'Name', type: 'name', inputs: [
        { id: '2.3', label: 'First' },
        { id: '2.6', label: 'Last' },
    ]},
};

describe('RichFieldPicker', () => {
    it('calls onChange with synthetic event on selection', () => {
        const handleChange = jest.fn();

        render(
            <RichFieldPicker
                id="test-picker"
                value=""
                onChange={handleChange}
                formFields={mockFormFields}
                columnIndex={0}
            />
        );

        // Open dropdown
        fireEvent.click(screen.getByRole('combobox'));

        // Select field
        fireEvent.click(screen.getByText('Email'));

        // Verify synthetic event
        expect(handleChange).toHaveBeenCalledWith(
            expect.objectContaining({
                target: expect.objectContaining({ value: 1 }),
            })
        );
    });

    it('maintains keyboard navigation', () => {
        render(
            <RichFieldPicker
                id="test-picker"
                value=""
                onChange={() => {}}
                formFields={mockFormFields}
                columnIndex={0}
            />
        );

        const combobox = screen.getByRole('combobox');
        fireEvent.keyDown(combobox, { key: 'ArrowDown' });
        fireEvent.keyDown(combobox, { key: 'Enter' });

        // Verify selection via keyboard
        expect(combobox).toHaveValue('1');
    });

    it('filters fields when searching', () => {
        render(
            <RichFieldPicker
                id="test-picker"
                value=""
                onChange={() => {}}
                formFields={mockFormFields}
                columnIndex={0}
                enableSearch={true}
            />
        );

        fireEvent.click(screen.getByRole('combobox'));
        fireEvent.change(screen.getByRole('searchbox'), {
            target: { value: 'email' }
        });

        expect(screen.getByText('Email')).toBeInTheDocument();
        expect(screen.queryByText('Name')).not.toBeInTheDocument();
    });
});
```

#### 2. Storybook Stories

```jsx
// __stories__/RichFieldPicker.stories.jsx
import RichFieldPicker from '../RichFieldPicker';

export default {
    title: 'Step3/RichFieldPicker',
    component: RichFieldPicker,
    argTypes: {
        showCompatibility: { control: 'boolean' },
        showFieldInfo: { control: 'boolean' },
        enableSearch: { control: 'boolean' },
    },
};

const mockFormFields = {
    1: { id: 1, label: 'Email Address', type: 'email' },
    2: { id: 2, label: 'Newsletter Preferences', type: 'checkbox',
         choices: [
             { text: 'Weekly', value: 'weekly' },
             { text: 'Monthly', value: 'monthly' },
         ]},
    3: { id: 3, label: 'Date of Birth', type: 'date', dateFormat: 'mdy' },
};

const Template = (args) => <RichFieldPicker {...args} />;

export const Default = Template.bind({});
Default.args = {
    id: 'demo-picker',
    value: '',
    formFields: mockFormFields,
    columnIndex: 0,
    onChange: (e) => console.log('Selected:', e.target.value),
};

export const WithSampleData = Template.bind({});
WithSampleData.args = {
    ...Default.args,
    csvSampleData: ['john@example.com', 'jane@test.org', 'bob@company.net'],
};

export const WithCompatibilityScores = Template.bind({});
WithCompatibilityScores.args = {
    ...Default.args,
    csvSampleData: ['weekly', 'monthly', 'daily'],
    showCompatibility: true,
};

export const SearchEnabled = Template.bind({});
SearchEnabled.args = {
    ...Default.args,
    enableSearch: true,
};

export const MinimalMode = Template.bind({});
MinimalMode.args = {
    ...Default.args,
    showCompatibility: false,
    showFieldInfo: false,
    enableSearch: false,
};
```

#### 3. Integration Test

```jsx
// __tests__/integration.test.jsx
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';

/**
 * Integration test: Verify RichFieldPicker works as drop-in
 * for the existing select in map-fields.jsx
 */
describe('RichFieldPicker Integration', () => {
    it('maintains identical interface to native select', async () => {
        const user = userEvent.setup();
        const handleChange = jest.fn();

        // This simulates how map-fields.jsx uses the component
        const { rerender } = render(
            <RichFieldPicker
                ref={jest.fn()}
                id="column-0"
                data-automation-id="existing-form-field-selection-column-0"
                value=""
                onChange={handleChange}
                formFields={mockFormFields}
                columnIndex={0}
            />
        );

        // Interact
        await user.click(screen.getByRole('combobox'));
        await user.click(screen.getByText('Email Address'));

        // Verify change callback matches native select behavior
        const call = handleChange.mock.calls[0][0];
        expect(call.target.value).toBe(1);
        expect(typeof call.target.value).toBe('number'); // or string, match native

        // Verify controlled value update works
        rerender(
            <RichFieldPicker
                id="column-0"
                value={1}
                onChange={handleChange}
                formFields={mockFormFields}
                columnIndex={0}
            />
        );

        expect(screen.getByRole('combobox')).toHaveTextContent('Email Address');
    });
});
```

### npm Scripts for Testing

```json
// package.json
{
    "scripts": {
        "test:field-picker": "jest --testPathPattern=field-picker",
        "test:field-picker:watch": "jest --testPathPattern=field-picker --watch",
        "storybook": "storybook dev -p 6006",
        "storybook:field-picker": "storybook dev -p 6006 --include-story='**/RichFieldPicker*'"
    }
}
```

### Gradual Rollout Plan

| Phase | Audience | Flag Value |
|-------|----------|------------|
| 1. Dev | Developers only | `?richFieldPicker=1` URL param |
| 2. Beta | Beta testers | `define('GK_RICH_FIELD_PICKER', true)` |
| 3. Soft Launch | 10% users | A/B test via filter |
| 4. Full Launch | All users | Default enabled |

```php
// PHP feature flag
add_filter( 'gk/gravityimport/features', function( $features ) {
    $features['richFieldPicker'] = apply_filters(
        'gk/gravityimport/rich_field_picker_enabled',
        defined( 'GK_RICH_FIELD_PICKER' ) && GK_RICH_FIELD_PICKER
    );
    return $features;
});
```

---

## Success Metrics

| Metric | Current | Target | Measurement |
|--------|---------|--------|-------------|
| Mapping accuracy | Unknown | >90% first-try | Track remapping rate |
| Import success rate | Unknown | >95% | Track failed imports |
| Support tickets | Baseline | -50% | Help Scout metrics |
| Time to map | Baseline | -30% | User session analytics |
| User confidence | Baseline | +40% | Post-import survey |

---

## Technical Considerations

### Accessibility

- Maintain ARIA attributes from current implementation
- Keyboard navigation: Arrow keys, Enter, Escape
- Screen reader announcements for field descriptions
- Focus management when dropdown opens/closes
- High contrast mode support

### Performance

- Lazy load field choices (only when dropdown opens)
- Debounce search input
- Virtual scrolling for forms with 50+ fields
- Cache compatibility scores per session

### Mobile

- Touch-friendly target sizes (44px minimum)
- Swipe gestures for scrolling
- Long-press for tooltips (Phase 1)
- Full-screen dropdown on small screens (Phase 2)

---

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Custom dropdown breaks accessibility | High | Extensive ARIA testing, preserve keyboard nav |
| Performance with large forms | Medium | Virtual scrolling, lazy loading |
| Mobile UX regression | Medium | Dedicated mobile testing, responsive design |
| Compatibility scoring inaccuracy | Medium | Conservative scoring, clear "estimated" language |
| Increased bundle size | Low | Code splitting, lazy loading component |

---

## Appendix: Date Format Reference

Gravity Forms supports these date formats:

| Format Key | Pattern | Example | Storage |
|------------|---------|---------|---------|
| `mdy` | MM/DD/YYYY | 12/25/2024 | As entered |
| `dmy` | DD/MM/YYYY | 25/12/2024 | As entered |
| `dmy_dash` | DD-MM-YYYY | 25-12-2024 | As entered |
| `dmy_dot` | DD.MM.YYYY | 25.12.2024 | As entered |
| `ymd_slash` | YYYY/MM/DD | 2024/12/25 | As entered |
| `ymd_dash` | YYYY-MM-DD | 2024-12-25 | As entered |
| `ymd_dot` | YYYY.MM.DD | 2024.12.25 | As entered |

---

## Appendix: Compound Field Sub-Input IDs

### Name Field (Non-Sequential!)

| Sub-ID | Label | Required |
|--------|-------|----------|
| 2 | Prefix | No |
| 3 | First | Yes |
| 4 | Middle | No |
| 6 | Last | Yes |
| 8 | Suffix | No |

*Note: IDs 1, 5, 7 are intentionally skipped by Gravity Forms*

### Address Field

| Sub-ID | Label |
|--------|-------|
| 1 | Street Address |
| 2 | Address Line 2 |
| 3 | City |
| 4 | State/Province |
| 5 | ZIP/Postal Code |
| 6 | Country |

### Time Field

| Sub-ID | Label |
|--------|-------|
| 1 | Hour |
| 2 | Minute |
| 3 | AM/PM |

---

*Document created: 2024-12-30*
*Based on analysis of: Airtable, Notion, Flatfile, OneSchema, Dromo*
