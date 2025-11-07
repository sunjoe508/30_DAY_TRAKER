<?php
class Security {
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitizeFileName($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '', $filename);
        return $filename;
    }

    public static function getClientIP() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (self::validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private static function validateIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    public static function rateLimit($key, $max_attempts = 5, $time_window = 900) { // 15 minutes
        $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($key);
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            $current_time = time();
            
            if ($current_time - $data['first_attempt'] <= $time_window) {
                if ($data['attempts'] >= $max_attempts) {
                    return false; // Rate limited
                }
                $data['attempts']++;
            } else {
                // Reset counter
                $data = ['attempts' => 1, 'first_attempt' => $current_time];
            }
        } else {
            $data = ['attempts' => 1, 'first_attempt' => time()];
        }
        
        file_put_contents($cache_file, json_encode($data));
        return true;
    }
}
?>