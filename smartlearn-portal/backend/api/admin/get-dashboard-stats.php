<?php
require_once '../../config/core.php';

class GetDashboardStats extends Core {
    public function getStats() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can see dashboard stats
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $stats = array();

        // Total users
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total tests
        $query = "SELECT COUNT(*) as total FROM tests";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalTests'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Active users today
        $query = "SELECT COUNT(DISTINCT user_id) as total FROM activity_logs 
                 WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['activeToday'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total results
        $query = "SELECT COUNT(*) as total FROM results";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalResults'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $this->sendSuccess($stats);
    }
}

$getStats = new GetDashboardStats();
$getStats->getStats();
?>