<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = (string) config('services.greenhouse_api.key');
        $providedKey = (string) $request->header('X-API-Key');

        if ($configuredKey === '' || ! hash_equals($configuredKey, $providedKey)) {
            return response()->json(['message' => 'Неверный или отсутствующий API ключ.'], 401);
        }

        return $next($request);
    }
}
