<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends ResourceController
{
    private $key;

    public function __construct()
    {
        // Load secret key from .env instead of hardcoding
        $this->key = getenv('JWT_SECRET') ?: 'default_secret';
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getVar('email'))->first();

        if (! $user || ! password_verify($this->request->getVar('password'), $user->password)) {
            return $this->respond(['error' => 'Invalid email or password'], 401);
        }

        // JWT payload
        $payload = [
            "iss"   => "LearnWithAI",
            "sub"   => $user->id,
            "email" => $user->email,
            "iat"   => time(),
            "exp"   => time() + 3600
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        return $this->respond([
            "status" => "success",
            "token"  => $jwt,
            "user"   => [
                "id"    => $user->id,
                "name"  => $user->name,
                "email" => $user->email
            ]
        ]);
    }

    public function profile()
    {
        $authHeader = $this->request->getHeaderLine("Authorization");

        if (! $authHeader || ! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->respond(['error' => 'Authorization header missing'], 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

            return $this->respond([
                "status" => "success",
                "data"   => $decoded
            ]);
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], 401);
        }
    }
}
