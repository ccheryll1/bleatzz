<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('image_or_default')) {
    /**
     * Return a public URL for an image, or a default no-image placeholder.
     *
     * Usage in Blade: <img src="{{ image_or_default($model->image_path) }}"> 
     *
     * @param string|null $path  Path relative to the configured disk (usually 'public'), or absolute URL.
     * @param string $disk       Storage disk name used for stored images (default: 'public')
     * @return string
     */
    function image_or_default($path = null, $disk = 'public')
    {
        $default = asset('asset-web/no-image.png');

        if (empty($path)) {
            return $default;
        }

        // If it's an absolute URL, return as-is
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // If it already starts with a slash assume it's a public path
        if (Str::startsWith($path, ['/'])) {
            return url(ltrim($path, '/'));
        }

        // If stored on the configured disk (e.g. storage/app/public), return storage URL
        try {
            if (Storage::disk($disk)->exists($path)) {
                return asset('storage/' . ltrim($path, '/'));
            }
        } catch (Throwable $e) {
            // If Storage isn't available for some reason, fall back to default
        }

        return $default;
    }
}
