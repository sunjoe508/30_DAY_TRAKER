<?php
require_once '../../config/core.php';
require_once '../../models/Result.php';

class GetAnalytics extends Core {
    public function getUserAnalytics() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $result = new Result($this->db);
        $analytics = $result->getAnalytics($user_data->user_id);

        $this->sendSuccess($analytics);
    }

    public function getTestAnalytics() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only instructors and admins can see test analytics
        if ($user_data->role != 'instructor' && $user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $test_id = $_GET['test_id'] ?? null;
        if (!$test_id) {
            $this->sendError("Test ID required");
        }

        $result = new Result($this->db);
        $analytics = $result->getTestStats($test_id);

        $this->sendSuccess($analytics);
    }
}

$getAnalytics = new GetAnalytics();

if (isset($_GET['test_id'])) {
    $getAnalytics->getTestAnalytics();
} else {
    $getAnalytics->getUserAnalytics();
}
?>