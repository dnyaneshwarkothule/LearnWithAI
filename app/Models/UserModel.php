<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'full_name', 'mobile', 'password', 'role'
    ];
    protected $useTimestamps = true;

    /**
     * Register a new user (hashes password automatically)
     */
    public function registerUser(array $data): ?int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role']     = $data['role'] ?? 'student';

        return $this->insert($data, true); // return insert ID
    }

    /**
     * Find user by mobile (email/mobile)
     */
    public function getUserByMobile(string $mobile): ?array
    {
        return $this->where('mobile', $mobile)->first();
    }

    /**
     * Verify login credentials
     */
    public function verifyLogin(string $mobile, string $password): ?array
    {
        $user = $this->getUserByMobile($mobile);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Get all users by role
     */
    public function getUsersByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }
}
