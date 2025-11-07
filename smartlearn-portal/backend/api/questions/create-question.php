<?php
require_once '../../config/core.php';
require_once '../../models/Question.php';

class CreateQuestion extends Core {
    public function create() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only instructors and admins can create questions
        if ($user_data->role != 'instructor' && $user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->test_id) || !isset($data->question) || !isset($data->type)) {
            $this->sendError("Test ID, question and type are required");
        }

        $question = new Question($this->db);
        $question->test_id = $data->test_id;
        $question->question = $data->question;
        $question->type = $data->type;
        $question->options = $data->options ?? null;
        $question->correct_answer = $data->correct_answer ?? null;
        $question->code_snippet = $data->code_snippet ?? null;
        $question->difficulty = $data->difficulty ?? 'beginner';
        $question->points = $data->points ?? 1;

        $question_id = $question->create();
        
        if ($question_id) {
            $this->sendSuccess([
                'message' => 'Question created successfully',
                'question_id' => $question_id
            ]);
        } else {
            $this->sendError("Failed to create question");
        }
    }
}

$createQuestion = new CreateQuestion();
$createQuestion->create();
?>