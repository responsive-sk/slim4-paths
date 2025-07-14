<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths;

/**
 * Interface for preset implementations
 */
interface PresetInterface
{
    /**
     * Get preset paths
     * 
     * @return array<string, string>
     */
    public function getPaths(): array;
}
