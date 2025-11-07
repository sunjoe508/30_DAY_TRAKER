-- Main database structure for SmartLearn Portal
CREATE DATABASE IF NOT EXISTS smartlearn_portal;
USE smartlearn_portal;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'instructor', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Subjects table (Python topics)
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tests table
CREATE TABLE tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    subject_id INT,
    duration INT NOT NULL, -- in minutes
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    max_attempts INT DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Questions table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT,
    question TEXT NOT NULL,
    type ENUM('multiple_choice', 'code_challenge', 'project') DEFAULT 'multiple_choice',
    options JSON, -- For multiple choice questions
    correct_answer VARCHAR(255),
    code_snippet TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    points INT DEFAULT 1,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

-- Results table
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    test_id INT,
    score DECIMAL(5,2),
    time_spent INT, -- in seconds
    answers JSON,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);
CREATE INDEX idx_activity_logs_type ON activity_logs(activity_type);
CREATE INDEX idx_results_user_id ON results(user_id);
CREATE INDEX idx_results_test_id ON results(test_id);
CREATE INDEX idx_questions_test_id ON questions(test_id);
CREATE INDEX idx_tests_subject_id ON tests(subject_id);

-- Insert default admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@smartlearn.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Python subjects
INSERT INTO subjects (name, description, difficulty) VALUES
('Python Basics', 'Introduction to Python programming fundamentals', 'beginner'),
('Data Structures', 'Lists, tuples, dictionaries, and sets in Python', 'beginner'),
('Functions & Modules', 'Python functions, modules, and packages', 'intermediate'),
('Object-Oriented Programming', 'Classes, objects, inheritance, and polymorphism', 'intermediate'),
('Algorithms', 'Common algorithms and problem-solving in Python', 'advanced');