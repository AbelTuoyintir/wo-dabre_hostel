<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('image_url')) {
    function image_url(?string $path, ?string $default = null): string
    {
        if (empty($path)) {
            return $default ?? '';
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If a physical public/image.php exists (common on shared hosts), prefer it
        // to avoid webserver rewrite rules that block /storage/* URLs.
        if (file_exists(public_path('image.php'))) {
            return url('image.php') . '?path=' . urlencode(ltrim($path, '/'));
        }

        return route('image.proxy', ['path' => ltrim($path, '/')]);
    }
}
