<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private $secret;
    private $ttl;
    public function __construct(
        string $secret, 
        int $ttl
    ) { 
        $this->secret = $secret; 
        $this->ttl = $ttl; 
    }

    public function generate(array $payload): string 
    {
        $now = time();
        $token = array_merge([
            'iat' => $now,
            'exp' => $now + $this->ttl
        ], $payload);
        
        return JWT::encode($token, $this->secret, 'HS256');
    }

    public function decode(string $jwt): ?array 
    {
        try {
            $decoded = (array) JWT::decode($jwt, new Key($this->secret, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}