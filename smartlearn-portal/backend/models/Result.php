<?php
class Result {
    private $conn;
    private $table_name = "results";

    public $id;
    public $user_id;
    public $test_id;
    public $score;
    public $time_spent;
    public $answers;
    public $completed_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET user_id=:user_id, test_id=:test_id, score=:score,
                    time_spent=:time_spent, answers=:answers";
        
        $stmt = $this->conn->prepare($query);
        
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->test_id = htmlspecialchars(strip_tags($this->test_id));
        $this->score = htmlspecialchars(strip_tags($this->score));
        $this->time_spent = htmlspecialchars(strip_tags($this->time_spent));
        $this->answers = json_encode($this->answers);
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":test_id", $this->test_id);
        $stmt->bindParam(":score", $this->score);
        $stmt->bindParam(":time_spent", $this->time_spent);
        $stmt->bindParam(":answers", $this->answers);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getByUserId($user_id) {
        $query = "SELECT r.*, t.title as test_title, s.name as subject_name
                FROM " . $this->table_name . " r
                LEFT JOIN tests t ON r.test_id = t.id
                LEFT JOIN subjects s ON t.subject_id = s.id
                WHERE r.user_id = ?
                ORDER BY r.completed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['answers']) {
                $row['answers'] = json_decode($row['answers'], true);
            }
            $results[] = $row;
        }
        
        return $results;
    }

    public function getById($id) {
        $query = "SELECT r.*, t.title as test_title, t.duration, t.difficulty,
                         s.name as subject_name, u.name as user_name
                FROM " . $this->table_name . " r
                LEFT JOIN tests t ON r.test_id = t.id
                LEFT JOIN subjects s ON t.subject_id = s.id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.id = ?
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['answers']) {
                $row['answers'] = json_decode($row['answers'], true);
            }
            return $row;
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT r.*, t.title as test_title, u.name as user_name,
                         s.name as subject_name
                FROM " . $this->table_name . " r
                LEFT JOIN tests t ON r.test_id = t.id
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN subjects s ON t.subject_id = s.id
                ORDER BY r.completed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getAnalytics($user_id) {
        // Get average score
        $query = "SELECT AVG(score) as average_score, COUNT(*) as tests_completed
                FROM " . $this->table_name . "
                WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $basic_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get strongest topic
        $query = "SELECT s.name as topic, AVG(r.score) as avg_score
                FROM " . $this->table_name . " r
                LEFT JOIN tests t ON r.test_id = t.id
                LEFT JOIN subjects s ON t.subject_id = s.id
                WHERE r.user_id = ?
                GROUP BY s.name
                ORDER BY avg_score DESC
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $strongest_topic = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'averageScore' => round($basic_stats['average_score'], 2),
            'testsCompleted' => $basic_stats['tests_completed'],
            'strongestTopic' => $strongest_topic['topic'] ?? 'N/A'
        );
    }

    public function getTestStats($test_id) {
        $query = "SELECT COUNT(*) as total_attempts, AVG(score) as average_score,
                         MIN(score) as min_score, MAX(score) as max_score
                FROM " . $this->table_name . "
                WHERE test_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $test_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>