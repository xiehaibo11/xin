<?php
/**
 * Session安全管理类
 */
class SessionSecurity
{
    /**
     * 初始化安全的Session配置
     */
    public static function initSecureSession()
    {
        // 防止Session固定攻击
        if (session_status() == PHP_SESSION_NONE) {
            // 设置安全的Session配置
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // 设置Session超时时间（30分钟）
            ini_set('session.gc_maxlifetime', 1800);
            ini_set('session.cookie_lifetime', 1800);
            
            // 使用更安全的Session ID生成
            ini_set('session.entropy_length', 32);
            ini_set('session.hash_function', 'sha256');
            
            session_start();
        }
        
        // 定期重新生成Session ID
        self::regenerateSessionId();
    }

    /**
     * 重新生成Session ID（防止Session固定攻击）
     */
    public static function regenerateSessionId()
    {
        // 每15分钟重新生成一次Session ID
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 900) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * 验证Session的有效性
     * @return bool
     */
    public static function validateSession()
    {
        // 检查Session是否过期
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > 1800)) {
            self::destroySession();
            return false;
        }
        
        // 更新最后活动时间
        $_SESSION['last_activity'] = time();
        
        // 验证用户代理（防止Session劫持）
        if (isset($_SESSION['user_agent'])) {
            if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                self::destroySession();
                return false;
            }
        } else {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        // 验证IP地址（可选，可能影响移动用户）
        if (isset($_SESSION['ip_address'])) {
            if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
                // 记录可疑活动
                error_log("Session IP mismatch: " . $_SESSION['ip_address'] . " vs " . $_SERVER['REMOTE_ADDR']);
            }
        } else {
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        }
        
        return true;
    }

    /**
     * 安全地销毁Session
     */
    public static function destroySession()
    {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * 设置安全的Cookie
     * @param string $name Cookie名称
     * @param string $value Cookie值
     * @param int $expire 过期时间
     * @param string $path 路径
     * @param string $domain 域名
     */
    public static function setSecureCookie($name, $value, $expire = 0, $path = '/', $domain = '')
    {
        setcookie($name, $value, $expire, $path, $domain, true, true);
    }

    /**
     * 生成CSRF令牌
     * @return string
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * 验证CSRF令牌
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}
