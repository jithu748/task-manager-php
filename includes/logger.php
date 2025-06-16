<?php
class Logger {
    private static $log_file = 'logs/app.log';
    
    public static function init() {
        $log_dir = __DIR__ . '/../logs';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        self::$log_file = $log_dir . '/app.log';
    }
    
    public static function log($message, $level = 'INFO') {
        self::init();
        
        $date = date('Y-m-d H:i:s');
        $log_message = "[$date] [$level] $message" . PHP_EOL;
        
        file_put_contents(self::$log_file, $log_message, FILE_APPEND);
    }
    
    public static function error($message) {
        self::log($message, 'ERROR');
    }
    
    public static function warning($message) {
        self::log($message, 'WARNING');
    }
    
    public static function info($message) {
        self::log($message, 'INFO');
    }
    
    // Clean old logs (older than 30 days)
    public static function cleanup() {
        self::init();
        
        if (file_exists(self::$log_file)) {
            if (filemtime(self::$log_file) < time() - 30 * 24 * 60 * 60) {
                rename(self::$log_file, self::$log_file . '.' . date('Y-m-d'));
            }
        }
    }
}
