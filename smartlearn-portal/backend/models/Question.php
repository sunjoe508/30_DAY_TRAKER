<?php
class Question {
    private $conn;
    private $table_name = "questions";

    public $id;
    public $test_id;
    public $question;
    public $type;
    public $options;
    public $correct_answer;
    public $code_snippet;
    public $difficulty;
    public $points;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET test_id=:test_id, question=:question, type=:type,
                    options=:options, correct_answer=:correct_answer,
                    code_snippet=:code_snippet, difficulty=:difficulty, points=:points";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->test_id = htmlspecialchars(strip_tags($this->test_id));
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->options = $this->options ? json_encode($this->options) : null;
        $this->correct_answer = htmlspecialchars(strip_tags($this->correct_answer));
        $this->code_snippet = htmlspecialchars(strip_tags($this->code_snippet));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->points = htmlspecialchars(strip_tags($this->points));
        
        // Bind values
        $stmt->bindParam(":test_id", $this->test_id);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":options", $this->options);
        $stmt->bindParam(":correct_answer", $this->correct_answer);
        $stmt->bindParam(":code_snippet", $this->code_snippet);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":points", $this->points);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getByTestId($test_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE test_id = ?
                ORDER BY id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $test_id);
        $stmt->execute();
        
        $questions = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['options']) {
                $row['options'] = json_decode($row['options'], true);
            }
            $questions[] = $row;
        }
        
        return $questions;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE id = ?
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['options']) {
                $row['options'] = json_decode($row['options'], true);
            }
            return $row;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET question=:question, type=:type, options=:options,
                    correct_answer=:correct_answer, code_snippet=:code_snippet,
                    difficulty=:difficulty, points=:points
                WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->options = $this->options ? json_encode($this->options) : null;
        $this->correct_answer = htmlspecialchars(strip_tags($this->correct_answer));
        $this->code_snippet = htmlspecialchars(strip_tags($this->code_snippet));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->points = htmlspecialchars(strip_tags($this->points));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":options", $this->options);
        $stmt->bindParam(":correct_answer", $this->correct_answer);
        $stmt->bindParam(":code_snippet", $this->code_snippet);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":points", $this->points);
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

    public function validateAnswer($user_answer) {
        if ($this->type == 'multiple_choice') {
            return $user_answer == $this->correct_answer;
        } elseif ($this->type == 'code_challenge') {
            // Basic code validation - in production, use a code execution service
            return $this->validateCodeAnswer($user_answer);
        }
        return false;
    }

    private function validateCodeAnswer($user_code) {
        // Simple validation - check if key elements exist
        // In production, integrate with a code execution API
        $required_elements = explode(',', $this->correct_answer);
        $is_valid = true;
        
        foreach ($required_elements as $element) {
            if (strpos($user_code, trim($element)) === false) {
                $is_valid = false;
                break;
            }
        }
        
        return $is_valid;
    }
}
?>