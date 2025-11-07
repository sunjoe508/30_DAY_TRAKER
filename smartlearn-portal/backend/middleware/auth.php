<?php
require_once '../config/core.php';

class AuthMiddleware {
    private $core;
    
    public function __construct() {
        $this->core = new Core();
    }
    
    public function authenticate() {
        $user_data = $this->core->validateToken();
        if (!$user_data) {
            $this->core->sendError("Authentication required", 401);
        }
        return $user_data;
    }
    
    public function requireRole($allowed_roles) {
        $user_data = $this->authenticate();
        
        if (!in_array($user_data->role, $allowed_roles)) {
            $this->core->sendError("Insufficient permissions", 403);
        }
        
        return $user_data;
    }
    
    public function requireAdmin() {
        return $this->requireRole(['admin']);
    }
    
    public function requireInstructor() {
        return $this->requireRole(['instructor', 'admin']);
    }
}
?>