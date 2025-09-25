<?php
/**
 * 安全监控类
 */
class SecurityMonitor
{
    private static $logFile = '/www/wwwlogs/security.log';
    
    /**
     * 记录安全事件
     * @param string $event 事件类型
     * @param string $message 事件消息
     * @param array $context 上下文信息
     */
    public static function logSecurityEvent($event, $message, $context = [])
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'context' => $context
        ];
        
        $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents(self::$logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * 检测暴力破解攻击
     * @param string $identifier 标识符（IP或用户名）
     * @param string $type 类型（login, admin_login等）
     * @return bool 是否被限制
     */
    public static function checkBruteForce($identifier, $type = 'login')
    {
        $cacheKey = "bruteforce_{$type}_{$identifier}";
        $attempts = cache($cacheKey) ?: 0;
        
        // 超过5次失败尝试，锁定30分钟
        if ($attempts >= 5) {
            self::logSecurityEvent('BRUTE_FORCE_BLOCKED', "Blocked brute force attempt", [
                'identifier' => $identifier,
                'type' => $type,
                'attempts' => $attempts
            ]);
            return true;
        }
        
        return false;
    }

    /**
     * 记录失败的登录尝试
     * @param string $identifier 标识符
     * @param string $type 类型
     */
    public static function recordFailedAttempt($identifier, $type = 'login')
    {
        $cacheKey = "bruteforce_{$type}_{$identifier}";
        $attempts = cache($cacheKey) ?: 0;
        $attempts++;
        
        // 缓存30分钟
        cache($cacheKey, $attempts, 1800);
        
        self::logSecurityEvent('LOGIN_FAILED', "Failed login attempt", [
            'identifier' => $identifier,
            'type' => $type,
            'attempts' => $attempts
        ]);
    }

    /**
     * 清除失败尝试记录
     * @param string $identifier 标识符
     * @param string $type 类型
     */
    public static function clearFailedAttempts($identifier, $type = 'login')
    {
        $cacheKey = "bruteforce_{$type}_{$identifier}";
        cache($cacheKey, null);
    }

    /**
     * 检测异常请求
     * @param array $request 请求数据
     * @return bool 是否异常
     */
    public static function detectAnomalousRequest($request = null)
    {
        $request = $request ?: $_REQUEST;
        $suspicious = false;
        $reasons = [];
        
        // 检测SQL注入尝试
        $sqlPatterns = [
            '/union\s+select/i',
            '/select\s+.*\s+from/i',
            '/insert\s+into/i',
            '/delete\s+from/i',
            '/update\s+.*\s+set/i',
            '/drop\s+table/i',
            '/exec\s*\(/i',
            '/script\s*>/i'
        ];
        
        foreach ($request as $key => $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $suspicious = true;
                        $reasons[] = "SQL injection attempt in {$key}";
                        break;
                    }
                }
            }
        }
        
        // 检测XSS尝试
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i'
        ];
        
        foreach ($request as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $suspicious = true;
                        $reasons[] = "XSS attempt in {$key}";
                        break;
                    }
                }
            }
        }
        
        if ($suspicious) {
            self::logSecurityEvent('SUSPICIOUS_REQUEST', "Detected suspicious request", [
                'reasons' => $reasons,
                'request_data' => $request
            ]);
        }
        
        return $suspicious;
    }

    /**
     * 检查请求频率限制
     * @param string $identifier 标识符
     * @param int $limit 限制次数
     * @param int $window 时间窗口（秒）
     * @return bool 是否超过限制
     */
    public static function checkRateLimit($identifier, $limit = 100, $window = 3600)
    {
        $cacheKey = "rate_limit_{$identifier}";
        $requests = cache($cacheKey) ?: [];
        $now = time();
        
        // 清理过期的请求记录
        $requests = array_filter($requests, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        if (count($requests) >= $limit) {
            self::logSecurityEvent('RATE_LIMIT_EXCEEDED', "Rate limit exceeded", [
                'identifier' => $identifier,
                'requests' => count($requests),
                'limit' => $limit,
                'window' => $window
            ]);
            return true;
        }
        
        // 记录当前请求
        $requests[] = $now;
        cache($cacheKey, $requests, $window);
        
        return false;
    }

    /**
     * 生成安全报告
     * @param int $hours 过去几小时的数据
     * @return array 报告数据
     */
    public static function generateSecurityReport($hours = 24)
    {
        $logFile = self::$logFile;
        if (!file_exists($logFile)) {
            return ['error' => 'Log file not found'];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $cutoff = time() - ($hours * 3600);
        $events = [];
        
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if ($data && strtotime($data['timestamp']) > $cutoff) {
                $events[] = $data;
            }
        }
        
        // 统计事件类型
        $eventCounts = [];
        $ipCounts = [];
        
        foreach ($events as $event) {
            $eventCounts[$event['event']] = ($eventCounts[$event['event']] ?? 0) + 1;
            $ipCounts[$event['ip']] = ($ipCounts[$event['ip']] ?? 0) + 1;
        }
        
        return [
            'total_events' => count($events),
            'event_types' => $eventCounts,
            'top_ips' => array_slice($ipCounts, 0, 10, true),
            'recent_events' => array_slice($events, -20)
        ];
    }
}
