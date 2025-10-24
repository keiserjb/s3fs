#!/bin/bash

# Build script for S3FS Backdrop module
# This script scopes dependencies to avoid conflicts with other modules

set -e

# Check if we're in the module root
if [ ! -f "s3fs.info" ]; then
    echo "Error: This script must be run from the s3fs module root directory."
    exit 1
fi

echo "Building scoped dependencies for S3FS module..."

# Ensure all dependencies are installed
echo "Ensuring all dependencies are installed..."
composer install --quiet

# Check if vendor/bin/php-scoper exists
if [ ! -f "vendor/bin/php-scoper" ]; then
    echo "Error: php-scoper not found after composer install."
    exit 1
fi

# Remove old scoped directory
if [ -d "build" ]; then
    echo "Removing old build directory..."
    rm -rf build
fi

# Temporarily install without dev to prepare vendor for scoping
echo "Preparing production dependencies for scoping..."
composer install --no-dev --quiet

# Run php-scoper (using the cached version from composer's bin)
echo "Running php-scoper..."
if [ -f ~/.composer/vendor/bin/php-scoper ]; then
    ~/.composer/vendor/bin/php-scoper add-prefix --force
else
    # Reinstall with dev just to get php-scoper
    composer install --quiet
    php vendor/bin/php-scoper add-prefix --force
fi

# Reinstall dev dependencies (for development)
echo "Reinstalling dev dependencies..."
composer install --quiet

echo "Done! Scoped dependencies are in build/"
