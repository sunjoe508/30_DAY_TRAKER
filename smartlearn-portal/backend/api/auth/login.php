<?php
require_once '../../config/core.php';
require_once '../../models/User.php';
require_once '../../utils/validation.php';

class Login extends Core {
    public function authenticate() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->email) || !isset($data->password)) {
            $this->sendError("Email and password required");
        }

        // Validate email
        if (!Validation::validateEmail($data->email)) {
            $this->sendError("Invalid email format");
        }

        $user = new User($this->db);
        $user->email = $data->email;
        
        if ($user->emailExists() && password_verify($data->password, $user->password)) {
            $token = $this->generateToken($user);
            
            // Log login activity
            $this->logActivity($user->id, "user_login", "User logged in from IP: " . $this->getClientIP());
            
            $this->sendSuccess([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ]);
        }
        
        $this->sendError("Invalid credentials");
    }

    private function generateToken($user) {
        $payload = [
            'iss' => "smartlearn-portal",
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 hours
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]
        ];

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }
}

$login = new Login();
$login->authenticate();
?>