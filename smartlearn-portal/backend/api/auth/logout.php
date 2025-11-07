<?php
require_once '../../config/core.php';

class Logout extends Core {
    public function logoutUser() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Invalid token");
        }

        // In a stateless JWT system, we can't invalidate the token on server side
        // Client should remove the token from storage
        // We can maintain a blacklist if needed
        
        $this->sendSuccess(['message' => 'Logged out successfully']);
    }
}

$logout = new Logout();
$logout->logoutUser();
?>