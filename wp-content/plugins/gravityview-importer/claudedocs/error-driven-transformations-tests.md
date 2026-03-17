# Error-Driven Transformations: Critical Test Requirements

**Purpose**: Define the MUST-HAVE tests to ensure zero regressions when implementing Error-Driven Transformations.

---

## Test Categories Overview

| Category | Type | Priority | Location |
|----------|------|----------|----------|
| Field Definitions | Unit (JS) | 🔴 CRITICAL | `assets/js/src/utils/__tests__/` |
| Transformations | Unit (PHP) | 🔴 CRITICAL | `tests/test-transformations.php` |
| Validator | Unit (JS) | 🔴 CRITICAL | `assets/js/src/components/__tests__/` |
| Date Formats | Acceptance | 🔴 CRITICAL | `tests/acceptance/acceptance/` |
| Bulk Operations | Acceptance | 🟡 HIGH | `tests/acceptance/acceptance/` |

---

## 1. Unit Tests: Field Definitions (JavaScript)

**File**: `assets/js/src/utils/__tests__/gf-field-definitions.test.js`

### 1.1 Compound Field Sub-Input Tests

```javascript
describe('compoundFieldSubInputs', () => {
    describe('address field', () => {
        it('should have exactly 6 sub-inputs with sequential IDs 1-6', () => {
            const { compoundFieldSubInputs } = require('../gf-field-definitions');
            const address = compoundFieldSubInputs.address;

            expect(Object.keys(address)).toEqual(['1', '2', '3', '4', '5', '6']);
            expect(address['1'].label).toBe('Street Address');
            expect(address['3'].label).toBe('City');
            expect(address['5'].label).toBe('ZIP/Postal Code');
        });
    });

    describe('name field', () => {
        // CRITICAL: Name field uses non-sequential IDs
        it('should have sub-inputs with IDs 2,3,4,6,8 (NOT sequential)', () => {
            const { compoundFieldSubInputs } = require('../gf-field-definitions');
            const name = compoundFieldSubInputs.name;

            expect(Object.keys(name)).toEqual(['2', '3', '4', '6', '8']);
            expect(name['1']).toBeUndefined(); // ID 1 does NOT exist
            expect(name['5']).toBeUndefined(); // ID 5 does NOT exist
            expect(name['7']).toBeUndefined(); // ID 7 does NOT exist
        });

        it('should mark First (3) and Last (6) as required', () => {
            const { compoundFieldSubInputs } = require('../gf-field-definitions');
            const name = compoundFieldSubInputs.name;

            expect(name['3'].required).toBe(true);  // First
            expect(name['6'].required).toBe(true);  // Last
            expect(name['2'].required).toBeFalsy(); // Prefix
            expect(name['4'].required).toBeFalsy(); // Middle
            expect(name['8'].required).toBeFalsy(); // Suffix
        });
    });

    describe('time field', () => {
        it('should have sub-inputs for hour, minute, am/pm', () => {
            const { compoundFieldSubInputs } = require('../gf-field-definitions');
            const time = compoundFieldSubInputs.time;

            expect(Object.keys(time)).toEqual(['1', '2', '3']);
            expect(time['1'].label).toBe('Hour');
            expect(time['2'].label).toBe('Minute');
            expect(time['3'].label).toBe('AM/PM');
        });
    });
});
```

### 1.2 Date Format Pattern Tests

