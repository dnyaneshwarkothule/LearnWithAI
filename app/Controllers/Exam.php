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

        // --- Categories available ---
        $categories = ["All Exams"];
        $distinctCategories = $examModel->select('DISTINCT list_type')->findAll();

        foreach ($distinctCategories as $row) {
            if (!empty($row['list_type'])) {
                $categories[] = $row['list_type'];
            }
        }

        // --- List Types ---
        $listTypes = $examModel->select('DISTINCT list_type')->findColumn('list_type');

        $lists = [];
        foreach ($listTypes as $lt) {
            $builder = $examModel->where('list_type', $lt);

            // Apply search filter
            if (!empty($search)) {
                $builder = $builder->like('name', $search);
            }

            // Apply category filter
            if (!empty($category) && $category !== "All Exams") {
                $builder = $builder->where('type', $category);
            }

            // Limit if not viewAll
            if (! $viewAll) {
                $builder = $builder->limit(5);
            }

            $rows = $builder->find();

            // Format response records
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
