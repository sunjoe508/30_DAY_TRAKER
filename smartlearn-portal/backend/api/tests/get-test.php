<?php
require_once '../../config/core.php';
require_once '../../models/Test.php';
require_once '../../models/Question.php';

class GetTest extends Core {
    public function getById() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $test_id = $_GET['id'] ?? null;
        if (!$test_id) {
            $this->sendError("Test ID required");
        }

        $test = new Test($this->db);
        $test_data = $test->getById($test_id);
        
        if (!$test_data) {
            $this->sendError("Test not found");
        }

        // Get questions for the test
        $question = new Question($this->db);
        $questions = $question->getByTestId($test_id);

        $test_data['questions'] = $questions;

        $this->sendSuccess($test_data);
    }
}

$getTest = new GetTest();
$getTest->getById();
?>