<?php
require_once 'auth.php';

class AdminAuthMiddleware extends AuthMiddleware {
    public function __construct() {
        parent::__construct();
    }
    
    public function requireAdmin() {
        return $this->requireRole(['admin']);
    }
}
?>