<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Cache Clear Command
 * 
 * Clear application cache
 * Usage: php spark cache:clear
 */
class CacheClear extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'cache:clear';
    protected $description = 'Clear all cache files';

    public function run(array $params)
    {
        $cachePath = WRITEPATH . 'cache';

        if (!is_dir($cachePath)) {
            CLI::error('Cache directory not found: ' . $cachePath);
            return;
        }

        CLI::write('Starting cache cleanup...', 'yellow');

        $deleted = 0;
        $totalSize = 0;

        // Clear cache using CodeIgniter's cache service
        $cache = \Config\Services::cache();
        
        if ($cache->clean()) {
            CLI::write('✓ Cache cleaned successfully using cache service!', 'green');
        }

        // Also manually clear cache directory
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cachePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isFile() && 
                $fileinfo->getFilename() !== 'index.html' && 
                $fileinfo->getFilename() !== '.htaccess') {
                
                $filesize = $fileinfo->getSize();
                if (unlink($fileinfo->getRealPath())) {
                    $deleted++;
                    $totalSize += $filesize;
                }
            }
        }

        CLI::newLine();
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::write('Cache Clear Summary:', 'yellow');
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::write("Files deleted: {$deleted}", 'green');
        CLI::write("Space freed: " . $this->formatBytes($totalSize), 'green');
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::newLine();
        CLI::write('✓ Cache cleanup completed!', 'green');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
