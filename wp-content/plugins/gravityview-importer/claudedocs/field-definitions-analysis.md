# Field Definitions Analysis for GravityImport Validation

*Deep investigation of gravity-forms-field-definitions for validation enhancement*

---

## Executive Summary

**Question**: Should GravityImport use gravity-forms-field-definitions to improve validation?

**Answer**: **YES, selectively.** The project provides valuable schema knowledge that makes Error-Driven Transformations significantly more accurate. However, it should be used as a **reference for extraction**, not as a runtime dependency.

---

## Investigation Findings

### What gravity-forms-field-definitions Provides

| Component | Description | Value for Import |
|-----------|-------------|------------------|
| **Compound Sub-Inputs** | Address (1-6), Name (2,3,4,6,8) mappings | 🔴 CRITICAL |
| **Date Format Patterns** | mdy, dmy, ymd_* with regex patterns | 🔴 CRITICAL |
| **Storage Types** | string, number, compound, pipe_list, etc. | 🟡 HIGH |
| **Field Variants** | Settings that affect field behavior | 🟡 HIGH |
| **Validation Rules** | Built-in validators (email, url, range) | 🟢 MEDIUM |
| **Hook Definitions** | GF action/filter hooks | ⚪ NONE |
| **TypeScript Types** | Type definitions for IDE | ⚪ NONE |

### Critical Discovery: Non-Sequential Sub-Input IDs

**Name Field Sub-Inputs**:
```
ID 2 = Prefix
ID 3 = First (required)
ID 4 = Middle
ID 6 = Last (required)  ← NOT ID 5!
ID 8 = Suffix          ← NOT ID 7!
```

This is **essential knowledge** - without it, GravityImport could incorrectly map CSV columns to name sub-fields.

**Address Field Sub-Inputs**:
```
ID 1 = Street Address
ID 2 = Address Line 2
ID 3 = City
ID 4 = State/Province
ID 5 = ZIP/Postal Code
ID 6 = Country
```

### Date Format Definitions

The project defines 7 date format variants that GravityImport must understand:

| Format Key | Pattern | Example |
|------------|---------|---------|
| `mdy` | MM/DD/YYYY | 12/25/2024 |
| `dmy` | DD/MM/YYYY | 25/12/2024 |
| `dmy_dash` | DD-MM-YYYY | 25-12-2024 |
| `dmy_dot` | DD.MM.YYYY | 25.12.2024 |
| `ymd_slash` | YYYY/MM/DD | 2024/12/25 |
| `ymd_dash` | YYYY-MM-DD | 2024-12-25 |
| `ymd_dot` | YYYY.MM.DD | 2024.12.25 |

This enables automatic date transformation suggestions.

---

## Recommended Approach

### Create: `gf-field-definitions.js`

Extract import-relevant data into a focused utility file:

```javascript
// assets/js/src/utils/gf-field-definitions.js

/**
 * Compound field sub-input definitions
 * Source: gravity-forms-field-definitions/src/types/core/storage.types.ts
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
 * Date format patterns for validation and transformation
 * Source: gravity-forms-field-definitions/src/types/fields/date/date.types.ts
 */
export const dateFormatPatterns = {
    mdy: {
        pattern: 'MM/DD/YYYY',
        regex: /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/,
        order: ['month', 'day', 'year'],
        separator: '/'
    },
    dmy: {
        pattern: 'DD/MM/YYYY',
        regex: /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/,
        order: ['day', 'month', 'year'],
        separator: '/'
    },
    dmy_dash: {
        pattern: 'DD-MM-YYYY',
        regex: /^(\d{1,2})-(\d{1,2})-(\d{4})$/,
        order: ['day', 'month', 'year'],
        separator: '-'
    },
    dmy_dot: {
        pattern: 'DD.MM.YYYY',
        regex: /^(\d{1,2})\.(\d{1,2})\.(\d{4})$/,
        order: ['day', 'month', 'year'],
        separator: '.'
    },
    ymd_slash: {
        pattern: 'YYYY/MM/DD',
        regex: /^(\d{4})\/(\d{1,2})\/(\d{1,2})$/,
        order: ['year', 'month', 'day'],
        separator: '/'
    },
    ymd_dash: {
        pattern: 'YYYY-MM-DD',
        regex: /^(\d{4})-(\d{1,2})-(\d{1,2})$/,
        order: ['year', 'month', 'day'],
        separator: '-'
    },
    ymd_dot: {
        pattern: 'YYYY.MM.DD',
        regex: /^(\d{4})\.(\d{1,2})\.(\d{1,2})$/,
        order: ['year', 'month', 'day'],
        separator: '.'
    }
};

/**
 * Storage type to validation type mapping
 */
export const storageTypeValidation = {
    string: { validator: 'text', maxLength: null },
    number: { validator: 'numeric', allowDecimals: true },
    integer: { validator: 'numeric', allowDecimals: false },
    json_array: { validator: 'multiChoice', delimiter: null },
    pipe_list: { validator: 'multiChoice', delimiter: '|' },
    comma_list: { validator: 'multiChoice', delimiter: ',' },
    boolean: {
        validator: 'boolean',
        trueValues: ['1', 'true', 'yes', 'on'],
        falseValues: ['0', 'false', 'no', 'off', '']
    },
    email: { validator: 'email' },
    url: { validator: 'url' }
};

/**
 * Get validation configuration for a field
 */
export function getFieldValidationConfig(fieldType, fieldSettings = {}) {
    const config = {
        type: fieldType,
        isCompound: false,
        subInputs: null,
        dateFormat: null,
        storageType: 'string'
    };

    // Handle compound fields
    if (compoundFieldSubInputs[fieldType]) {
        config.isCompound = true;
        config.subInputs = compoundFieldSubInputs[fieldType];
    }

    // Handle date fields
    if (fieldType === 'date' && fieldSettings.dateFormat) {
        config.dateFormat = dateFormatPatterns[fieldSettings.dateFormat];
    }

    return config;
}
```

