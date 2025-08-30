<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ExamQuestionModel;

class ExamResultModel extends Model
{
    protected $table      = 'exam_results';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'exam_id', 'score', 'answers',
        'total', 'attempted', 'correct', 'wrong'
    ];

    /**
     * Evaluate answers, save result, return summary
     */
    public function evaluateAndSaveResult(int $userId, int $examId, array $answers, int $total, int $attempted): array
    {
        $questionModel = new ExamQuestionModel();
        $correctMap = $questionModel->getCorrectAnswers($examId);

        $correct = 0;
        $wrong   = 0;

        foreach ($answers as $ans) {
            $qid = $ans['question_id'];
            $userAns = strtoupper($ans['answer']);
            if (isset($correctMap[$qid])) {
                if ($correctMap[$qid] === $userAns) {
                    $correct++;
                } else {
                    $wrong++;
                }
            }
        }

        $score = $correct;

        // Save result
        $this->insert([
            'user_id'   => $userId,
            'exam_id'   => $examId,
            'score'     => $score,
            'answers'   => json_encode($answers),
            'total'     => $total,
            'attempted' => $attempted,
            'correct'   => $correct,
            'wrong'     => $wrong,
        ]);

        return [
            'success'   => true,
            'score'     => $score,
            'total'     => $total,
            'attempted' => $attempted,
            'correct'   => $correct,
            'wrong'     => $wrong
        ];
    }
}
