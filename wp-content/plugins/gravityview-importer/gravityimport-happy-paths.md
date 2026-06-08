# GravityImport Happy Paths

Prioritized list of happy-path E2E test scenarios for the GravityImport plugin (v2.11.0).
Derived from [product documentation](https://docs.gravitykit.com/category/795-gravityimport), codebase analysis (`src/`, `assets/js/src/components/`), and the existing activation smoke test.

---

## Priority 1: Core Import Flows

These are the most critical, user-facing workflows that must always work.

### HP-01: Basic CSV import into an existing form

- **Role(s):** Admin
- **Preconditions:** Gravity Forms active with at least one form; GravityImport active and licensed; a valid CSV file whose columns match form fields
- **Steps:**
  1. Navigate to Forms > Import/Export > Import Entries (`/wp-admin/admin.php?page=gf_export&subview=import_entries`)
  2. Click the upload area and select a CSV file
  3. The file uploads and the wizard advances to Step 2
  4. Select "Import to an Existing Form"
  5. Click on the target form from the list
  6. On the Map Fields screen, verify columns are auto-matched to form fields
  7. Click "Continue With Import"
  8. On the Configure screen, leave defaults and click "Continue With Import"
  9. Wait for the import to complete
- **Expected outcome:** Import completes with a summary showing all rows imported successfully; entries are visible in Forms > Entries for the target form

### HP-02: Create a new form from a CSV file

- **Role(s):** Admin
- **Preconditions:** Gravity Forms active; GravityImport active and licensed; a valid CSV file
- **Steps:**
  1. Navigate to Import Entries page
  2. Upload a CSV file
  3. Select "Create a New Form"
  4. Enter a name for the new form
  5. Click "Continue With Import"
  6. On the Map Fields screen, verify field types are auto-detected from CSV data
  7. Click "Create Form and Continue With Import"
  8. On the Configure screen, leave defaults and click "Continue With Import"
  9. Wait for the import to complete
- **Expected outcome:** A new Gravity Forms form is created with fields matching the CSV columns; all entries are imported; the new form appears in Forms > Forms list

### HP-03: Update existing entries via Entry ID column

- **Role(s):** Admin
- **Preconditions:** Gravity Forms active with a form that has existing entries; GravityImport active and licensed; a CSV file containing a valid "Entry ID" column with IDs matching existing entries
- **Steps:**
  1. Navigate to Import Entries page
  2. Upload the CSV file containing the Entry ID column
  3. Select the existing form that contains the entries to update
  4. A warning banner appears indicating the CSV contains an Entry ID column and entries will be overwritten
  5. Click "Continue" to acknowledge the warning
  6. Map CSV columns to form fields (Entry ID should auto-map)
  7. Click "Continue With Import"
  8. Configure options and click "Continue With Import"
  9. Wait for the import to complete
- **Expected outcome:** Existing entries are updated with new values from the CSV; import summary shows entries updated; viewing entries confirms data was overwritten

---

## Priority 2: Import Wizard Step Interactions

### HP-04: Upload CSV via drag and drop

- **Role(s):** Admin
- **Preconditions:** GravityImport active; a valid CSV file
- **Steps:**
  1. Navigate to Import Entries page
  2. Drag a CSV file from the desktop onto the upload drop zone
  3. The file uploads and the progress indicator shows upload completing
- **Expected outcome:** CSV is accepted; wizard advances to Step 2 (Select Form) with the file parsed

### HP-05: Search and select a form by name

- **Role(s):** Admin
- **Preconditions:** Multiple Gravity Forms exist (5+); CSV uploaded
- **Steps:**
  1. After uploading a CSV, arrive at Step 2 (Select Form)
  2. Select "Import to an Existing Form"
  3. Type a partial form name in the search box
  4. The form list filters to show only matching forms
  5. Click the target form
- **Expected outcome:** Form is selected; wizard advances to Step 3 (Map Fields) showing the selected form's fields

### HP-06: Field mapping with auto-detection

- **Role(s):** Admin
- **Preconditions:** CSV uploaded; existing form selected with fields whose labels match CSV column headers
- **Steps:**
  1. Arrive at Step 3 (Map Fields)
  2. Observe that CSV columns are automatically matched to form fields based on header names
  3. Verify preview data appears below each column header
  4. Click "Continue With Import"
- **Expected outcome:** Fields are correctly auto-mapped; no manual mapping needed; wizard advances to Step 4 (Configure)

### HP-07: Skip a CSV column with "Do Not Import"

- **Role(s):** Admin
- **Preconditions:** CSV uploaded with more columns than form fields
- **Steps:**
  1. Arrive at Step 3 (Map Fields)
  2. For an unwanted CSV column, open the "Import To..." dropdown
  3. Select "Do Not Import"
  4. The column preview grays out
  5. Click "Continue With Import" and complete the import
- **Expected outcome:** The skipped column's data is not imported; other columns import normally; import summary does not include the skipped column

### HP-08: Add a new field to an existing form during import

- **Role(s):** Admin
- **Preconditions:** CSV uploaded; existing form selected; CSV contains columns that don't match any existing form field
- **Steps:**
  1. Arrive at Step 3 (Map Fields)
  2. For an unmapped CSV column, open the "Import To..." dropdown
  3. Select "Add Form Field"
  4. Enter a Field Label (e.g., "Phone Number")
  5. Select a Field Type from the dropdown (e.g., "Phone")
  6. Confirm the new field creation
  7. The new field appears in the mapping
  8. Complete the import
- **Expected outcome:** A new field is added to the existing form; the CSV column data is imported into the new field; the field appears in the form editor

### HP-09: Map multi-input fields (Name, Address)

- **Role(s):** Admin
- **Preconditions:** CSV with separate columns for sub-inputs (e.g., "First Name", "Last Name", "Street", "City", "State", "Zip"); form with Name and Address fields
- **Steps:**
  1. Arrive at Step 3 (Map Fields)
  2. Map "First Name" CSV column to the Name field's "First" sub-input
  3. Map "Last Name" CSV column to the Name field's "Last" sub-input
  4. Map address-related CSV columns to the Address field's respective sub-inputs
  5. Complete the import
- **Expected outcome:** Each sub-input receives the correct data; entries show full Name and Address with all components populated

---

## Priority 3: Configure Options

### HP-10: Enable "Continue Processing If Errors Occur"

- **Role(s):** Admin
- **Preconditions:** CSV with some invalid rows (e.g., invalid email format); form with email field; existing form selected
- **Steps:**
  1. Complete Steps 1-3 of the import wizard
  2. On the Configure screen, ensure "Continue Processing If Errors Occur" is enabled (default: on)
  3. Click "Continue With Import"
  4. Wait for import to complete
- **Expected outcome:** Import processes all rows; valid rows are imported; invalid rows are reported as errors in the summary; user can download rejected records

### HP-11: Import with "Ignore Required Fields" enabled

- **Role(s):** Admin
- **Preconditions:** Form with required fields; CSV where some rows are missing values for required fields
- **Steps:**
  1. Complete Steps 1-3
  2. On the Configure screen, verify "Ignore Required Fields" toggle is visible and enabled (default: on)
  3. Complete the import
- **Expected outcome:** Rows with missing required field data are still imported as entries; no validation errors for missing required fields

### HP-12: Import with "Skip Field Validation" enabled

- **Role(s):** Admin
- **Preconditions:** Form with validated fields (phone, URL, email); CSV with data that might not pass strict validation
- **Steps:**
  1. Complete Steps 1-3
  2. On the Configure screen, enable "Skip Field Validation"
  3. Complete the import
- **Expected outcome:** Entries are created even with data that would normally fail field validation; no validation warnings appear

### HP-13: Conditional import (filter rows by criteria)

- **Role(s):** Admin
- **Preconditions:** CSV with varied data; form selected and fields mapped
- **Steps:**
  1. Complete Steps 1-3
  2. On the Configure screen, enable "Conditional Import"
  3. Set a condition (e.g., "Import the row if Column X contains 'value'")
  4. Optionally add additional conditions with the + button
  5. Complete the import
- **Expected outcome:** Only rows matching the condition(s) are imported; non-matching rows are skipped (not counted as errors); import summary reflects the filtered count

### HP-14: Enable email notifications for imported entries

- **Role(s):** Admin
- **Preconditions:** Form with at least one active notification configured; CSV uploaded and mapped
- **Steps:**
  1. Complete Steps 1-3
  2. On the Configure screen, enable "Email Notifications"
  3. Complete the import
- **Expected outcome:** The toggle activates; import proceeds; (verification of actual email delivery is out of E2E scope, but the setting should persist through the import without errors)

### HP-15: Process feeds for imported entries

- **Role(s):** Admin
- **Preconditions:** Form with at least one feed configured (e.g., a webhook or add-on feed); CSV uploaded and mapped
- **Steps:**
  1. Complete Steps 1-3
  2. On the Configure screen, enable "Process Feeds"
  3. Feed checkboxes appear listing available feeds
  4. Check the desired feed(s)
  5. Complete the import
- **Expected outcome:** The Process Feeds section expands showing available feeds with checkboxes; checked feeds are triggered for each imported entry; import completes without errors

---

## Priority 4: Results & Post-Import Navigation

### HP-16: View import results summary

- **Role(s):** Admin
- **Preconditions:** A completed import (successful or with some errors)
- **Steps:**
  1. Complete a full import
  2. Observe the Import Results page
- **Expected outcome:** Summary shows: total rows processed, entries imported successfully, entries rejected/errored; action buttons are visible: "View Imported Records", "Modify Import Configuration", "Change Field Mapping", "Start New Import"

### HP-17: View imported records from results page

- **Role(s):** Admin
- **Preconditions:** A completed import with at least one successful entry
- **Steps:**
  1. On the Import Results page, click "View Imported Records"
- **Expected outcome:** Navigates to the Gravity Forms Entries page for the target form; imported entries are visible in the entries list

### HP-18: Start a new import from results page

- **Role(s):** Admin
- **Preconditions:** A completed import
- **Steps:**
  1. On the Import Results page, click "Start New Import"
- **Expected outcome:** Returns to Step 1 (Select Source / Upload CSV); the wizard is reset for a fresh import

### HP-19: Modify import configuration from results page

- **Role(s):** Admin
- **Preconditions:** A completed import
- **Steps:**
  1. On the Import Results page, click "Modify Import Configuration"
- **Expected outcome:** Returns to Step 4 (Configure) with the previous configuration pre-populated

### HP-20: Change field mapping from results page

- **Role(s):** Admin
- **Preconditions:** A completed import
- **Steps:**
  1. On the Import Results page, click "Change Field Mapping"
- **Expected outcome:** Returns to Step 3 (Map Fields) with the previous mappings pre-populated

---

## Priority 5: Access Points & Navigation

### HP-21: Access import via Gravity Forms Import/Export menu

- **Role(s):** Admin
- **Preconditions:** GravityImport active and licensed
- **Steps:**
  1. In WordPress admin, navigate to Forms > Import/Export
  2. Click on "Import Entries" tab
- **Expected outcome:** The GravityImport wizard loads showing Step 1 (Upload CSV)

### HP-22: Access import via Gravity Forms Entries screen

- **Role(s):** Admin
- **Preconditions:** GravityImport active; at least one form exists
- **Steps:**
  1. Navigate to Forms > select a form > Entries
  2. Look for the "Import Entries" link/button
  3. Click it
- **Expected outcome:** Navigates to the GravityImport wizard, potentially pre-selecting the current form

### HP-23: Access import via WordPress Tools > Import screen

- **Role(s):** Admin
- **Preconditions:** GravityImport active
- **Steps:**
  1. Navigate to Tools > Import in WordPress admin
  2. Find the Gravity Forms import option
  3. Click "Run Importer"
- **Expected outcome:** Navigates to the GravityImport wizard

---

## Priority 6: Import Progress & Error Handling

### HP-24: Import progress bar displays during processing

- **Role(s):** Admin
- **Preconditions:** CSV with 10+ rows; import configured and started
- **Steps:**
  1. Start an import with a multi-row CSV
  2. Observe Step 5 (Import Data) while processing
- **Expected outcome:** A progress bar is visible showing import progress; progress updates as rows are processed; progress reaches 100% and transitions to the results page

### HP-25: Download rejected records after import with errors

- **Role(s):** Admin
- **Preconditions:** A completed import that had some rejected/errored rows
- **Steps:**
  1. On the Import Results page, observe the error count
  2. Click the "view log" link
  3. Click the download link for rejected records
- **Expected outcome:** A CSV file downloads containing only the rejected rows; the log shows the reason each row was rejected

### HP-26: Resume an interrupted import

- **Role(s):** Admin
- **Preconditions:** A previously started import that was not completed (e.g., browser was closed mid-import)
- **Steps:**
  1. Navigate to the Import Entries page
  2. A dialog appears: "It appears that you never finished importing [file] that you started on [date]. Do you want to resume import or start a new import?"
  3. Click "Resume Import"
- **Expected outcome:** The import resumes from where it left off; remaining entries are processed; final results page shows all imported entries

---

## Priority 7: Special Field Type Handling

### HP-27: Import with date format configuration

- **Role(s):** Admin
- **Preconditions:** CSV with date column in a specific format (e.g., MM/DD/YYYY); form with a Date field
- **Steps:**
  1. Upload CSV and select form
  2. On Map Fields, map the date column to the Date field
  3. A date format selector appears for the mapped Date field
  4. Select the matching date format (e.g., "mm/dd/yyyy")
  5. Complete the import
- **Expected outcome:** Dates are correctly parsed according to the selected format; entries show properly formatted dates

### HP-28: Import list field data

- **Role(s):** Admin
- **Preconditions:** Form with a List field; CSV with list data (either JSON format or pipe-delimited)
- **Steps:**
  1. Upload CSV and select the form
  2. Map the list column to the List field
  3. Complete the import
- **Expected outcome:** List field data is correctly imported; entries show list rows populated with the imported data

---

## Notes

- **Page slug:** The import UI is served at `admin.php?page=gv-admin-import-entries` (redirected from the GF Import/Export subview)
- **Capabilities:** Access requires one of: `manage_options`, `gravityforms_import_entries`, or `gravityforms_edit_entries`
- **REST namespace:** `gravityview/import/v1` (used by the background processor)
- **Existing test:** `tests/E2E/tests/activation.spec.js` covers plugin activation smoke tests (4 tests)
- **Test infrastructure:** E2E setup uses `@gravitykit/e2e-bootstrap` shared package (same as GravityView reference plugin)
