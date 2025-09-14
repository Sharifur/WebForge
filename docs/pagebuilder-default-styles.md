# Page Builder Default Styles

## Overview

The Page Builder Default Styles system provides consistent typography and spacing for common HTML elements across the website and page builder editor. This ensures a professional and cohesive appearance for all content.

## File Location

- **CSS File**: `public/css/pagebuilder-defaults.css`
- **Integration**: Automatically loaded in both frontend and admin layouts

## Coverage

### Typography Elements
- **Headings**: `h1`, `h2`, `h3`, `h4`, `h5`, `h6` with responsive scaling
- **Paragraphs**: `p` with proper line height and spacing
- **Lists**: `ul`, `ol`, `li` with nested list support
- **Text Formatting**: `strong`, `em`, `u`, `mark`, `small`
- **Code**: `code`, `pre` with syntax highlighting support

### Interactive Elements
- **Links**: `a` with hover states and focus accessibility
- **Forms**: `input`, `textarea`, `select`, `label`, `button`
- **Tables**: `table`, `th`, `td` with hover states

### Content Elements
- **Blockquotes**: Styled quotes with left border
- **Images**: Responsive with figure/figcaption support
- **Horizontal Rules**: Clean divider styling

## CSS Architecture

### Scoping
All styles are scoped to `.page-builder-content` to prevent conflicts with admin UI and other page elements:

```css
.page-builder-content h1 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
}
```

### Responsive Design
- Mobile-first approach with tablet and desktop breakpoints
- Responsive typography scaling
- Print media styles included

### Utility Classes
Ready-to-use classes for common styling needs:

```css
.text-center, .text-left, .text-right, .text-justify
.font-light, .font-normal, .font-medium, .font-semibold, .font-bold
.text-sm, .text-base, .text-lg, .text-xl, .text-2xl
.mb-0 through .mb-8, .mt-0 through .mt-8
```

## Integration Points

### Frontend Pages
- Loaded in: `resources/views/frontend/layout.blade.php`
- Applied to: All page builder content via `.page-builder-content` wrapper

### Admin Panel
- Loaded in: `resources/views/admin/layouts/admin.blade.php`
- Applied to: Page builder editor preview and content

### Widget Templates
Widgets automatically inherit these styles when rendered within the `.page-builder-content` container.

## Usage Examples

### Basic HTML Elements
```html
<div class="page-builder-content">
    <h1>Main Title</h1>
    <p>This paragraph will have proper spacing and typography.</p>
    <ul>
        <li>List items have consistent styling</li>
        <li>With proper line height and spacing</li>
    </ul>
</div>
```

### Custom Widget Development
When creating widgets, ensure your HTML is wrapped in the page builder content container:

```php
// In widget render method
return '<div class="page-builder-content">' . $content . '</div>';
```

### Utility Classes
```html
<div class="page-builder-content">
    <h2 class="text-center font-bold">Centered Bold Heading</h2>
    <p class="text-lg mb-8">Large text with extra bottom margin</p>
</div>
```

## Customization

### Override Styles
To customize default styles, you can:

1. **Add page-specific styles** via `@section('styles')` in individual pages
2. **Modify the CSS file** directly (not recommended for updates)
3. **Add custom classes** in your widget templates

### Color Scheme
The default color scheme uses neutral grays and blue accents:
- Primary text: `#333333`
- Headings: `#1f2937`
- Links: `#3b82f6` (hover: `#1d4ed8`)
- Muted text: `#6b7280`

### Typography Scale
Default font sizes (responsive):
- h1: 2.5rem (mobile: 2rem)
- h2: 2rem (mobile: 1.75rem)
- h3: 1.75rem (mobile: 1.5rem)
- h4: 1.5rem (mobile: 1.25rem)
- h5: 1.25rem (mobile: 1.125rem)
- h6: 1.125rem (mobile: 1rem)

## Performance Notes

- **File Size**: ~15KB uncompressed
- **Loading**: Loaded once per page, cached by browser
- **Specificity**: Low specificity to allow easy overrides
- **Print Styles**: Included for PDF generation and printing

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with graceful degradation)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Maintenance

When updating default styles:
1. Test across different widgets and content types
2. Verify responsive behavior on all breakpoints
3. Check print styles for PDF exports
4. Ensure accessibility standards are maintained
5. Clear browser cache after changes

## Related Files

- `plugins/Pagebuilder/Helpers/FrontendRenderer.php` - Applies `.page-builder-content` wrapper
- `resources/views/frontend/layout.blade.php` - Frontend CSS loading
- `resources/views/admin/layouts/admin.blade.php` - Admin CSS loading
- `resources/views/widgets/*.blade.php` - Widget templates that benefit from these styles