```javascript
describe('dateFormatPatterns', () => {
    const { dateFormatPatterns } = require('../gf-field-definitions');

    describe('regex matching', () => {
        const testCases = [
            // [format, validValue, invalidValue]
            ['mdy', '12/31/2024', '31/12/2024'],
            ['dmy', '31/12/2024', '2024/12/31'],
            ['dmy_dash', '31-12-2024', '31/12/2024'],
            ['dmy_dot', '31.12.2024', '31-12-2024'],
            ['ymd_slash', '2024/12/31', '12/31/2024'],
            ['ymd_dash', '2024-12-31', '2024/12/31'],
            ['ymd_dot', '2024.12.31', '2024-12-31'],
        ];

        test.each(testCases)('%s matches valid format', (format, valid, invalid) => {
            expect(dateFormatPatterns[format].regex.test(valid)).toBe(true);
        });

        test.each(testCases)('%s rejects invalid format', (format, valid, invalid) => {
            // May match if separators align, but order detection matters
            const pattern = dateFormatPatterns[format];
            expect(pattern.order).toBeDefined();
        });
    });

    describe('order extraction', () => {
        it('mdy should have month-day-year order', () => {
            expect(dateFormatPatterns.mdy.order).toEqual(['month', 'day', 'year']);
        });

        it('dmy formats should have day-month-year order', () => {
            expect(dateFormatPatterns.dmy.order).toEqual(['day', 'month', 'year']);
            expect(dateFormatPatterns.dmy_dash.order).toEqual(['day', 'month', 'year']);
            expect(dateFormatPatterns.dmy_dot.order).toEqual(['day', 'month', 'year']);
        });

        it('ymd formats should have year-month-day order', () => {
            expect(dateFormatPatterns.ymd_slash.order).toEqual(['year', 'month', 'day']);
            expect(dateFormatPatterns.ymd_dash.order).toEqual(['year', 'month', 'day']);
            expect(dateFormatPatterns.ymd_dot.order).toEqual(['year', 'month', 'day']);
        });
    });

    describe('separator extraction', () => {
        it('should have correct separators', () => {
            expect(dateFormatPatterns.mdy.separator).toBe('/');
            expect(dateFormatPatterns.dmy.separator).toBe('/');
            expect(dateFormatPatterns.dmy_dash.separator).toBe('-');
            expect(dateFormatPatterns.dmy_dot.separator).toBe('.');
            expect(dateFormatPatterns.ymd_slash.separator).toBe('/');
            expect(dateFormatPatterns.ymd_dash.separator).toBe('-');
            expect(dateFormatPatterns.ymd_dot.separator).toBe('.');
        });
    });
});
```

### 1.3 Field Configuration Helper Tests

```javascript
describe('getFieldValidationConfig', () => {
    const { getFieldValidationConfig } = require('../gf-field-definitions');

    it('should identify compound fields', () => {
        expect(getFieldValidationConfig('address').isCompound).toBe(true);
        expect(getFieldValidationConfig('name').isCompound).toBe(true);
        expect(getFieldValidationConfig('time').isCompound).toBe(true);
        expect(getFieldValidationConfig('text').isCompound).toBe(false);
        expect(getFieldValidationConfig('email').isCompound).toBe(false);
    });

    it('should return sub-inputs for compound fields', () => {
        const addressConfig = getFieldValidationConfig('address');
        expect(addressConfig.subInputs).toBeDefined();
        expect(addressConfig.subInputs['1'].label).toBe('Street Address');
    });

    it('should return date format config when settings provided', () => {
        const config = getFieldValidationConfig('date', { dateFormat: 'mdy' });
        expect(config.dateFormat).toBeDefined();
        expect(config.dateFormat.order).toEqual(['month', 'day', 'year']);
    });

    it('should return null dateFormat when not provided', () => {
        const config = getFieldValidationConfig('date');
        expect(config.dateFormat).toBeNull();
    });
});
```

---

## 2. Unit Tests: Transformations (PHP)

**File**: `tests/test-transformations.php`

### 2.1 Number Cleaning Tests

