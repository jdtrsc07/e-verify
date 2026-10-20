<?php

// app/Http/Middleware/ApiKeyAuth.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
class ApiKeyAuth
{
    public function handle($request, Closure $next)
    {
        $provided = (string) $request->header('X-Api-Key');
        $valid = array_filter(explode(',', (string) config('everify.api_keys')));

        foreach ($valid as $key) {
            if (hash_equals($key, $provided)) {
                return $next($request);
            }
        }

        Log::warning('Bad API key on eVerify endpoint', ['ip' => $request->ip()]);
        abort(401);
    }
    // public function handle(Request $request, Closure $next)
    // {
    //     $provided = trim((string) $request->header('X-Api-Key', ''));
    //     $keys = array_filter(array_map('trim', explode(',', (string) config('everify.api_keys', ''))));

    //     foreach ($keys as $key) {
    //         if ($provided !== '' && hash_equals($key, $provided)) {
    //             return $next($request);
    //         }
    //     }

    //     abort(401);
    // }
}
