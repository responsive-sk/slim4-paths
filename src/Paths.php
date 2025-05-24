<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths;

/**
 * Simple paths management for Slim 4 applications
 * 
 * Provides easy access to common application paths without external dependencies.
 * Designed to be lightweight and fast.
 */
class Paths
{
    private string $basePath;
    
    /** @var array<string, string> */
    private array $paths;

    /**
     * Constructor
     * 
     * @param string $basePath Application base path (usually __DIR__ or dirname(__DIR__))
     * @param array<string, string> $customPaths Optional custom paths to override defaults
     */
    public function __construct(string $basePath, array $customPaths = [])
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->initializePaths($customPaths);
    }

    /**
     * Initialize default paths with optional custom overrides
     * 
     * @param array<string, string> $customPaths
     */
    private function initializePaths(array $customPaths): void
    {
        $defaultPaths = [
            'base' => $this->basePath,
            'config' => $this->basePath . '/config',
            'src' => $this->basePath . '/src',
            'public' => $this->basePath . '/public',
            'templates' => $this->basePath . '/templates',
            'var' => $this->basePath . '/var',
            'cache' => $this->basePath . '/var/cache',
            'logs' => $this->basePath . '/var/logs',
            'storage' => $this->basePath . '/var/storage',
            'tests' => $this->basePath . '/tests',
            'vendor' => $this->basePath . '/vendor',
            'assets' => $this->basePath . '/public/assets',
            'uploads' => $this->basePath . '/public/uploads',
        ];

        $this->paths = array_merge($defaultPaths, $customPaths);
    }

    /**
     * Get path by name
     * 
     * @param string $name Path name (e.g., 'config', 'templates', 'logs')
     * @return string Full path
     * @throws \InvalidArgumentException If path name doesn't exist
     */
    public function get(string $name): string
    {
        if (!isset($this->paths[$name])) {
            throw new \InvalidArgumentException("Path '{$name}' not found");
        }

        return $this->paths[$name];
    }

    /**
     * Create path relative to base path
     * 
     * @param string $relativePath Relative path from base
     * @return string Full path
     */
    public function path(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/\\');
    }

    /**
     * Check if path name exists
     */
    public function has(string $name): bool
    {
        return isset($this->paths[$name]);
    }

    /**
     * Get all configured paths
     * 
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->paths;
    }

    /**
     * Get base path
     */
    public function base(): string
    {
        return $this->get('base');
    }

    /**
     * Get config path with optional file
     */
    public function config(string $file = ''): string
    {
        $path = $this->get('config');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get templates path with optional file
     */
    public function templates(string $file = ''): string
    {
        $path = $this->get('templates');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get public path with optional file
     */
    public function public(string $file = ''): string
    {
        $path = $this->get('public');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get logs path with optional file
     */
    public function logs(string $file = ''): string
    {
        $path = $this->get('logs');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get storage path with optional file
     */
    public function storage(string $file = ''): string
    {
        $path = $this->get('storage');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get cache path with optional file
     */
    public function cache(string $file = ''): string
    {
        $path = $this->get('cache');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get assets path with optional file
     */
    public function assets(string $file = ''): string
    {
        $path = $this->get('assets');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }

    /**
     * Get uploads path with optional file
     */
    public function uploads(string $file = ''): string
    {
        $path = $this->get('uploads');
        return $file ? $path . '/' . ltrim($file, '/\\') : $path;
    }
}
