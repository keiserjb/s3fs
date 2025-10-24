# S3FS Module - Scoped Dependencies Solution

## Overview

This module uses PHP Scoper to prefix all Composer dependencies (AWS SDK, GuzzleHttp), allowing it to coexist with other Backdrop modules that use the same packages without namespace conflicts.

## The Problem We Solved

S3fsStreamWrapper **extends** `Aws\S3\StreamWrapper` (the parent class). This is different from just using a class - it requires the parent class to exist at PHP parse time.

When another module (like openai) also loaded GuzzleHttp, we got this error:
```
Fatal error: Cannot declare interface BackdropOpenAI\GuzzleHttp\Promise\PromiseInterface, 
because the name is already in use
```

The issue: Both modules tried to define the same classes in different namespaces, causing a collision.

## The Solution - Three Critical Changes

### 1. Smart Autoloader Detection

**File**: `includes/autoloader.inc`

The autoloader automatically detects which vendor to use:

```php
function s3fs_load_autoloader() {
  // Try composer_manager first (recommended for production)
  if (module_exists('composer_manager')) {
    $vendor_dir = config_get('composer_manager.settings', 'vendor_dir');
    if (file_exists($vendor_dir . '/autoload.php')) {
      require_once $vendor_dir . '/autoload.php';
      return TRUE;
    }
  }
  
  // Fall back to scoped build/ directory (standalone)
  if (file_exists($module_path . '/build/autoload.php')) {
    require_once $module_path . '/build/autoload.php';
    _s3fs_create_class_aliases(); // Critical: create aliases immediately
    return TRUE;
  }
}
```

### 2. Centralized Class Aliasing

**File**: `includes/autoloader.inc`

All class aliases created in ONE place, immediately after loading scoped vendor:

```php
function _s3fs_create_class_aliases() {
  $prefix = s3fs_get_namespace_prefix(); // 'BackdropS3FS\'
  
  $classes_to_alias = [
    'Aws\Credentials\CredentialProvider',
    'Aws\S3\S3Client',
    'Aws\S3\StreamWrapper',           // The parent class!
    'Aws\S3\Exception\S3Exception',
    'Aws\CacheInterface',
    'Aws\Sdk',
    'Aws\Api',
  ];
  
  foreach ($classes_to_alias as $class) {
    $scoped_class = $prefix . $class;
    // ⭐ CRITICAL: Use TRUE to trigger autoloading!
    if (class_exists($scoped_class, TRUE) || interface_exists($scoped_class, TRUE)) {
      class_alias($scoped_class, $class);
    }
  }
}
```

**Why TRUE is critical**:
- `class_exists($class, FALSE)` - doesn't trigger autoloading, class not loaded
- `class_exists($class, TRUE)` - triggers autoloading, class IS loaded, then we can alias it

### 3. Load Autoloader BEFORE Class Definitions

**Files**: `s3fs.module`, `s3fs.admin.inc`, `includes/S3fsStreamWrapper.inc`

Autoloader must be called at file top, BEFORE any `use` statements or `class ... extends` statements:

```php
<?php
// s3fs.module

// Load autoloader FIRST - at parse time
require_once backdrop_get_path('module', 's3fs') . '/includes/autoloader.inc';
s3fs_load_autoloader();  // This creates ALL aliases immediately

// NOW the use statements work (aliases already exist)
use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Sdk;
```

And critically in S3fsStreamWrapper.inc:

```php
<?php
// includes/S3fsStreamWrapper.inc

// Load autoloader FIRST
require_once backdrop_get_path('module', 's3fs') . '/includes/autoloader.inc';
s3fs_load_autoloader();

// NOW the parent class alias exists
use Aws\S3\StreamWrapper;

// NOW this inheritance works!
class S3fsStreamWrapper extends StreamWrapper implements BackdropStreamWrapperInterface {
  // Parent class alias was created above, so PHP can find it
}
```

## How It Works

### Scenario 1: With Composer Manager (Production - Recommended)

```
Site has both openai and s3fs modules enabled
Composer Manager is enabled
  ↓
s3fs autoloader detects composer_manager
  ↓
Loads files/vendor/autoload.php
  ↓
Gets unscoped AWS SDK classes: Aws\S3\S3Client, Aws\S3\StreamWrapper, etc.
  ↓
openai also loads same files/vendor/autoload.php
  ↓
Both modules share same unscoped classes
  ↓
No conflicts! ✅
```

### Scenario 2: Without Composer Manager (Standalone)

```
Site has both openai and s3fs modules
Composer Manager is NOT enabled
  ↓
s3fs autoloader doesn't find composer_manager
  ↓
Loads build/autoload.php (scoped with BackdropS3FS\ prefix)
  ↓
Creates aliases: BackdropS3FS\Aws\S3\S3Client → Aws\S3\S3Client
  ↓
openai autoloader also doesn't find composer_manager
  ↓
Loads its build/autoload.php (scoped with BackdropOpenAI\ prefix)
  ↓
Creates aliases: BackdropOpenAI\OpenAI\Client → OpenAI\Client
  ↓
Each module has isolated, prefixed dependencies
  ↓
No conflicts! ✅
```

## The Key Insight

**PHP's parsing order**:
1. File is read and parsed
2. `use` statements are processed
3. `class ... extends` statements are processed
4. Function bodies execute (too late!)

So aliases must be created at parse time (step 0), not in function bodies.

## Why This Works

### Without Scoping (Fails When Multiple Modules Use Same Package)
```
Module A: require 'vendor/autoload.php'  → loads GuzzleHttp\Promise\PromiseInterface
Module B: require 'vendor/autoload.php'  → tries to load GuzzleHttp\Promise\PromiseInterface
Result: ❌ Fatal error - interface already defined
```

### With Scoping (Works)
```
Module A (s3fs): loads BackdropS3FS\vendor/autoload.php  → BackdropS3FS\GuzzleHttp\...
Module B (openai): loads BackdropOpenAI\vendor/autoload.php  → BackdropOpenAI\GuzzleHttp\...
Result: ✅ No conflict - different namespaces
```

## Building Scoped Dependencies

```bash
cd modules/contrib/s3fs

# Install dependencies
composer install

# Build scoped vendor
bash build.sh

# This creates build/ directory with ~4600 prefixed files
# All classes have BackdropS3FS\ prefix
```

## Key Files

- `includes/autoloader.inc` - Smart autoloader with aliasing
- `s3fs.module` - Loads autoloader at top
- `s3fs.admin.inc` - Loads autoloader at top
- `includes/S3fsStreamWrapper.inc` - Loads autoloader before class definition
- `build/` - Scoped dependencies (not gitignored for distribution)
- `vendor/` - Dev dependencies (gitignored)
- `scoper.inc.php` - PHP Scoper configuration
- `build.sh` - Build script

## What Changed

### Before (Broken)
- Direct `use Aws\...` statements without autoloader
- No aliases created
- Parent class not found for S3fsStreamWrapper
- Conflicts with other modules using same packages

### After (Works)
- Autoloader loaded at file top
- All aliases created immediately
- Parent class alias exists before class definition parsed
- Works with other modules (each has own namespace)

## Result

✅ S3FS works with openai module
✅ Each module has isolated dependencies
✅ No namespace collisions
✅ Can use with composer_manager (preferred)
✅ Can use standalone with scoped build
✅ Parent class inheritance works
✅ Reusable pattern for any Backdrop module

## For Developers

This solution can be applied to any Backdrop module that:
- Uses Composer dependencies
- Extends vendor library classes
- Needs to coexist with other modules

See the pattern in `includes/autoloader.inc` and replicate it for your module.
