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

        // Use the Laravel route for image proxy
        // public/image.php is available as optional fallback for shared hosts
        return route('image.proxy', ['path' => ltrim($path, '/')]);
    }
}
