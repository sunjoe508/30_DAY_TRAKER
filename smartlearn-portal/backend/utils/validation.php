<?php
class Validation {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password) {
        return strlen($password) >= 8;
    }

    public static function validateName($name) {
        return !empty(trim($name)) && strlen(trim($name)) >= 2;
    }

    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map('self::sanitizeInput', $data);
        }
        
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function validateTestData($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Test title is required';
        }
        
        if (empty($data['duration']) || !is_numeric($data['duration']) || $data['duration'] <= 0) {
            $errors[] = 'Valid duration is required';
        }
        
        if (!empty($data['difficulty']) && !in_array($data['difficulty'], ['beginner', 'intermediate', 'advanced'])) {
            $errors[] = 'Invalid difficulty level';
        }
        
        return $errors;
    }

    public static function validateQuestionData($data) {
        $errors = [];
        
        if (empty($data['question'])) {
            $errors[] = 'Question text is required';
        }
        
        if (empty($data['type']) || !in_array($data['type'], ['multiple_choice', 'code_challenge', 'project'])) {
            $errors[] = 'Valid question type is required';
        }
        
        if ($data['type'] == 'multiple_choice' && empty($data['options'])) {
            $errors[] = 'Options are required for multiple choice questions';
        }
        
        return $errors;
    }
}
?>