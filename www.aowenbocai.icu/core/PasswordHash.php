<?php
/**
 * 安全密码哈希工具类
 * 使用PHP内置的password_hash和password_verify函数
 * 替代不安全的MD5哈希
 */
class PasswordHash
{
    /**
     * 生成密码哈希
     * @param string $password 明文密码
     * @return string 哈希后的密码
     */
    public static function hash($password)
    {
        // 使用PASSWORD_DEFAULT算法（目前是bcrypt）
        // 自动生成盐值，成本因子为12
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    /**
     * 验证密码
     * @param string $password 明文密码
     * @param string $hash 存储的哈希值
     * @return bool 验证结果
     */
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * 检查哈希是否需要重新生成
     * @param string $hash 当前哈希值
     * @return bool 是否需要重新哈希
     */
    public static function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    /**
     * 兼容旧MD5密码的验证方法
     * @param string $password 明文密码
     * @param string $hash 存储的哈希值
     * @return bool 验证结果
     */
    public static function verifyLegacy($password, $hash)
    {
        // 如果是新的bcrypt哈希
        if (strlen($hash) > 32) {
            return self::verify($password, $hash);
        }
        
        // 兼容旧的MD5哈希
        $md5Hash = md5($password);
        
        // 检查完整MD5
        if ($hash === $md5Hash) {
            return true;
        }
        
        // 检查截取的MD5（兼容旧系统）
        if (strlen($hash) === 16 && $hash === substr($md5Hash, 8, 16)) {
            return true;
        }
        
        return false;
    }

    /**
     * 生成随机字符串（用于SID等）
     * @param int $length 长度
     * @return string 随机字符串
     */
    public static function generateRandomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    /**
     * 迁移旧密码到新哈希算法
     * @param string $userId 用户ID
     * @param string $plainPassword 明文密码（登录时提供）
     * @param object $userModel 用户模型实例
     * @return bool 是否成功迁移
     */
    public static function migratePassword($userId, $plainPassword, $userModel)
    {
        try {
            $newHash = self::hash($plainPassword);
            $result = $userModel->where('id', $userId)->update(['password' => $newHash]);
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }
}
