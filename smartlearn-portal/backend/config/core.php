<?php
// Include CORS configuration
require_once 'cors.php';

// Enable CORS
CORS::enableCORS();

// Composer autoload
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Core {
    protected $db;
    protected $secret_key = "your_secret_key_here";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    protected function validateToken() {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            return false;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function sendError($message, $status = 400) {
        $this->sendResponse([
            'success' => false,
            'error' => $message
        ], $status);
    }

    protected function sendSuccess($data = null) {
        $response = ['success' => true];
        if ($data) {
            $response['data'] = $data;
        }
        $this->sendResponse($response);
    }

    protected function logActivity($user_id, $activity_type, $description) {
        require_once '../models/ActivityLog.php';
        return ActivityLog::log($this->db, $user_id, $activity_type, $description);
    }

    protected function getClientIP() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if ($this->validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function validateIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
?>