<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json(['message' => 'Unauthorized - No token provided'], 401);
        }

        $data = $this->validateToken($token);

        if (!$data) {
            return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
        }

        // Find and attach user to request
        $user = User::find($data['user_id']);
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized - User not found'], 401);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    private function extractToken(Request $request)
    {
        $header = $request->header('Authorization');
        
        if (!$header) {
            return null;
        }

        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    private function validateToken($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        // Verify signature
        $secret = env('JWT_SECRET', 'secret');
        $expectedSignature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));

        if ($signature !== $expectedSignature) {
            return false;
        }

        // Decode and validate payload
        $decoded = json_decode(base64_decode($payload), true);

        if (!$decoded) {
            return false;
        }

        // Check expiration
        if (isset($decoded['exp']) && $decoded['exp'] < time()) {
            return false;
        }

        return $decoded;
    }
}
