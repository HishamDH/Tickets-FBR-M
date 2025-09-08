<?php
// Deploy script for Hostinger
echo "Starting deployment...\n";

// Run composer install
exec('composer install --no-dev --optimize-autoloader 2>&1', $output, $return);
if ($return !== 0) {
    echo "Composer install failed:\n";
    echo implode("\n", $output);
    exit(1);
}

// Clear cache
exec('php artisan config:clear');
exec('php artisan cache:clear');
exec('php artisan route:clear');
exec('php artisan view:clear');

// Optimize for production
exec('php artisan config:cache');
exec('php artisan route:cache');
exec('php artisan view:cache');

echo "Deployment completed successfully!\n";
