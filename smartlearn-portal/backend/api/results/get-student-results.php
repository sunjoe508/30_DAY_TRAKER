<?php
require_once '../../config/core.php';
require_once '../../models/Result.php';

class GetStudentResults extends Core {
    public function getByUser() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $result = new Result($this->db);
        $results = $result->getByUserId($user_data->user_id);

        $this->sendSuccess($results);
    }
}

$getStudentResults = new GetStudentResults();
$getStudentResults->getByUser();
?>