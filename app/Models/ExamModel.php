<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table      = 'exams';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'rating', 'reviews', 'type',
        'questions', 'duration', 'image', 'list_type'
    ];

    /**
     * Get distinct categories (list_type)
     */
    public function getDistinctCategories(): array
    {
        return $this->select('list_type')
            ->distinct()
            ->findAll();
    }

    /**
     * Get list types (only column values)
     */
    public function getListTypes(): array
    {
        return $this->select('list_type')
            ->distinct()
            ->findColumn('list_type');
    }

    /**
     * Fetch exams by list type with optional filters
     */
    public function getExamsByListType(string $listType, string $search = "", string $category = "", bool $viewAll = false): array
    {
        $builder = $this->where('list_type', $listType);

        if (!empty($search)) {
            $builder = $builder->like('name', $search);
        }

        if (!empty($category) && $category !== "All Exams") {
            $builder = $builder->where('type', $category);
        }

        if (! $viewAll) {
            $builder = $builder->limit(5);
        }

        return $builder->find();
    }
}
