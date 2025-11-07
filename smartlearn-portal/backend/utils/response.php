<?php
class Response {
    public static function send($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data = null, $message = null) {
        $response = ['success' => true];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if ($data) {
            $response['data'] = $data;
        }
        
        self::send($response);
    }

    public static function error($message, $status_code = 400) {
        self::send([
            'success' => false,
            'error' => $message
        ], $status_code);
    }

    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }

    public static function unauthorized($message = 'Unauthorized access') {
        self::error($message, 401);
    }

    public static function forbidden($message = 'Access forbidden') {
        self::error($message, 403);
    }
}
?>