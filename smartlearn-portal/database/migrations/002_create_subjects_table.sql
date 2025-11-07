CREATE TABLE IF NOT EXISTS subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Python subjects
INSERT INTO subjects (name, description, difficulty) VALUES
('Python Basics', 'Introduction to Python programming fundamentals', 'beginner'),
('Data Structures', 'Lists, tuples, dictionaries, and sets in Python', 'beginner'),
('Functions & Modules', 'Python functions, modules, and packages', 'intermediate'),
('Object-Oriented Programming', 'Classes, objects, inheritance, and polymorphism', 'intermediate'),
('Algorithms', 'Common algorithms and problem-solving in Python', 'advanced');