# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Theme Overview

This is a modern WordPress theme built on Sage 11 framework with:

- **Laravel Blade** templating system
- **Tailwind CSS 4** for styling with custom design system
- **Vite** for fast development and HMR (Hot Module Replacement)
- **Acorn** (Laravel for WordPress) for advanced PHP features
- **Custom blocks** using ACF Composer via `log1x/acf-composer`

## Development Commands

### Start Development

```bash
# Start development server with HMR
npm run dev

# Build for production
npm run build
```

### Code Quality & Testing

```bash
# Install dependencies
composer install && npm install

# Check PHP code quality (uses Laravel Pint)
composer pint

# Translation tools (WordPress i18n)
npm run translate:pot     # Generate .pot file
npm run translate:update  # Update .po files
npm run translate:compile # Compile translations
```

### Acorn Commands

**Important**: WP-CLI commands must be run from WordPress root directory (`/c/Users/abc/Local Sites/appandsite/app/public`) and use full path to wp-cli.phar.

```bash
# Navigate to WordPress root
cd "/c/Users/abc/Local Sites/appandsite/app/public"

# Clear ACF Composer cache (after creating/modifying ACF fields)
php /c/wp-cli/wp-cli.phar acorn acf:clear

# Create new ACF Composer field groups
php /c/wp-cli/wp-cli.phar acorn make:field MyFieldGroup

# Create new ACF Composer blocks
php /c/wp-cli/wp-cli.phar acorn make:block MyBlock

# List all available Acorn commands
php /c/wp-cli/wp-cli.phar acorn list

# Cache optimization commands
php /c/wp-cli/wp-cli.phar acorn optimize
php /c/wp-cli/wp-cli.phar acorn config:cache
```

**Note:** ACF Composer creates field groups in code (`app/Fields/`) rather than the ACF UI. These won't appear in the WordPress admin ACF interface but are registered programmatically and version-controlled.

## Architecture

### Directory Structure

```
appandsite/
├── app/                   # PHP application code
│   ├── Blocks/            # PHP-based custom blocks (ACF Composer)
│   ├── Providers/         # Service providers
│   ├── View/
│   │   └── Composers/     # Blade view composers
│   ├── setup.php          # Theme setup and WordPress hooks
│   └── filters.php        # WordPress filters
├── resources/
│   ├── css/               # Tailwind CSS styles
│   ├── fonts/             # Source fonts
│   ├── images/            # Source images
│   ├── js/                # JavaScript components
│   └── views/             # Blade templates
│       ├── layouts/       # Base layouts
│       ├── partials/      # Reusable template parts
│       ├── components/    # Custom Blade components
│       ├── sections/      # Major sections (header, footer)
│       └── blocks/        # Block templates
├── config/                # Configuration files
└── public/build/          # Built assets (auto-generated)
```

### Asset Pipeline

- **Entry Points**:
    - `resources/css/app.css` - Theme styles
    - `resources/js/app.js` - Theme JavaScript
    - `resources/css/editor.css` - Block editor styles
    - `resources/js/editor.js` - Block editor JavaScript
- **Build Output**: `public/build/` directory
- **Development**: Uses Vite dev server with HMR on `npm run dev`
- **Path Aliases**: `@scripts`, `@styles`, `@fonts`, `@images` map to respective `resources/` directories

## Block Development

### Block Types

#### 1. PHP Blocks (ACF Composer)

Located in `app/Blocks/` for server-side rendered blocks with ACF fields.

**IMPORTANT**: All ACF blocks must include the `$blockVersion` property to use ACF Block API version 3:

```php
/**
 * The internal ACF block version.
 *
 * @var int
 */
public $blockVersion = 3;
```

This property should be added to every block class in `app/Blocks/` to ensure blocks use the latest ACF Block API version 3 instead of the older version 2.

### Block Registration

Blocks are registered through ACF Composer. The `ThemeServiceProvider` extends Sage's service provider for automatic registration.

## Code Standards

### Language Guidelines

- **Code & Comments**: All PHP, JavaScript, CSS code and comments must be written in **English**
- **WordPress Admin Content**: All user-facing content in WordPress admin (ACF field labels, instructions, descriptions, block titles, etc.) must be written in **Polish**
- **Frontend Content**: Content displayed on the frontend should follow WordPress i18n best practices for translation

### PHP

- **PSR-4 Autoloading**: `App\` namespace maps to `app/` directory
- **Laravel Conventions**: Use Laravel-style service providers, composers, etc.
- **Code Quality**: Use Laravel Pint for formatting: `composer pint`

### CSS/Styling

- **Tailwind CSS 4** with custom design system
- **Theme Colors**: Defined in `tailwind.config.js`
- **Typography**: Open Sans, Menlo, Montserrat fonts
- **Responsive**: Mobile-first approach with Tailwind breakpoints

### JavaScript

- **ES6+ Modules**: Modern JavaScript with Vite
- **Object-Oriented Approach**: Use ES6 classes for component logic
- **Interactive Elements**: Add `js-` prefixed classes to all interactive elements (buttons, toggles, etc.)
- **File Structure**: Create separate `.js` files in `resources/js/` for each component/functionality
- **Naming Convention**: Use PascalCase for class names, camelCase for methods and variables

**Example:**

```javascript
// resources/js/EmployeesList.js
class EmployeesList {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Use js- prefixed classes for selectors
        document.querySelectorAll('.js-office-toggle').forEach((element) => {
            element.addEventListener('click', this.handleToggle.bind(this));
        });
    }
}

