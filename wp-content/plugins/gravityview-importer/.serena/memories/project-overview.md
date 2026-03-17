# GravityImport Project Overview

## Purpose
GravityImport is a WordPress plugin that enables importing CSV data into Gravity Forms entries. It provides a step-by-step wizard UI for mapping CSV columns to form fields, with support for data transformations and validation.

## Tech Stack

### Backend (PHP)
- PHP 7.2+
- WordPress plugin architecture
- PSR-4 autoloading (`GravityKit\GravityImport\` → `src/`)
- Strauss for vendor prefixing (vendor_prefixed/)
- GravityKit Foundation framework

### Frontend (JavaScript/React)
- React with JSX components
- Webpack 4 for bundling
- Bulma CSS framework (0.8.0)
- dayjs for date handling
- react-dropzone for file uploads
- react-router-dom for navigation

## Key Dependencies
- gravitykit/foundation (shared framework)
- gravitykit/goodby-csv (CSV parsing)
- lucatume/wp-browser (testing)

## Directory Structure
```
src/                 # PHP classes (PSR-4)
  ├── Processor.php  # Core CSV processing logic
  ├── UI.php         # Admin UI and AJAX handlers
  ├── Batch.php      # Import batch management (CPT)
  └── Schema.php     # Field schema definitions

assets/
  └── js/src/        # React components
      └── components/
          ├── step1/  # Upload step
          ├── step2/  # Form selection
          ├── step3/  # Field mapping
          └── step4/  # Import execution

tests/              # PHPUnit tests
vendor_prefixed/    # Strauss-prefixed dependencies
translations/       # i18n files
claudedocs/        # Architecture documentation
```

## Import Workflow
1. **Upload**: CSV file uploaded via AJAX → stored in uploads/
2. **Batch Creation**: CPT record (`gv_importentry_batch`) created with metadata
3. **CSV Parsing**: Processor extracts columns + 50-row excerpt for preview
4. **Field Mapping**: React UI maps CSV columns → GF field IDs
5. **Import**: Server-side processing row-by-row with transformations
