<?php
require_once '../../config/core.php';
require_once '../../models/Question.php';

class DeleteQuestion extends Core {
    public function delete() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can delete questions
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $question_id = $_GET['id'] ?? null;
        if (!$question_id) {
            $this->sendError("Question ID required");
        }

        $question = new Question($this->db);
        $question->id = $question_id;

        if ($question->delete()) {
            $this->sendSuccess(['message' => 'Question deleted successfully']);
        } else {
            $this->sendError("Failed to delete question");
        }
    }
}

$deleteQuestion = new DeleteQuestion();
$deleteQuestion->delete();
?>