export default EmployeesList;
```

**HTML Convention:**

```html
<!-- Good - uses js- prefix for JavaScript hooks -->
<button class="js-office-toggle btn btn-primary" data-office-id="123">Toggle Office</button>

<!-- Bad - mixing styling and JS selection -->
<button class="btn btn-primary" onclick="toggle()">Toggle Office</button>
```

## Common Tasks

### Adding New Custom Block

1. Create block: `wp acorn make:block ExampleBlock`
2. Develop in `app/Blocks/` and `resources/views/blocks/`
3. Clear ACF cache: `wp acorn acf:clear`
4. Test in block editor

### Creating ACF Fields

1. Create field group: `wp acorn make:field MyFieldGroup`
2. Develop in `app/Fields/`
3. **IMPORTANT**: Always add Polish title to field groups using `setGroupConfig()`:
    ```php
    $fields = Builder::make('field_group_name')
        ->setGroupConfig('title', 'Polski Tytuł Grupy');
    ```
4. Clear caches: `wp acorn acf:clear` and `wp acorn optimize:clear`
5. Test in WordPress admin

### Modifying Styles

1. Edit `resources/css/app.css` for theme styles
2. Use Tailwind classes and custom CSS variables
3. Run `npm run dev` for HMR during development
4. Build with `npm run build` for production

### Creating Blade Templates

1. Add templates in `resources/views/`
2. Use Laravel Blade syntax with WordPress functions
3. Follow existing naming conventions
4. Utilize view composers in `app/View/Composers/` for data

## WordPress Integration

### Theme Features

- SVG upload support
- Custom image sizes
- Block editor styles injection
- Navigation menu support

### Performance

- Asset optimization via Vite
- Font optimization
- Image optimization
- Responsive images

### Image Handling Best Practices

**Always use WordPress image functions instead of manual HTML:**

- Use `wp_get_attachment_image()` for displaying images
- Use `get_the_post_thumbnail()` for featured images
- WordPress automatically adds `loading="lazy"` since version 5.5 - DO NOT add it manually
- WordPress handles responsive images (srcset, sizes) automatically
- WordPress manages image optimization and caching

**Example:**

```php
// Good - WordPress handles lazy loading automatically
wp_get_attachment_image($image_id, 'medium', false, [
    'class' => 'custom-class'
])

// Bad - redundant lazy loading
wp_get_attachment_image($image_id, 'medium', false, [
    'class' => 'custom-class',
    'loading' => 'lazy'  // Don't do this!
])
```

## Development Notes

### Local Development

- Use `npm run dev` for development with HMR
- Supports Lando/Docker environments
- Environment-specific configuration via `.env`

### Environment Configuration

The theme uses `.env` file for environment-specific configuration:

- **`.env`** - Contains actual configuration (mail credentials, app URL, etc.)
    - Ignored by Git (listed in `.gitignore`)
    - Protected from web access via `.htaccess`
    - Should contain real credentials for local/staging/production environments

- **`.env.example`** - Template file with example values
    - Committed to Git as a reference
    - Protected from web access via `.htaccess` (all files starting with `.` are blocked)
    - Use this as a starting point for new projects

**Security**: All files starting with `.` (dot) are automatically blocked from web access by `.htaccess` rules, including `.env`, `.env.example`, `.gitignore`, etc.

### Deployment

- Run `npm run build` before deploying
- Built assets committed to version control
- Production-optimized bundles

### Debugging

- Enable WP_DEBUG for development
- Use Laravel debugging features via Acorn
- Browser dev tools for frontend debugging

## Dependencies

Key dependencies include:

- **ACF Composer** (`log1x/acf-composer`) for programmatic ACF field management
- **Sage SVG** (`log1x/sage-svg`) for SVG icon support
- **Acorn packages** for Laravel integration with WordPress
- **Tailwind CSS 4** with Vite integration
- **AOS** (Animate On Scroll) and **Headroom.js** for animations

## File Conventions

### Naming

- **PHP Classes**: PascalCase (e.g., `HeroBlock.php`)
- **Blade Templates**: kebab-case (e.g., `hero-block.blade.php`)
- **CSS/JS Files**: kebab-case (e.g., `hero-block-style.js`)

### Organization

- Keep related files together
- Follow Sage 11 conventions
- Use appropriate directories for file types
- Maintain consistent structure across components

## Documentation Synchronization

**IMPORTANT**: When modifying any functionality described in this CLAUDE.md file, you MUST:

1. **Verify consistency** - Check if changes align with current documentation
2. **Update documentation** - If functionality changes, update the relevant sections in CLAUDE.md to reflect new behavior
3. **Maintain accuracy** - Ensure CLAUDE.md always represents the current state of the codebase

This applies to changes in:

- Custom Post Types (CPT) configuration and fields
- Development commands and workflows
- File structure and architecture
- Code standards and conventions
- Theme features and integrations
- Security and configuration practices

The documentation should always be the single source of truth for project understanding.

## Custom Post Types

### Blog CPT

A blog post type with Polish admin labels for use as a starter example:

**Configuration** (`config/post-types.php`):

- Post type: `blog`
- Slug: `blog`
- Supports: title, editor, author, revisions, thumbnail, excerpt
- Taxonomy: `blog_category` (radio select)
- All admin labels in Polish

**Usage:**

- Use as a reference for creating additional custom post types
- Modify or remove based on project needs
- Follow the pattern for proper Polish translations in WordPress admin