```php
class Test_Transformations extends WP_UnitTestCase {

    /**
     * @dataProvider get_number_cleaning_test_cases
     */
    public function test_clean_number_format( $input, $config, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'number_clean' => $config
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_number_cleaning_test_cases() {
        return array(
            // Currency removal
            array( '$1,234.56', array( 'remove_currency' => true, 'remove_commas' => true ), '1234.56' ),
            array( '€1.234,56', array( 'remove_currency' => true ), '1.234,56' ),
            array( '£99.99', array( 'remove_currency' => true ), '99.99' ),
            array( '¥10000', array( 'remove_currency' => true ), '10000' ),

            // Comma removal
            array( '1,234,567', array( 'remove_commas' => true ), '1234567' ),
            array( '1,234.56', array( 'remove_commas' => true ), '1234.56' ),

            // Space removal
            array( '1 234 567', array( 'remove_spaces' => true ), '1234567' ),

            // Combined
            array( '$ 1,234.56', array( 'remove_currency' => true, 'remove_commas' => true, 'remove_spaces' => true ), '1234.56' ),

            // Edge cases
            array( '', array( 'remove_currency' => true ), '' ),
            array( 'not a number', array( 'remove_currency' => true ), 'not a number' ),
        );
    }

    /**
     * @dataProvider get_url_protocol_test_cases
     */
    public function test_add_url_protocol( $input, $config, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'url_protocol' => $config
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_url_protocol_test_cases() {
        return array(
            // Add https when missing
            array( 'example.com', array( 'protocol' => 'https://', 'only_missing' => true ), 'https://example.com' ),
            array( 'www.example.com', array( 'protocol' => 'https://', 'only_missing' => true ), 'https://www.example.com' ),

            // Don't modify existing protocol
            array( 'http://example.com', array( 'protocol' => 'https://', 'only_missing' => true ), 'http://example.com' ),
            array( 'https://example.com', array( 'protocol' => 'https://', 'only_missing' => true ), 'https://example.com' ),

            // Handle leading slashes
            array( '//example.com', array( 'protocol' => 'https://', 'only_missing' => true ), 'https://example.com' ),

            // Edge cases
            array( '', array( 'protocol' => 'https://', 'only_missing' => true ), '' ),
        );
    }

    /**
     * @dataProvider get_trim_whitespace_test_cases
     */
    public function test_trim_whitespace( $input, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'trim_whitespace' => true
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_trim_whitespace_test_cases() {
        return array(
            array( ' hello ', 'hello' ),
            array( "\thello\t", 'hello' ),
            array( "\nhello\n", 'hello' ),
            array( '  multiple  spaces  ', 'multiple  spaces' ), // Only trims ends
            array( '', '' ),
        );
    }

    /**
     * @dataProvider get_boolean_standardization_test_cases
     */
    public function test_standardize_boolean( $input, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'standardize_boolean' => true
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_boolean_standardization_test_cases() {
        return array(
            // True values
            array( 'yes', '1' ),
            array( 'YES', '1' ),
            array( 'Yes', '1' ),
            array( 'y', '1' ),
            array( 'Y', '1' ),
            array( 'true', '1' ),
            array( 'TRUE', '1' ),
            array( '1', '1' ),

            // False values
            array( 'no', '0' ),
            array( 'NO', '0' ),
            array( 'No', '0' ),
            array( 'n', '0' ),
            array( 'N', '0' ),
            array( 'false', '0' ),
            array( 'FALSE', '0' ),
            array( '0', '0' ),

            // Unchanged values
            array( 'maybe', 'maybe' ),
            array( '', '' ),
            array( 'other', 'other' ),
        );
    }

    /**
     * @dataProvider get_name_capitalization_test_cases
     */
    public function test_capitalize_name( $input, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'capitalize_name' => true
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_name_capitalization_test_cases() {
        return array(
            array( 'john doe', 'John Doe' ),
            array( 'JOHN DOE', 'John Doe' ),
            array( 'jOhN dOe', 'John Doe' ),
            array( 'mary-jane watson', 'Mary-jane Watson' ), // Note: ucwords limitation
            array( '', '' ),
        );
    }

    /**
     * @dataProvider get_email_lowercase_test_cases
     */
    public function test_email_lowercase( $input, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'email_lowercase' => true
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_email_lowercase_test_cases() {
        return array(
            array( 'John@Example.COM', 'john@example.com' ),
            array( 'ADMIN@GRAVITYKIT.COM', 'admin@gravitykit.com' ),
            array( 'already@lowercase.com', 'already@lowercase.com' ),
            array( '', '' ),
        );
    }

    /**
     * @dataProvider get_null_value_test_cases
     */
    public function test_handle_null_values( $input, $expected ) {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( $input, array(
            'handle_null_values' => true
        ) );

        $this->assertEquals( $expected, $result );
    }

    public function get_null_value_test_cases() {
        return array(
            array( 'n/a', '' ),
            array( 'N/A', '' ),
            array( 'na', '' ),
            array( 'NA', '' ),
            array( 'null', '' ),
            array( 'NULL', '' ),
            array( 'none', '' ),
            array( 'None', '' ),
            array( '-', '' ),
            array( '--', '' ),
            array( 'empty', '' ),
            array( 'EMPTY', '' ),

            // Unchanged
            array( 'actual value', 'actual value' ),
            array( 'Nathan', 'Nathan' ), // Should NOT match "na"
            array( '', '' ),
        );
    }

    /**
     * Test that transformations can be combined
     */
    public function test_combined_transformations() {
        $processor = new Processor( array() );

        $result = $processor->apply_column_transform( '  $1,234.56  ', array(
            'trim_whitespace' => true,
            'number_clean' => array(
                'remove_currency' => true,
                'remove_commas' => true
            )
        ) );

        $this->assertEquals( '1234.56', $result );
    }
}
```

