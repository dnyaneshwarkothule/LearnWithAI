<?php

namespace App\Controllers;

use App\Models\ExamResultModel;
use CodeIgniter\RESTful\ResourceController;

class ExamResult extends ResourceController
{
    protected $format = 'json';

    public function submit()
    {
        $data = $this->request->getJSON(true);

        $examId   = $data['exam_id']   ?? null;
        $answers  = $data['answers']   ?? [];
        $total    = $data['total']     ?? count($answers);
        $attempted= $data['attempted'] ?? count($answers);

        if (!$examId || empty($answers)) {
            return $this->fail('Invalid request', 400);
        }

        $user = $this->request->user; // stdClass from JwtFilter

        $userId = $user->sub ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Unauthorized');
        }

        $resultModel = new ExamResultModel();
        $result = $resultModel->evaluateAndSaveResult(
            $userId,
            $examId,
            $answers,
            $total,
            $attempted
        );

        return $this->respond($result);
    }
}
