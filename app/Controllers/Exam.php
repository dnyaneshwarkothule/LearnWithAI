<?php

namespace App\Controllers;

use App\Models\ExamModel;
use CodeIgniter\RESTful\ResourceController;

class Exam extends ResourceController
{
    public function index()
    {
        $examModel = new ExamModel();

        // --- Filters from query params ---
        $search   = $this->request->getGet('search') ?? '';
        $category = $this->request->getGet('category') ?? '';
        $viewAll  = filter_var($this->request->getGet('viewAll') ?? false, FILTER_VALIDATE_BOOLEAN);

        // --- Categories ---
        $categories = ["All Exams"];
        $distinctCategories = $examModel->getDistinctCategories();
        foreach ($distinctCategories as $row) {
            if (!empty($row['list_type'])) {
                $categories[] = $row['list_type'];
            }
        }

        // --- Lists by type ---
        $lists = [];
        $listTypes = $examModel->getListTypes();

        foreach ($listTypes as $lt) {
            $rows = $examModel->getExamsByListType($lt, $search, $category, $viewAll);

            // Format response
            $exams = array_map(function ($row) {
                return [
                    "id"        => $row["id"],
                    "name"      => $row["name"],
                    "rating"    => floatval($row["rating"]),
                    "reviews"   => intval($row["reviews"]),
                    "type"      => $row["type"],
                    "questions" => intval($row["questions"]),
                    "duration"  => $row["duration"],
                    "image"     => $row["image"],
                    "listType"  => $row["list_type"],
                ];
            }, $rows);

            $lists[$lt] = $exams;
        }

        return $this->respond([
            "success"    => true,
            "categories" => $categories,
            "lists"      => $lists
        ]);
    }
}