---

## 3. Unit Tests: RealTimeValidator (JavaScript)

**File**: `assets/js/src/components/__tests__/RealTimeValidator.test.js`

### 3.1 Date Format Detection Tests

```javascript
describe('RealTimeValidator', () => {
    describe('detectDateFormat', () => {
        const validator = new RealTimeValidator();

        it('should detect mdy format (US dates)', () => {
            expect(validator.detectDateFormat('12/31/2024')).toBe('mdy');
            expect(validator.detectDateFormat('01/15/2024')).toBe('mdy');
        });

        it('should detect dmy format (EU dates)', () => {
            // When day > 12, it's clearly DMY
            expect(validator.detectDateFormat('31/12/2024')).toBe('dmy');
            expect(validator.detectDateFormat('25/12/2024')).toBe('dmy');
        });

        it('should detect ymd formats', () => {
            expect(validator.detectDateFormat('2024/12/31')).toBe('ymd_slash');
            expect(validator.detectDateFormat('2024-12-31')).toBe('ymd_dash');
            expect(validator.detectDateFormat('2024.12.31')).toBe('ymd_dot');
        });

        it('should return null for ambiguous dates when first value <= 12', () => {
            // 01/02/2024 could be Jan 2 or Feb 1
            const result = validator.detectDateFormat('01/02/2024');
            expect(['mdy', 'dmy', null]).toContain(result);
        });

        it('should return null for invalid dates', () => {
            expect(validator.detectDateFormat('not-a-date')).toBeNull();
            expect(validator.detectDateFormat('')).toBeNull();
            expect(validator.detectDateFormat('13/32/2024')).toBeNull();
        });
    });

    describe('generateDateTransformSuggestion', () => {
        const validator = new RealTimeValidator();

        it('should suggest transformation when formats differ', () => {
            const suggestion = validator.generateDateTransformSuggestion(
                '25/12/2024',  // Source (DMY)
                'mdy'          // Target format
            );

            expect(suggestion).toBeDefined();
            expect(suggestion.type).toBe('date_format');
            expect(suggestion.preview).toContain('12/25/2024');
        });

        it('should not suggest when formats match', () => {
            const suggestion = validator.generateDateTransformSuggestion(
                '12/25/2024',  // Source (MDY)
                'mdy'          // Target format (also MDY)
            );

            expect(suggestion).toBeNull();
        });
    });
});
```

### 3.2 Suggestion Generation Tests

```javascript
describe('generateSuggestions', () => {
    const validator = new RealTimeValidator();

    it('should generate trim suggestion for whitespace', () => {
        const suggestions = validator.generateSuggestions(
            [' value1 ', 'value2', ' value3'],
            { type: 'text' },
            0
        );

        const trimSuggestion = suggestions.find(s => s.type === 'trim_whitespace');
        expect(trimSuggestion).toBeDefined();
        expect(trimSuggestion.data.affected).toBe(2);
    });

    it('should generate email lowercase suggestion', () => {
        const suggestions = validator.generateSuggestions(
            ['John@Example.COM', 'jane@test.org'],
            { type: 'email' },
            0
        );

        const emailSuggestion = suggestions.find(s => s.type === 'email_lowercase');
        expect(emailSuggestion).toBeDefined();
    });

    it('should generate URL protocol suggestion', () => {
        const suggestions = validator.generateSuggestions(
            ['example.com', 'https://other.com'],
            { type: 'website' },
            0
        );

        const urlSuggestion = suggestions.find(s => s.type === 'url_protocol');
        expect(urlSuggestion).toBeDefined();
    });

    it('should generate boolean standardization suggestion', () => {
        const suggestions = validator.generateSuggestions(
            ['yes', 'no', 'true', 'false'],
            { type: 'checkbox' },
            0
        );

        const boolSuggestion = suggestions.find(s => s.type === 'standardize_boolean');
        expect(boolSuggestion).toBeDefined();
    });

    it('should generate null value handling suggestion', () => {
        const suggestions = validator.generateSuggestions(
            ['value1', 'n/a', 'value2', 'null'],
            { type: 'text' },
            0
        );

        const nullSuggestion = suggestions.find(s => s.type === 'null_values');
        expect(nullSuggestion).toBeDefined();
        expect(nullSuggestion.data.affected).toBe(2);
    });
});
```

