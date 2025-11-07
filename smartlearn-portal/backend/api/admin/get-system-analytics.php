<?php
require_once '../../config/core.php';

class GetSystemAnalytics extends Core {
    public function getAnalytics() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can see system analytics
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $analytics = array();

        // User growth (last 7 days)
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count
                 FROM users
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(created_at)
                 ORDER BY date";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $analytics['userGrowth'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Test completion rate
        $query = "SELECT AVG(score) as avg_score, 
                         COUNT(*) as total_tests,
                         COUNT(DISTINCT user_id) as unique_users
                 FROM results";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $analytics['completionStats'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Popular subjects
        $query = "SELECT s.name, COUNT(r.id) as attempt_count
                 FROM results r
                 JOIN tests t ON r.test_id = t.id
                 JOIN subjects s ON t.subject_id = s.id
                 GROUP BY s.id, s.name
                 ORDER BY attempt_count DESC
                 LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $analytics['popularSubjects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->sendSuccess($analytics);
    }
}

$getAnalytics = new GetSystemAnalytics();
$getAnalytics->getAnalytics();
?>