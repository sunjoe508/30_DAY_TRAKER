<?php
class Subject {
    private $conn;
    private $table_name = "subjects";

    public $id;
    public $name;
    public $description;
    public $difficulty;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET name=:name, description=:description, difficulty=:difficulty";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":difficulty", $this->difficulty);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . "
                ORDER BY difficulty, name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE id = ?
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getWithTestCount() {
        $query = "SELECT s.*, COUNT(t.id) as test_count
                FROM " . $this->table_name . " s
                LEFT JOIN tests t ON s.id = t.subject_id
                GROUP BY s.id
                ORDER BY s.difficulty, s.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET name=:name, description=:description, difficulty=:difficulty
                WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":difficulty", $this->difficulty);
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
}
?>