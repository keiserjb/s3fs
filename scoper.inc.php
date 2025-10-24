<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$prefix = 'BackdropS3FS';

return [
    'prefix' => $prefix,
    'exclude-files' => [],
    'exclude-namespaces' => [],
    'exclude-classes' => [],
    'exclude-functions' => [],
    'exclude-constants' => [
        '/^BACKDROP_/',
        '/^FILE_/',
        '/^LANGUAGE_/',
        '/^MENU_/',
        '/^WATCHDOG_/',
    ],
    'expose-global-constants' => false,
    'expose-global-classes' => false,
    'expose-global-functions' => false,
    'patchers' => [
        static function (string $filePath, string $prefix, string $contents): string {
            // Fix autoload_real.php to use prefixed class names in checks
            if (str_ends_with($filePath, 'autoload_real.php')) {
                // Fix the class check in loadClassLoader
                $contents = str_replace(
                    "'Composer\\Autoload\\ClassLoader'",
                    "'{$prefix}\\Composer\\Autoload\\ClassLoader'",
                    $contents
                );
                // Fix the unregister call to use the prefixed class name
                $contents = preg_replace(
                    "/\\\spl_autoload_unregister\(array\('ComposerAutoloaderInit/",
                    "\\spl_autoload_unregister(array('{$prefix}\\ComposerAutoloaderInit",
                    $contents
                );
                
                // Make file loading safer by checking if files exist before requiring
                $contents = str_replace(
                    'require $file;',
                    'if (file_exists($file)) { require $file; }',
                    $contents
                );
            }
            
            return $contents;
        },
    ],
    'finders' => [
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc', 
                'test', 
                'test_old', 
                'tests', 
                'Tests', 
                'vendor-bin', 
                'bin',
                // Exclude dev dependencies only
                'humbug',
                'fidry',
            ])
            ->in('vendor'),
    ],
];
