<?php
require_once '../../config/core.php';
require_once '../../models/User.php';
require_once '../../utils/validation.php';

class Register extends Core {
    public function registerUser() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
            $this->sendError("Name, email and password are required");
        }

        // Validate email
        if (!Validation::validateEmail($data->email)) {
            $this->sendError("Invalid email format");
        }

        // Validate password strength
        if (!Validation::validatePassword($data->password)) {
            $this->sendError("Password must be at least 8 characters long");
        }

        $user = new User($this->db);
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->role = $data->role ?? 'student';

        // Check if email already exists
        if ($user->emailExists()) {
            $this->sendError("Email already registered");
        }

        // Create user
        if ($user->create()) {
            // Log activity
            $this->logActivity($user->id, "user_registered", "New user registration: {$user->email}");
            
            $this->sendSuccess([
                'message' => 'User registered successfully',
                'user_id' => $user->id
            ]);
        } else {
            $this->sendError("Registration failed");
        }
    }

    private function logActivity($user_id, $type, $description) {
        $activity_log = new ActivityLog($this->db);
        $activity_log->user_id = $user_id;
        $activity_log->activity_type = $type;
        $activity_log->description = $description;
        $activity_log->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $activity_log->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $activity_log->create();
    }
}

$register = new Register();
$register->registerUser();
?>