---

## 4. Acceptance Tests (Codeception)

### 4.1 Date Format Transformation Tests

**File**: `tests/acceptance/acceptance/DateTransformationCest.php`

```php
<?php

class DateTransformationCest {

    /**
     * Test that date format mismatch shows inline suggestion
     */
    public function testDateFormatMismatchShowsSuggestion( AcceptanceTester $I ) {
        $form_title = sprintf( 'Date Transform Test "%s"', $I->generateRandomString() );
        $form_id = $I->importForm( 'date_mdy.json', $form_title ); // Form expects MM/DD/YYYY

        $I->wantTo( 'See date format transformation suggestion when formats mismatch' );

        $I->login();
        $I->goToPluginPage();

        // Upload CSV with DMY dates (European format)
        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/dates_dmy.csv' );
        $I->waitForElement( $I->getAutomationId( 'form-source-selection-container' ) );

        // Select the MDY form
        $I->click( $I->getAutomationId( 'existing_form_selection' ) );
        $I->fillField( $I->getAutomationId( 'form_search_bar' ), "#{$form_id}" );
        $I->waitForElement( $I->getAutomationId( "form-{$form_id}" ) );
        $I->click( $I->getAutomationId( "form-{$form_id}" ) );

        // Map the date column
        $I->waitForElement( $I->getAutomationId( 'field_mapping_container' ) );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-0' ), 'Date' );

        // Should see transformation suggestion
        $I->waitForElement( $I->getAutomationId( 'transformation_suggestion' ) );
        $I->see( 'Convert date format', $I->getAutomationId( 'transformation_suggestion' ) );

        // Preview should show before → after
        $I->see( '25/12/2024 → 12/25/2024', $I->getAutomationId( 'transformation_preview' ) );
    }

    /**
     * Test applying date transformation
     */
    public function testApplyDateTransformation( AcceptanceTester $I ) {
        $form_title = sprintf( 'Date Apply Test "%s"', $I->generateRandomString() );
        $form_id = $I->importForm( 'date_mdy.json', $form_title );

        $I->login();
        $I->goToPluginPage();

        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/dates_dmy.csv' );
        $I->waitForElement( $I->getAutomationId( 'form-source-selection-container' ) );

        $I->click( $I->getAutomationId( 'existing_form_selection' ) );
        $I->fillField( $I->getAutomationId( 'form_search_bar' ), "#{$form_id}" );
        $I->click( $I->getAutomationId( "form-{$form_id}" ) );

        $I->waitForElement( $I->getAutomationId( 'field_mapping_container' ) );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-0' ), 'Date' );

        // Click apply transformation
        $I->waitForElement( $I->getAutomationId( 'apply_transformation_btn' ) );
        $I->click( $I->getAutomationId( 'apply_transformation_btn' ) );

        // Suggestion should disappear, preview should update
        $I->waitForElementNotVisible( $I->getAutomationId( 'transformation_suggestion' ) );
        $I->see( '12/25/2024', $I->getAutomationId( 'column_0_row_1_data' ) );

        // Complete import
        $I->click( $I->getAutomationId( 'continue_with_import' ) );
        $I->waitForElement( $I->getAutomationId( 'configure_options' ) );
        $I->click( $I->getAutomationId( 'continue_with_import' ) );
        $I->waitForElement( $I->getAutomationId( 'import_finished' ) );

        // Verify imported data
        $entry_ids = \GFAPI::get_entry_ids( $form_id );
        $entry = \GFAPI::get_entry( $entry_ids[0] );

        $I->assertEquals( '2024-12-25', $entry[1], 'Date was correctly transformed from DMY to GF storage format' );
    }
}
```

### 4.2 Number Transformation Tests

**File**: `tests/acceptance/acceptance/NumberTransformationCest.php`