---

## Integration with Error-Driven Transformations

### Phase 1 Enhancement: FieldTypeValidator

```javascript
// Use field definitions for accurate compound field validation
import { compoundFieldSubInputs, getFieldValidationConfig } from './gf-field-definitions';

class NameFieldValidator extends BaseFieldValidator {
    validate(value, field) {
        const config = getFieldValidationConfig('name');
        const errors = [];

        // Validate using correct sub-input IDs (2,3,4,6,8 NOT 1,2,3,4,5)
        for (const [id, def] of Object.entries(config.subInputs)) {
            const subValue = value[id];
            if (def.required && !subValue) {
                errors.push({
                    subInput: id,
                    message: `${def.label} is required`,
                    severity: 'error'
                });
            }
        }

        return errors;
    }
}
```

### Phase 2 Enhancement: DateTransformer

```javascript
import { dateFormatPatterns } from './gf-field-definitions';

class DateTransformer {
    detectFormat(value) {
        for (const [key, def] of Object.entries(dateFormatPatterns)) {
            if (def.regex.test(value)) {
                return { format: key, definition: def };
            }
        }
        return null;
    }

    transform(value, targetFormat) {
        const source = this.detectFormat(value);
        if (!source) return { success: false, reason: 'Unknown date format' };

        const target = dateFormatPatterns[targetFormat];
        if (!target) return { success: false, reason: 'Invalid target format' };

        // Parse source
        const match = source.definition.regex.exec(value);
        const parts = {};
        source.definition.order.forEach((part, i) => {
            parts[part] = match[i + 1];
        });

        // Format to target
        const result = target.order.map(part => {
            const val = parts[part];
            return part === 'year' ? val : val.padStart(2, '0');
        }).join(target.separator);

        return { success: true, value: result };
    }
}
```

### Phase 3 Enhancement: Transformation Suggestions

With field definitions, suggestions become highly specific:

| Without Definitions | With Definitions |
|---------------------|------------------|
| "Date format mismatch" | "Value '25/12/2024' appears to be DD/MM/YYYY format. Target field expects MM/DD/YYYY. Convert to '12/25/2024'?" |
| "Invalid name field" | "Name sub-field 5 doesn't exist in Gravity Forms. Did you mean sub-field 6 (Last Name)?" |
| "Address mapping error" | "Column 'ZIP Code' mapped to Address Line 2. Should it map to ZIP/Postal Code (sub-field 5)?" |

---

## Effort Estimate

| Task | Effort | Priority |
|------|--------|----------|
| Create gf-field-definitions.js | 2-3 hours | Phase 1.5 |
| Integrate with FieldTypeValidator | 2-3 hours | Phase 1 |
| Integrate with DateTransformer | 2 hours | Phase 2 |
| Enhance suggestion messages | 1-2 hours | Phase 3 |
| **Total Additional Effort** | **7-10 hours** | |

---

## Maintenance Considerations

**Update Frequency**: Gravity Forms rarely changes field structures. Review when:
- GF releases new field types
- GF changes existing field storage (rare)
- Annual review during major GF updates

**Source of Truth**:
- Primary: gravity-forms-field-definitions project
- Secondary: Gravity Forms core code

---

## Conclusion

The gravity-forms-field-definitions project is **valuable as a reference** but should NOT be a runtime dependency. By extracting the import-relevant schema knowledge into a focused `gf-field-definitions.js` file, GravityImport gains:

1. **Accurate compound field mapping** (especially Name field with non-sequential IDs)
2. **Smart date format detection and transformation**
3. **Clear, specific transformation suggestions**
4. **No additional dependencies or runtime overhead**

This approach aligns perfectly with the Error-Driven Transformations plan and will significantly improve validation accuracy and user experience.

---

*Analysis completed: 2024-12-30*
*Reference: /Users/zackkatz/Dropbox/MonoKit/Development/Gravity-Forms/gravity-forms-field-definitions*
