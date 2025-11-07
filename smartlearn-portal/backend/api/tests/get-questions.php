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
}

$getQuestions = new GetQuestions();
$getQuestions->getByTest();
?>