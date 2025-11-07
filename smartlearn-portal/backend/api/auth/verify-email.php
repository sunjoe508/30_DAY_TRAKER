<?php
require_once '../../config/core.php';

class VerifyEmail extends Core {
    public function verify() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->email)) {
            $this->sendError("Email is required");
        }

        $user = new User($this->db);
        $user->email = $data->email;
        
        if ($user->emailExists()) {
            $this->sendError("Email already registered");
        } else {
            $this->sendSuccess(['message' => 'Email available']);
        }
    }
}

$verify = new VerifyEmail();
$verify->verify();
?>