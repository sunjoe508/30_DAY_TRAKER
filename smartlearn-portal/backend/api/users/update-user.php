<?php
require_once '../../config/core.php';
require_once '../../models/User.php';

class UpdateUser extends Core {
    public function update() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->id)) {
            $this->sendError("User ID required");
        }

        // Users can only update their own data, unless they're admin
        if ($user_data->role != 'admin' && $data->id != $user_data->user_id) {
            $this->sendError("Insufficient permissions");
        }

        $user = new User($this->db);
        $user->id = $data->id;
        $user->name = $data->name ?? null;
        $user->email = $data->email ?? null;
        $user->password = $data->password ?? null;

        // Only admins can change roles
        if ($user_data->role == 'admin' && isset($data->role)) {
            $user->role = $data->role;
        }

        if ($user->update()) {
            $this->sendSuccess(['message' => 'User updated successfully']);
        } else {
            $this->sendError("Failed to update user");
        }
    }
}

$updateUser = new UpdateUser();
$updateUser->update();
?>