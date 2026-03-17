# GravityImport Task Completion Checklist

## Before Committing

### PHP Changes
- [ ] Run `php -l src/ChangedFile.php` for syntax check
- [ ] Run `./vendor/bin/phpunit` for tests
- [ ] Verify no PHP errors in WordPress debug log

### JavaScript Changes
- [ ] Run `npm run lint` for ESLint
- [ ] Run `npm run build` for production bundle
- [ ] Verify no console errors in browser
- [ ] Test in WordPress admin UI

### CSS/SCSS Changes
- [ ] Run `npm run build` to compile
- [ ] Check responsive behavior (mobile, tablet, desktop)

## Testing Requirements

### Unit Tests
- PHPUnit for PHP logic (`./vendor/bin/phpunit`)
- Test file naming: `tests/test-{classname}.php`

### Manual Testing
1. Upload CSV file
2. Select target form
3. Map fields (verify preview data)
4. Execute import
5. Verify entries in Gravity Forms

## Build Artifacts
After JavaScript/CSS changes, these files should be updated:
- `assets/js/gravityview-import-entries.js`
- `assets/js/gravityview-import-entries.js.map`
- `assets/css/gravityview-import-entries.css`

**Commit built assets with source changes.**

## Pull Request Guidelines
- Target branch: `develop` (not `main`)
- Branch naming: `feature/123-description` or `fix/123-description`
- Include test coverage for new features
- Update changelog if user-facing
