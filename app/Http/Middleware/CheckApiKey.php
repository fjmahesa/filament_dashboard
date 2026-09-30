<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = config('services.api_key');

        // Cek apakah header X-API-KEY cocok dengan yang di .env
        if (!$apiKey || $apiKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API Key tidak valid atau tidak ditemukan.'
            ], 401);
        }

        return $next($request);
    }
}
