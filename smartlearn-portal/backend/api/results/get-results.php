<?php
require_once '../../config/core.php';
require_once '../../models/Result.php';

class GetResults extends Core {
    public function getAll() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        // Only admins can see all results
        if ($user_data->role != 'admin') {
            $this->sendError("Insufficient permissions");
        }

        $result = new Result($this->db);
        $stmt = $result->getAll();
        
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }

        $this->sendSuccess($results);
    }
}

$getResults = new GetResults();
$getResults->getAll();
?>