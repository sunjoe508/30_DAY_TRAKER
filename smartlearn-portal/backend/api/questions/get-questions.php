<?php
require_once '../../config/core.php';
require_once '../../models/Question.php';

class GetQuestions extends Core {
    public function getByTest() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $test_id = $_GET['test_id'] ?? null;
        if (!$test_id) {
            $this->sendError("Test ID required");
        }

        $question = new Question($this->db);
        $questions = $question->getByTestId($test_id);

        $this->sendSuccess($questions);
    }

    public function getById() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $question_id = $_GET['id'] ?? null;
        if (!$question_id) {
            $this->sendError("Question ID required");
        }

        $question = new Question($this->db);
        $question_data = $question->getById($question_id);
        
        if (!$question_data) {
            $this->sendError("Question not found");
        }

        $this->sendSuccess($question_data);
    }
}

$getQuestions = new GetQuestions();

if (isset($_GET['id'])) {
    $getQuestions->getById();
} else {
    $getQuestions->getByTest();
}
?>