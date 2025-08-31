<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $userModel;
    protected $profileModel;
    private $key;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->profileModel = new UserProfileModel();
        $this->key          = getenv('JWT_SECRET') ?: 'default_secret';
    }

    // 🚀 Endpoint 1: Register User
    public function register()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['full_name']) || empty($data['mobile']) || empty($data['password'])) {
            return $this->failValidationErrors('Full name, Mobile, and password are required.');
        }

        // Check if user already exists
        if ($this->userModel->getUserByMobile($data['mobile'])) {
            return $this->failResourceExists('Mobile Number already registered.');
        }

        // Register user
        $userId = $this->userModel->registerUser($data);

        if (!$userId) {
            return $this->fail('User registration failed.');
        }

        // Create empty profile
        $this->profileModel->createEmptyProfile($userId);

        return $this->respondCreated([
            'status'  => true,
            'message' => 'User registered successfully',
            'user_id' => $userId
        ]);
    }

    // 🚀 Endpoint 2: Login (JWT)
    public function login()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['mobile']) || empty($data['password'])) {
            return $this->failValidationErrors('Mobile and password are required.');
        }

        $user = $this->userModel->where('mobile', $data['mobile'])->first();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->failUnauthorized('Invalid mobile or password.');
        }

        // JWT payload
        $payload = [
            "iss"    => "LearnWithAI",
            "sub"    => $user['id'],
            "mobile" => $user['mobile'],
            "role"   => $user['role'],
            "iat"    => time(),
            "exp"    => time() + 3600 // 1 hour expiry
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        return $this->respond([
            "status"  => true,
            "message" => "Login successful",
            "token"   => $jwt,
            "user"    => [
                "id"        => $user['id'],
                "full_name" => $user['full_name'],
                "mobile"    => $user['mobile'],
                "role"      => $user['role']
            ]
        ]);
    }

    // 🚀 Endpoint 3: Update Profile
    public function updateProfile($userId = null)
    {
        if (!$userId) {
            return $this->failValidationErrors('User ID required.');
        }

        $data = $this->request->getJSON(true);

        $updated = $this->profileModel->updateProfile($userId, $data);

        if (!$updated) {
            return $this->failNotFound('Profile update failed or not found.');
        }

        return $this->respond([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'user_id' => $userId
        ]);
    }

    // 🚀 Endpoint 4: Get Profile (JWT Protected)
    public function profile()
    {
        $authHeader = $this->request->getHeaderLine("Authorization");

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->respond(['error' => 'Authorization header missing'], 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

            $profile = $this->profileModel
                ->select('user_profiles.*, users.full_name, users.mobile, users.role')
                ->join('users', 'users.id = user_profiles.user_id')
                ->where('user_profiles.user_id', $decoded->sub)
                ->first();

            if (!$profile) {
                return $this->failNotFound('Profile not found.');
            }

            return $this->respond([
                "status"  => true,
                "message" => "Profile fetched successfully",
                "profile" => $profile
            ]);
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], 401);
        }
    }
}
