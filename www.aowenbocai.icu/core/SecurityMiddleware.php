<?php
/**
 * 安全中间件
 * 集成所有安全检查功能
 */
class SecurityMiddleware
{
    /**
     * 执行安全检查
     * @return bool|array 通过检查返回true，否则返回错误信息
     */
    public static function check()
    {
        // 1. 初始化安全Session
        SessionSecurity::initSecureSession();
        
        // 2. 验证Session有效性
        if (!SessionSecurity::validateSession()) {
            return ['error' => 'Session无效', 'code' => 401];
        }
        
        // 3. 检测异常请求
        if (SecurityMonitor::detectAnomalousRequest()) {
            return ['error' => '检测到可疑请求', 'code' => 403];
        }
        
        // 4. 检查请求频率限制
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (SecurityMonitor::checkRateLimit($clientIp, 200, 3600)) {
            return ['error' => '请求过于频繁', 'code' => 429];
        }
        
        // 5. 清理输入数据
        self::sanitizeInput();
        
        return true;
    }

    /**
     * 清理输入数据
     */
    private static function sanitizeInput()
    {
        // 清理GET参数
        if (!empty($_GET)) {
            $_GET = XSSProtection::clean($_GET);
        }
        
        // 清理POST参数
        if (!empty($_POST)) {
            $_POST = XSSProtection::clean($_POST);
        }
        
        // 清理COOKIE
        if (!empty($_COOKIE)) {
            $_COOKIE = XSSProtection::clean($_COOKIE);
        }
    }

    /**
     * 检查登录安全
     * @param string $identifier 用户标识
     * @param string $type 登录类型
     * @return bool 是否允许登录
     */
    public static function checkLoginSecurity($identifier, $type = 'login')
    {
        return !SecurityMonitor::checkBruteForce($identifier, $type);
    }

    /**
     * 记录登录失败
     * @param string $identifier 用户标识
     * @param string $type 登录类型
     */
    public static function recordLoginFailure($identifier, $type = 'login')
    {
        SecurityMonitor::recordFailedAttempt($identifier, $type);
    }

    /**
     * 记录登录成功
     * @param string $identifier 用户标识
     * @param string $type 登录类型
     */
    public static function recordLoginSuccess($identifier, $type = 'login')
    {
        SecurityMonitor::clearFailedAttempts($identifier, $type);
        SecurityMonitor::logSecurityEvent('LOGIN_SUCCESS', "Successful login", [
            'identifier' => $identifier,
            'type' => $type
        ]);
    }

    /**
     * 验证CSRF令牌
     * @param string $token 提交的令牌
     * @return bool 验证结果
     */
    public static function validateCSRF($token)
    {
        return SessionSecurity::validateCSRFToken($token);
    }

    /**
     * 生成CSRF令牌
     * @return string CSRF令牌
     */
    public static function generateCSRF()
    {
        return SessionSecurity::generateCSRFToken();
    }

    /**
     * 安全退出
     */
    public static function secureLogout()
    {
        SecurityMonitor::logSecurityEvent('LOGOUT', "User logout");
        SessionSecurity::destroySession();
    }
}