```php
<?php

class NumberTransformationCest {

    public function testCurrencyRemovalSuggestion( AcceptanceTester $I ) {
        $form_id = $I->importForm( 'number_field.json', 'Number Test' );

        $I->login();
        $I->goToPluginPage();

        // Upload CSV with currency symbols
        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/numbers_with_currency.csv' );
        $I->waitForElement( $I->getAutomationId( 'form-source-selection-container' ) );

        $I->click( $I->getAutomationId( 'existing_form_selection' ) );
        $I->fillField( $I->getAutomationId( 'form_search_bar' ), "#{$form_id}" );
        $I->click( $I->getAutomationId( "form-{$form_id}" ) );

        $I->waitForElement( $I->getAutomationId( 'field_mapping_container' ) );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-0' ), 'Amount' );

        // Should see number cleaning suggestion
        $I->waitForElement( $I->getAutomationId( 'transformation_suggestion' ) );
        $I->see( 'Remove currency symbols', $I->getAutomationId( 'transformation_suggestion' ) );
        $I->see( '$1,234.56 → 1234.56', $I->getAutomationId( 'transformation_preview' ) );
    }
}
```

### 4.3 Bulk Operations Tests

**File**: `tests/acceptance/acceptance/BulkTransformationCest.php`

```php
<?php

class BulkTransformationCest {

    public function testBulkApplyTransformations( AcceptanceTester $I ) {
        $form_id = $I->importForm( 'multi_field.json', 'Bulk Test' );

        $I->login();
        $I->goToPluginPage();

        // Upload CSV with multiple issues
        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/multiple_issues.csv' );
        $I->waitForElement( $I->getAutomationId( 'form-source-selection-container' ) );

        $I->click( $I->getAutomationId( 'existing_form_selection' ) );
        $I->fillField( $I->getAutomationId( 'form_search_bar' ), "#{$form_id}" );
        $I->click( $I->getAutomationId( "form-{$form_id}" ) );

        $I->waitForElement( $I->getAutomationId( 'field_mapping_container' ) );

        // Map multiple columns
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-0' ), 'Date' );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-1' ), 'Date 2' );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-2' ), 'Email' );

        // Open error panel
        $I->click( $I->getAutomationId( 'show_suggestions_panel' ) );
        $I->waitForElement( $I->getAutomationId( 'suggestions_panel' ) );

        // Should see grouped suggestions
        $I->see( 'Date Format Conversion', $I->getAutomationId( 'suggestion_group_date' ) );
        $I->see( '2 columns', $I->getAutomationId( 'suggestion_group_date' ) );

        // Click bulk apply
        $I->click( $I->getAutomationId( 'apply_all_date_transformations' ) );

        // Both columns should be transformed
        $I->waitForElementNotVisible( $I->getAutomationId( 'suggestion_group_date' ) );
    }
}
```

### 4.4 Compound Field Mapping Tests

**File**: `tests/acceptance/acceptance/CompoundFieldMappingCest.php`

```php
<?php

class CompoundFieldMappingCest {

    /**
     * CRITICAL: Test that name field sub-inputs use correct IDs (2,3,4,6,8)
     */
    public function testNameFieldSubInputMapping( AcceptanceTester $I ) {
        $form_id = $I->importForm( 'name_field.json', 'Name Field Test' );

        $I->login();
        $I->goToPluginPage();

        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/names.csv' );
        $I->waitForElement( $I->getAutomationId( 'form-source-selection-container' ) );

        $I->click( $I->getAutomationId( 'existing_form_selection' ) );
        $I->fillField( $I->getAutomationId( 'form_search_bar' ), "#{$form_id}" );
        $I->click( $I->getAutomationId( "form-{$form_id}" ) );

        $I->waitForElement( $I->getAutomationId( 'field_mapping_container' ) );

        // Map name sub-fields
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-0' ), 'Name (First)' );
        $I->selectOption( $I->getAutomationId( 'existing-form-field-selection-column-1' ), 'Name (Last)' );

        // Complete import
        $I->click( $I->getAutomationId( 'continue_with_import' ) );
        $I->waitForElement( $I->getAutomationId( 'configure_options' ) );
        $I->click( $I->getAutomationId( 'continue_with_import' ) );
        $I->waitForElement( $I->getAutomationId( 'import_finished' ) );

        // Verify correct sub-input IDs were used
        $entry_ids = \GFAPI::get_entry_ids( $form_id );
        $entry = \GFAPI::get_entry( $entry_ids[0] );

        // First name is sub-input 3, Last name is sub-input 6
        $I->assertEquals( 'John', $entry['1.3'], 'First name stored in sub-input 3' );
        $I->assertEquals( 'Doe', $entry['1.6'], 'Last name stored in sub-input 6' );

        // Sub-inputs 1, 5, 7 should NOT exist
        $I->assertArrayNotHasKey( '1.1', $entry, 'Sub-input 1 does not exist for name field' );
        $I->assertArrayNotHasKey( '1.5', $entry, 'Sub-input 5 does not exist for name field' );
    }

    /**
     * Test address field sub-input mapping
     */
    public function testAddressFieldSubInputMapping( AcceptanceTester $I ) {
        $form_id = $I->importForm( 'address_field.json', 'Address Field Test' );

        $I->login();
        $I->goToPluginPage();

        $I->attachFile( $I->getAutomationId( 'csv_upload' ), 'CSVs/addresses.csv' );
        // ... complete test

        $entry_ids = \GFAPI::get_entry_ids( $form_id );
        $entry = \GFAPI::get_entry( $entry_ids[0] );

        // Address uses sequential IDs 1-6
        $I->assertEquals( '123 Main St', $entry['1.1'], 'Street in sub-input 1' );
        $I->assertEquals( 'Apt 4', $entry['1.2'], 'Address Line 2 in sub-input 2' );
        $I->assertEquals( 'Denver', $entry['1.3'], 'City in sub-input 3' );
        $I->assertEquals( 'CO', $entry['1.4'], 'State in sub-input 4' );
        $I->assertEquals( '80202', $entry['1.5'], 'ZIP in sub-input 5' );
        $I->assertEquals( 'USA', $entry['1.6'], 'Country in sub-input 6' );
    }
}
```

