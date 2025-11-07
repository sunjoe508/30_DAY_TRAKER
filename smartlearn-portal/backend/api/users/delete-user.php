<?php
require_once '../../config/core.php';
require_once '../../models/User.php';

class DeleteUser extends Core {
    public function delete() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can delete users
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $user_id = $_GET['id'] ?? null;
        if (!$user_id) {
            $this->sendError("User ID required");
        }

        // Prevent self-deletion
        if ($user_id == $user_data->user_id) {
            $this->sendError("Cannot delete your own account");
        }

        $user = new User($this->db);
        $user->id = $user_id;

        if ($user->delete()) {
            $this->sendSuccess(['message' => 'User deleted successfully']);
        } else {
            $this->sendError("Failed to delete user");
        }
    }
}

$deleteUser = new DeleteUser();
$deleteUser->delete();
?>