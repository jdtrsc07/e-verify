<?php

namespace App\Http\Middleware;

// app/Http/Middleware/AllowedIps.php
use Closure;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class AllowedIps
{
    public function handle($request, Closure $next)
    {
        $allowed = config('everify.allowed_ips', []);

        if (!IpUtils::checkIp($request->ip(), $allowed)) {
            Log::warning('Blocked IP on eVerify endpoint', ['ip' => $request->ip()]);
            abort(403);
        }

        return $next($request);
    }
}