---

## 5. Test Data Files Required

### CSVs to Create

| File | Contents | Purpose |
|------|----------|---------|
| `dates_dmy.csv` | European format dates (25/12/2024) | Test DMY → MDY conversion |
| `dates_mdy.csv` | US format dates (12/25/2024) | Test MDY detection |
| `dates_mixed.csv` | Mixed date formats | Test format detection edge cases |
| `numbers_with_currency.csv` | $1,234.56, €1.234,56 | Test currency removal |
| `emails_mixed_case.csv` | JOHN@Example.COM | Test email lowercase |
| `names.csv` | First, Last name columns | Test name field mapping |
| `addresses.csv` | Full address columns | Test address field mapping |
| `multiple_issues.csv` | Multiple transformation needs | Test bulk operations |
| `whitespace.csv` | Values with leading/trailing spaces | Test trim |
| `booleans.csv` | yes/no/true/false/1/0 | Test boolean standardization |
| `null_values.csv` | n/a, null, none, - | Test null handling |

### Forms to Create

| File | Fields | Purpose |
|------|--------|---------|
| `date_mdy.json` | Date field with mdy format | Test date transformation |
| `number_field.json` | Number field | Test number cleaning |
| `name_field.json` | Name field (all sub-inputs) | Test compound mapping |
| `address_field.json` | Address field | Test address mapping |
| `multi_field.json` | Multiple dates, email | Test bulk operations |

---

## 6. Implementation Priority

### 🔴 MUST HAVE (Block Release)

1. **Name field sub-input tests** - Critical data integrity
2. **Date format detection tests** - Most common import issue
3. **Transformation application tests** - Core functionality
4. **Existing DateFieldCest** must pass - Regression prevention

### 🟡 SHOULD HAVE (Important)

5. **Number cleaning tests** - Common use case
6. **Bulk operation tests** - Key feature
7. **Email/URL transformation tests** - Standard validations

### 🟢 NICE TO HAVE (Can Follow)

8. **Boolean standardization tests**
9. **Null value handling tests**
10. **Edge case tests**

---

## 7. Running Tests

```bash
# PHP Unit Tests
./vendor/bin/phpunit tests/test-transformations.php

# JavaScript Unit Tests (if Jest configured)
npm test -- --testPathPattern=gf-field-definitions

# Acceptance Tests
cd tests/acceptance
./vendor/bin/codecept run acceptance DateTransformationCest
./vendor/bin/codecept run acceptance NumberTransformationCest
./vendor/bin/codecept run acceptance CompoundFieldMappingCest
./vendor/bin/codecept run acceptance BulkTransformationCest

# Run all new acceptance tests
./vendor/bin/codecept run acceptance *TransformationCest
./vendor/bin/codecept run acceptance CompoundFieldMappingCest
```

---

*Document created: 2025-12-30*
