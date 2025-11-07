-- Sample Python subjects
INSERT INTO subjects (name, description, difficulty) VALUES
('Python Basics', 'Introduction to Python programming fundamentals', 'beginner'),
('Data Structures', 'Lists, tuples, dictionaries, and sets in Python', 'beginner'),
('Functions & Modules', 'Python functions, modules, and packages', 'intermediate'),
('Object-Oriented Programming', 'Classes, objects, inheritance, and polymorphism', 'intermediate'),
('Algorithms', 'Common algorithms and problem-solving in Python', 'advanced');

-- Sample test
INSERT INTO tests (title, description, subject_id, duration, difficulty) VALUES
('Python Fundamentals Assessment', 'Test your basic Python knowledge', 1, 30, 'beginner');

-- Sample questions
INSERT INTO questions (test_id, question, type, options, correct_answer, difficulty) VALUES
(1, 'Which of the following is used to define a function in Python?', 'multiple_choice', 
 '["def", "function", "define", "func"]', '0', 'beginner'),

(1, 'What is the output of print(2 ** 3)?', 'multiple_choice',
 '["6", "8", "9", "23"]', '1', 'beginner'),

(1, 'Write a function to calculate the factorial of a number', 'code_challenge',
 NULL, 'def factorial(n):\n    if n == 0:\n        return 1\n    else:\n        return n * factorial(n-1)', 'intermediate');