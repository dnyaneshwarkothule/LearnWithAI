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
    protected $returnType = 'array';
}
