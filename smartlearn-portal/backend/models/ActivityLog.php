<?php
class ActivityLog {
    private $conn;
    private $table_name = "activity_logs";

    public $id;
    public $user_id;
    public $activity_type;
    public $description;
    public $ip_address;
    public $user_agent;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET user_id=:user_id, activity_type=:activity_type,
                    description=:description, ip_address=:ip_address,
                    user_agent=:user_agent";
        
        $stmt = $this->conn->prepare($query);
        
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->activity_type = htmlspecialchars(strip_tags($this->activity_type));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->ip_address = htmlspecialchars(strip_tags($this->ip_address));
        $this->user_agent = htmlspecialchars(strip_tags($this->user_agent));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":activity_type", $this->activity_type);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":ip_address", $this->ip_address);
        $stmt->bindParam(":user_agent", $this->user_agent);
        
        return $stmt->execute();
    }

    public function getRecent($limit = 50) {
        $query = "SELECT al.*, u.name as user_name
                FROM " . $this->table_name . " al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function getByUserId($user_id, $limit = 20) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function getByType($activity_type, $limit = 50) {
        $query = "SELECT al.*, u.name as user_name
                FROM " . $this->table_name . " al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.activity_type = ?
                ORDER BY al.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $activity_type);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function getStats($days = 7) {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total_activities,
                    COUNT(DISTINCT user_id) as unique_users
                FROM " . $this->table_name . "
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function cleanupOldLogs($days = 30) {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $days, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Static method to log activity easily
    public static function log($db, $user_id, $activity_type, $description) {
        $activity_log = new ActivityLog($db);
        $activity_log->user_id = $user_id;
        $activity_log->activity_type = $activity_type;
        $activity_log->description = $description;
        $activity_log->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $activity_log->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        return $activity_log->create();
    }

    // Common activity types
    public static function getActivityTypes() {
        return [
            'user_login' => 'User Login',
            'user_logout' => 'User Logout',
            'user_registered' => 'User Registration',
            'test_started' => 'Test Started',
            'test_completed' => 'Test Completed',
            'test_created' => 'Test Created',
            'test_updated' => 'Test Updated',
            'test_deleted' => 'Test Deleted',
            'question_created' => 'Question Created',
            'question_updated' => 'Question Updated',
            'question_deleted' => 'Question Deleted',
            'profile_updated' => 'Profile Updated',
            'password_changed' => 'Password Changed',
            'admin_action' => 'Admin Action',
            'system_event' => 'System Event'
        ];
    }
}
?>