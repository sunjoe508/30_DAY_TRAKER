<?php
require_once '../../config/core.php';
require_once '../../models/Question.php';

class UpdateQuestion extends Core {
    public function update() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only instructors and admins can update questions
        if ($user_data->role != 'instructor' && $user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->id)) {
            $this->sendError("Question ID required");
        }

        $question = new Question($this->db);
        $question->id = $data->id;
        $question->question = $data->question ?? null;
        $question->type = $data->type ?? null;
        $question->options = $data->options ?? null;
        $question->correct_answer = $data->correct_answer ?? null;
        $question->code_snippet = $data->code_snippet ?? null;
        $question->difficulty = $data->difficulty ?? null;
        $question->points = $data->points ?? null;

        if ($question->update()) {
            $this->sendSuccess(['message' => 'Question updated successfully']);
        } else {
            $this->sendError("Failed to update question");
        }
    }
}

$updateQuestion = new UpdateQuestion();
$updateQuestion->update();
?>