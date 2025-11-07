<?php
class Test {
    private $conn;
    private $table_name = "tests";

    public $id;
    public $title;
    public $description;
    public $subject_id;
    public $duration;
    public $difficulty;
    public $max_attempts;
    public $created_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET title=:title, description=:description, subject_id=:subject_id,
                    duration=:duration, difficulty=:difficulty, max_attempts=:max_attempts,
                    created_by=:created_by";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->subject_id = htmlspecialchars(strip_tags($this->subject_id));
        $this->duration = htmlspecialchars(strip_tags($this->duration));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->max_attempts = htmlspecialchars(strip_tags($this->max_attempts));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        
        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":subject_id", $this->subject_id);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":max_attempts", $this->max_attempts);
        $stmt->bindParam(":created_by", $this->created_by);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT t.*, s.name as subject_name, u.name as created_by_name
                FROM " . $this->table_name . " t
                LEFT JOIN subjects s ON t.subject_id = s.id
                LEFT JOIN users u ON t.created_by = u.id
                WHERE t.id = ?
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT t.*, s.name as subject_name, u.name as created_by_name
                FROM " . $this->table_name . " t
                LEFT JOIN subjects s ON t.subject_id = s.id
                LEFT JOIN users u ON t.created_by = u.id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getBySubject($subject_id) {
        $query = "SELECT t.*, s.name as subject_name
                FROM " . $this->table_name . " t
                LEFT JOIN subjects s ON t.subject_id = s.id
                WHERE t.subject_id = ?
                ORDER BY t.difficulty, t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $subject_id);
        $stmt->execute();
        
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET title=:title, description=:description, subject_id=:subject_id,
                    duration=:duration, difficulty=:difficulty, max_attempts=:max_attempts
                WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->subject_id = htmlspecialchars(strip_tags($this->subject_id));
        $this->duration = htmlspecialchars(strip_tags($this->duration));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->max_attempts = htmlspecialchars(strip_tags($this->max_attempts));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":subject_id", $this->subject_id);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":max_attempts", $this->max_attempts);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getRecommendedTests($user_id) {
        $query = "SELECT t.*, s.name as subject_name,
                 (SELECT COUNT(*) FROM results r WHERE r.test_id = t.id AND r.user_id = ?) as attempts
                 FROM " . $this->table_name . " t
                 LEFT JOIN subjects s ON t.subject_id = s.id
                 WHERE t.max_attempts > (SELECT COUNT(*) FROM results r WHERE r.test_id = t.id AND r.user_id = ?)
                 OR (SELECT COUNT(*) FROM results r WHERE r.test_id = t.id AND r.user_id = ?) = 0
                 ORDER BY t.difficulty, t.created_at DESC
                 LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $user_id);
        $stmt->bindParam(3, $user_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>