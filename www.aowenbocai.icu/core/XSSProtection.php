<?php
/**
 * XSS防护工具类
 */
class XSSProtection
{
    /**
     * 清理用户输入，防止XSS攻击
     * @param string $input 用户输入
     * @param bool $allowHtml 是否允许HTML标签
     * @return string 清理后的内容
     */
    public static function clean($input, $allowHtml = false)
    {
        if (is_array($input)) {
            return array_map([self::class, 'clean'], $input);
        }
        
        if (!is_string($input)) {
            return $input;
        }
        
        // 移除null字节
        $input = str_replace(chr(0), '', $input);
        
        if ($allowHtml) {
            // 允许HTML但进行安全过滤
            return self::filterHtml($input);
        } else {
            // 完全转义HTML
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    /**
     * 过滤HTML内容，只保留安全的标签
     * @param string $html HTML内容
     * @return string 过滤后的HTML
     */
    public static function filterHtml($html)
    {
        // 允许的HTML标签
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code>';
        
        // 移除危险的标签和属性
        $html = strip_tags($html, $allowedTags);
        
        // 移除危险的属性
        $html = preg_replace('/(<[^>]+)\s+(on\w+|javascript:|vbscript:|data:)[^>]*>/i', '$1>', $html);
        
        // 移除style属性中的危险内容
        $html = preg_replace('/style\s*=\s*["\'][^"\']*expression\s*\([^"\']*["\']/', '', $html);
        
        return $html;
    }

    /**
     * 清理输出内容
     * @param string $content 输出内容
     * @return string 清理后的内容
     */
    public static function output($content)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 清理URL
     * @param string $url URL地址
     * @return string 清理后的URL
     */
    public static function cleanUrl($url)
    {
        // 移除危险的协议
        $url = preg_replace('/^(javascript|vbscript|data):/i', '', $url);
        
        // 验证URL格式
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }
        
        return $url;
    }

    /**
     * 生成安全的JSON输出
     * @param mixed $data 数据
     * @return string JSON字符串
     */
    public static function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 验证和清理文件名
     * @param string $filename 文件名
     * @return string 安全的文件名
     */
    public static function cleanFilename($filename)
    {
        // 移除路径分隔符
        $filename = basename($filename);
        
        // 只保留字母、数字、点、下划线和连字符
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // 防止多个点
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // 限制长度
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        return $filename;
    }

    /**
     * 检查内容是否包含恶意代码
     * @param string $content 内容
     * @return bool 是否包含恶意代码
     */
    public static function containsMalicious($content)
    {
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/expression\s*\(/i',
            '/url\s*\(/i',
            '/@import/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
}
