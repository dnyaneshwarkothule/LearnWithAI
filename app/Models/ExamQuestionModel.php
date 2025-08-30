<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamQuestionModel extends Model
{
    protected $table      = 'exam_questions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'exam_id', 'question',
        'option_a', 'option_b', 'option_c', 'option_d',
        'correct_option'
    ];

    /**
     * Fetch all questions for a given exam_id
     */
    public function getQuestionsByExamId(int $examId): array
    {
        return $this->select('id, exam_id, question, option_a as a, option_b as b, option_c as c, option_d as d, correct_option')
            ->where('exam_id', $examId)
            ->findAll();
    }


    /**
     * Get all correct answers for an exam in [question_id => correct_option] format
     */
    public function getCorrectAnswers(int $examId): array
    {
        $questions = $this->select('id, correct_option')
            ->where('exam_id', $examId)
            ->findAll();

        $map = [];
        foreach ($questions as $q) {
            $map[$q['id']] = strtoupper($q['correct_option']);
        }
        return $map;
    }
}
