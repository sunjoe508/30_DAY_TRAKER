<?php
require_once '../../config/core.php';
require_once '../../models/User.php';

class CreateUser extends Core {
    public function create() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can create users
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
            $this->sendError("Name, email and password are required");
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

        if ($user->create()) {
            $this->sendSuccess([
                'message' => 'User created successfully',
                'user_id' => $user->id
            ]);
        } else {
            $this->sendError("Failed to create user");
        }
    }
}

$createUser = new CreateUser();
$createUser->create();
?>