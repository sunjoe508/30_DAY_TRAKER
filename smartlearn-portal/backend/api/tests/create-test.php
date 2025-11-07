<?php
require_once '../../config/core.php';
require_once '../../models/Test.php';

class CreateTest extends Core {
    public function create() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only instructors and admins can create tests
        if ($user_data->role != 'instructor' && $user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->title) || !isset($data->duration)) {
            $this->sendError("Title and duration are required");
        }

        $test = new Test($this->db);
        $test->title = $data->title;
        $test->description = $data->description ?? '';
        $test->subject_id = $data->subject_id ?? null;
        $test->duration = $data->duration;
        $test->difficulty = $data->difficulty ?? 'beginner';
        $test->max_attempts = $data->max_attempts ?? 1;
        $test->created_by = $user_data->user_id;

        $test_id = $test->create();
        
        if ($test_id) {
            // Log test creation activity
            $this->logActivity($user_data->user_id, "test_created", "Created test: {$data->title} (ID: {$test_id})");
            
            $this->sendSuccess([
                'message' => 'Test created successfully',
                'test_id' => $test_id
            ]);
        } else {
            $this->sendError("Failed to create test");
        }
    }
}

$createTest = new CreateTest();
$createTest->create();
?>