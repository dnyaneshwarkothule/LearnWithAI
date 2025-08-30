<?php

namespace App\Controllers;

use App\Models\ExamQuestionModel;
use CodeIgniter\RESTful\ResourceController;

class ExamQuestion extends ResourceController
{
    protected $modelName = ExamQuestionModel::class;
    protected $format    = 'json';

    public function index()
    {
        $examId = $this->request->getGet('exam_id');

        if (!$examId) {
            return $this->fail('Missing exam_id', 400);
        }

        $questions = $this->model->getQuestionsByExamId((int)$examId);

        return $this->respond($questions);
    }
}
