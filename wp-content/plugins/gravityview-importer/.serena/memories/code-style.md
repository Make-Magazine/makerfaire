# GravityImport Code Style & Conventions

## PHP Style

### Naming
- Classes: PascalCase (`Processor`, `UI`, `Batch`)
- Methods: snake_case (`parse_csv`, `apply_column_transform`)
- Constants: UPPER_SNAKE_CASE
- Namespace: `GravityKit\GravityImport`

### Patterns
- PSR-4 autoloading
- WordPress coding standards for hooks/filters
- DocBlocks for all public methods
- Type hints where practical (PHP 7.2+)

### Example
```php
namespace GravityKit\GravityImport;

class Example {
    /**
     * Process the data.
     *
     * @param array $data Input data.
     * @return array Processed data.
     */
    public function process_data( array $data ): array {
        // WordPress-style spacing around parentheses
        return $data;
    }
}
```

## JavaScript/React Style

### Naming
- Components: PascalCase (`MapFields`, `Upload`)
- Functions: camelCase (`bootstrapFieldMapping`, `uploadCSV`)
- Constants: UPPER_SNAKE_CASE
- Files: kebab-case (`map-fields.jsx`, `enhanced-mapping-ui.jsx`)

### Patterns
- Functional components with hooks preferred
- PropTypes for component props
- ES6+ syntax (arrow functions, destructuring)
- WordPress ESLint plugin rules

### Example
```jsx
import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';

const MyComponent = ({ data, onUpdate }) => {
    const [state, setState] = useState(null);
    
    useEffect(() => {
        // Effect logic
    }, [data]);
    
    return <div>{state}</div>;
};

MyComponent.propTypes = {
    data: PropTypes.object.isRequired,
    onUpdate: PropTypes.func,
};
```

## CSS/SCSS
- Bulma framework classes preferred
- BEM naming for custom classes
- SCSS variables for colors/sizing
- Component-scoped styles

## File Organization
- One class per PHP file
- React components in dedicated files
- Group related components in subdirectories
- Tests mirror src/ structure in tests/
