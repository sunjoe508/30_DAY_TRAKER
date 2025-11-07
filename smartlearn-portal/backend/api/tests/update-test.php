<?php
require_once '../../config/core.php';
require_once '../../models/Test.php';

class UpdateTest extends Core {
    public function update() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only instructors and admins can update tests
        if ($user_data->role != 'instructor' && $user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->id)) {
            $this->sendError("Test ID required");
        }

        $test = new Test($this->db);
        $test->id = $data->id;
        $test->title = $data->title ?? null;
        $test->description = $data->description ?? null;
        $test->subject_id = $data->subject_id ?? null;
        $test->duration = $data->duration ?? null;
        $test->difficulty = $data->difficulty ?? null;
        $test->max_attempts = $data->max_attempts ?? null;

        if ($test->update()) {
            $this->sendSuccess(['message' => 'Test updated successfully']);
        } else {
            $this->sendError("Failed to update test");
        }
    }
}

$updateTest = new UpdateTest();
$updateTest->update();
?>