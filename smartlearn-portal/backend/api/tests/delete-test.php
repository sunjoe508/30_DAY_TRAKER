<?php
require_once '../../config/core.php';
require_once '../../models/Test.php';

class DeleteTest extends Core {
    public function delete() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can delete tests
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $test_id = $_GET['id'] ?? null;
        if (!$test_id) {
            $this->sendError("Test ID required");
        }

        $test = new Test($this->db);
        $test->id = $test_id;

        if ($test->delete()) {
            $this->sendSuccess(['message' => 'Test deleted successfully']);
        } else {
            $this->sendError("Failed to delete test");
        }
    }
}

$deleteTest = new DeleteTest();
$deleteTest->delete();
?>