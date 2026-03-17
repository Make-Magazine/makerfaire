# GravityImport Development Commands

## Build Commands

### JavaScript/CSS
```bash
npm run build          # Production build (Webpack)
npm run watch          # Watch mode for development
npm run lint           # ESLint for JSX files
npm run start          # Webpack dev server
```

### PHP
```bash
composer install       # Install dependencies + run Strauss
composer dump-autoload # Regenerate autoloader
./vendor/bin/phpunit   # Run PHP tests
./vendor/bin/phpunit tests/test-processor.php --stop-on-error  # Specific test file
```

## Testing Commands

### PHPUnit
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/test-processor.php

# Run specific test method
./vendor/bin/phpunit tests/test-processor.php --filter test_method_name

# Stop on first error
./vendor/bin/phpunit --stop-on-error
```

### Lint
```bash
npm run lint           # ESLint for React/JSX
php -l src/File.php    # PHP syntax check
```

## Git Workflow
```bash
git checkout develop   # Main development branch
git checkout -b feature/123-description  # Feature branch
git push -u origin feature/123-description
```

## i18n
```bash
grunt exec:makepot     # Generate translations.pot
```

## Useful File Locations
- Main plugin file: `gravityview-importer.php`
- React entry: `assets/js/src/index.jsx`
- SCSS entry: `assets/scss/gravityview-import-entries.scss`
- PHPUnit config: `phpunit.xml`
- ESLint config: `.eslintrc`
- Webpack config: `webpack.config.js`
