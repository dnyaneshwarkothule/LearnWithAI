<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\ResponseInterface;

class JwtLib
{
    private $key;

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET') ?: 'default_secret';
    }

    public function validateRequest($request)
    {
        $authHeader = $request->getHeaderLine("Authorization");

        if (! $authHeader || ! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return [
                'valid'  => false,
                'error'  => 'Authorization header missing or invalid',
                'status' => ResponseInterface::HTTP_UNAUTHORIZED
            ];
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return [
                'valid'  => true,
                'data'   => $decoded,
                'status' => ResponseInterface::HTTP_OK
            ];
        } catch (\Exception $e) {
            return [
                'valid'  => false,
                'error'  => $e->getMessage(),
                'status' => ResponseInterface::HTTP_UNAUTHORIZED
            ];
        }
    }
}
