CREATE TABLE IF NOT EXISTS questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT,
    question TEXT NOT NULL,
    type ENUM('multiple_choice', 'code_challenge', 'project') DEFAULT 'multiple_choice',
    options JSON,
    correct_answer VARCHAR(255),
    code_snippet TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    points INT DEFAULT 1,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);