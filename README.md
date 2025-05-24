# responsive-sk/slim4-paths

Simple and lightweight paths management for Slim 4 applications.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/responsive-sk/slim4-paths.svg?style=flat-square)](https://packagist.org/packages/responsive-sk/slim4-paths)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/responsive-sk/slim4-paths.svg?style=flat-square)](https://packagist.org/packages/responsive-sk/slim4-paths)

## Features

- 🚀 **Lightweight** - No external dependencies
- ⚡ **Fast** - Simple array-based path storage
- 🎯 **Focused** - Does one thing well
- 🔧 **Flexible** - Customizable paths
- 📝 **Well documented** - Clear API and examples
- ✅ **Tested** - Comprehensive test suite

## Installation

```bash
composer require responsive-sk/slim4-paths
```

## Quick Start

```php
<?php

use ResponsiveSk\Slim4Paths\Paths;

// Create paths instance
$paths = new Paths(__DIR__);

// Use predefined paths
echo $paths->config('database.php');    // /path/to/project/config/database.php
echo $paths->templates('home.php');     // /path/to/project/templates/home.php
echo $paths->logs('app.log');           // /path/to/project/var/logs/app.log

// Use custom relative paths
echo $paths->path('custom/file.txt');   // /path/to/project/custom/file.txt

// Get all paths
$allPaths = $paths->all();
```

## Usage

### Basic Usage

```php
use ResponsiveSk\Slim4Paths\Paths;

$paths = new Paths('/path/to/your/project');

// Get specific paths
$configPath = $paths->config();                    // /path/to/your/project/config
$templateFile = $paths->templates('layout.php');   // /path/to/your/project/templates/layout.php
$logFile = $paths->logs('app.log');               // /path/to/your/project/var/logs/app.log
```

### Custom Paths

```php
$customPaths = [
    'views' => '/custom/views/path',
    'data' => '/custom/data/path'
];

$paths = new Paths('/project/root', $customPaths);

echo $paths->get('views');  // /custom/views/path
echo $paths->get('data');   // /custom/data/path
```

### Dependency Injection

```php
// In your DI container configuration
$container->set(Paths::class, function () {
    return new Paths(BASE_PATH);
});

// In your classes
class MyService
{
    public function __construct(private Paths $paths) {}
    
    public function doSomething(): void
    {
        $configFile = $this->paths->config('settings.php');
        // ...
    }
}
```

## Available Paths

| Method | Default Path | Description |
|--------|-------------|-------------|
| `base()` | `/` | Application base path |
| `config($file)` | `/config` | Configuration files |
| `templates($file)` | `/templates` | Template files |
| `public($file)` | `/public` | Public web files |
| `storage($file)` | `/var/storage` | Storage files |
| `cache($file)` | `/var/cache` | Cache files |
| `logs($file)` | `/var/logs` | Log files |
| `assets($file)` | `/public/assets` | Asset files |
| `uploads($file)` | `/public/uploads` | Upload files |

## API Reference

### Constructor

```php
public function __construct(string $basePath, array $customPaths = [])
```

### Methods

```php
// Get path by name
public function get(string $name): string

// Create relative path
public function path(string $relativePath): string

// Check if path exists
public function has(string $name): bool

// Get all paths
public function all(): array

// Convenience methods
public function base(): string
public function config(string $file = ''): string
public function templates(string $file = ''): string
// ... and more
```

## Requirements

- PHP 8.1 or higher

## Testing

```bash
composer test
```

## Code Quality

```bash
# PHPStan analysis
composer phpstan

# Code style check
composer cs

# Code style fix
composer cs-fix
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Credits

- [Responsive SK](https://github.com/responsive-sk)
- [All Contributors](../../contributors)
