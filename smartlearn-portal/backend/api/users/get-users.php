<?php
require_once '../../config/core.php';
require_once '../../models/User.php';

class GetUsers extends Core {
    public function getAll() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can see all users
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $user = new User($this->db);
        $stmt = $user->getAll();
        
        $users = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($row['password']); // Remove password from response
            $users[] = $row;
        }

        $this->sendSuccess($users);
    }

    public function getById() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $user_id = $_GET['id'] ?? $user_data->user_id;
        
        // Users can only see their own data, unless they're admin
        if ($user_data->role != 'admin' && $user_id != $user_data->user_id) {
            $this->sendError("Insufficient permissions");
        }

        $user = new User($this->db);
        $user_data = $user->getById($user_id);
        
        if (!$user_data) {
            $this->sendError("User not found");
        }

        unset($user_data['password']); // Remove password from response

        $this->sendSuccess($user_data);
    }
}

$getUsers = new GetUsers();

if (isset($_GET['id'])) {
    $getUsers->getById();
} else {
    $getUsers->getAll();
}
?>