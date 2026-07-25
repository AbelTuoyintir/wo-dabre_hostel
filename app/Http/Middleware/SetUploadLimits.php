<?php

namespace App\Http\Middleware;

use Closure;

class SetUploadLimits
{
    public function handle($request, Closure $next)
    {
        // Apply to all routes
        ini_set('upload_max_filesize', '256M');
        ini_set('post_max_size', '256M');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '3600');
        ini_set('max_input_time', '3600');
        
        return $next($request);
    }
}