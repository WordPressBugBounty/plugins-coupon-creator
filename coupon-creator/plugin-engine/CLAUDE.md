# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this plugin.

## Project
Plugin Engine — A shared plugin framework embedded in Coupon Creator that provides common classes, utilities, admin fields, REST API support, and DI container functionality.

## Stack
- PHP: 7.4+
- WordPress: 5.8+
- DI Container: `lucatume/di52` (Strauss-prefixed as `Pngx\Vendor\lucatume\DI52\` in coupon-creator's vendor; plugin-engine does not bundle its own copy)
- Test Framework: Codeception (via `lucatume/wp-browser`)
- Test Runner: SLIC (Docker-based)

## Plugin Info
- Slug: `plugin-engine`
- Namespace: `Pngx\`
- Main File: `pngx-common.php`
- Main Class: `Pngx__Main`
- Version: 4.0.8
- Text Domain: `plugin-engine`

## Architecture

### Class Naming
Classes use double-underscore `__` as namespace separator (legacy pattern):
- `Pngx__Main` → `src/Pngx/Main.php`
- `Pngx__Container` → `src/Pngx/Container.php`
- `Pngx__Admin__Main` → `src/Pngx/Admin/Main.php`

PSR-4 autoloading maps `Pngx\` → `src/Pngx/`

### Embedded Framework
This is NOT a standalone plugin — it is embedded inside `coupon-creator/plugin-engine/`. It is loaded by the core Coupon Creator plugin and provides shared infrastructure to all Coupon Creator family plugins.

### Key Directories
```
plugin-engine/
├── pngx-common.php                # Bootstrap file
├── pngx-autoload.php              # Autoloader
├── src/
│   ├── Pngx/                      # Framework classes (PSR-4)
│   │   ├── Main.php               # Singleton entry point
│   │   ├── Container.php          # DI container
│   │   ├── Abstract_Plugin_Register.php  # Plugin registration base
│   │   ├── Autoloader.php         # Class autoloading
│   │   ├── Admin/                 # Admin framework
│   │   │   ├── Main.php           # Admin orchestration
│   │   │   ├── Field/             # Field type renderers
│   │   │   ├── Notices.php        # Admin notice system
│   │   │   └── Help.php           # Help tab system
│   │   ├── Ajax/                  # AJAX handlers
│   │   ├── Blocks/                # Gutenberg block base classes
│   │   ├── Documentation/         # Swagger/API docs
│   │   ├── Duplicate/             # Post duplication
│   │   ├── Field/                 # Front-end field renderers
│   │   ├── REST/                  # REST API framework
│   │   ├── Repository/            # Data repository pattern
│   │   ├── Service_Providers/     # DI service providers
│   │   ├── Utils/                 # String, Array, Path helpers
│   │   ├── Utilities/             # Additional utility classes
│   │   └── Validator/             # Input validation
│   ├── functions/                 # Template tags, utilities
│   ├── views/                     # Front-end view templates
│   └── admin-views/               # Admin view templates
└── vendor/                        # Composer dependencies
```

### Core Responsibilities
- **DI Container**: `Pngx__Container` wraps `\Pngx\Vendor\lucatume\DI52\Container` for dependency injection. The prefixed di52 classes are provided by coupon-creator's `vendor/prefixed/` — plugin-engine does not bundle its own copy.
- **Plugin Registration**: `Pngx__Abstract_Plugin_Register` — base class for registering plugins
- **Admin Fields**: Full field rendering system (checkbox, color, date, dropdown, image, etc.)
- **REST API**: Headers, endpoints, post repository, system info
- **Utilities**: Array helpers, string manipulation, path resolution, caching
- **Blocks**: Abstract block registration for Gutenberg
- **Process Handling**: Background process handler and tester

### Singleton Pattern
Main class uses singleton: `Pngx__Main::instance()`
Container access via `pngx()` helper function.

## Field Types Available
Checkbox, Color, Date, Dropdown, Heading, Help, Hidden, Icon, Image, License, License_Status, List, Message, Number, Post_ID, Radio, Select, Template, Url

## Consumers
All Coupon Creator family plugins depend on this framework:
- `coupon-creator` — core plugin (parent directory)
- `coupon-creator-pro` — pro features
- `coupon-creator-add-ons` — add-on features

## Important Notes
- Changes here affect ALL plugins in the Coupon Creator family
- The autoloader (`pngx-autoload.php`) handles class loading before Composer
- Version compatibility is checked by consumer plugins via `MIN_PNGX_VERSION`
- `pngx_engine_loaded` action fires when the engine is ready for consumers

## Testing

### Running Tests
```bash
slic use coupon-creator
slic run tests              # all suites (runs from parent plugin)
slic run tests/unit         # unit only
```

### Conventions
- Unit: extend `Codeception\Test\Unit`, `_before()` for setup
- WPUnit: extend `Codeception\TestCase\WPTestCase`, `setUp()`/`parent::setUp()`
- Namespace tests: `Pngx\Tests\{Unit,WPUnit}`

## Verification
- Run `slic run tests/unit` after pure PHP changes
- Test from the parent `coupon-creator` plugin context
- Verify no breaking changes for pro and add-ons plugins

## Learnings
<!-- Add patterns from PR reviews here. -->
