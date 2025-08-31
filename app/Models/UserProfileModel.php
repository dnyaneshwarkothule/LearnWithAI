<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table      = 'user_profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'dob', 'class_grade', 'subject_specialization', 'extra_info'
    ];
    protected $useTimestamps = true;

    /**
     * Create empty profile for a new user
     */
    public function createEmptyProfile(int $userId): ?int
    {
        return $this->insert(['user_id' => $userId], true);
    }

    /**
     * Update profile details for a user
     */
    public function updateProfile(int $userId, array $data): bool
    {
        return $this->where('user_id', $userId)->set($data)->update();
    }

    /**
     * Get profile by user ID
     */
    public function getProfileByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Get all profiles with user info (join users)
     */
    public function getProfilesWithUsers(): array
    {
        return $this->select('user_profiles.*, users.full_name, users.mobile, users.role')
            ->join('users', 'users.id = user_profiles.user_id')
            ->findAll();
    }
}
