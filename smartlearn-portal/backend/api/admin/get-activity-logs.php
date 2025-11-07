<?php
require_once '../../config/core.php';
require_once '../../models/ActivityLog.php';

class GetActivityLogs extends Core {
    public function getLogs() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can see activity logs
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $limit = $_GET['limit'] ?? 50;
        
        $activity_log = new ActivityLog($this->db);
        $stmt = $activity_log->getRecent($limit);
        
        $logs = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = $row;
        }

        $this->sendSuccess($logs);
    }
}

$getLogs = new GetActivityLogs();
$getLogs->getLogs();
?>