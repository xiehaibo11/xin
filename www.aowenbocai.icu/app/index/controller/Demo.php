<?php
namespace app\index\controller;

use think\Controller;
//use core\model\Good;
//use core\game\Issue;
//use core\game\GridGame;
use core\game\Prize;

class Demo extends Base
{
	public function index(){
        // 使用安全的URL访问方法
        $allowedHosts = ['trade.500.com']; // 白名单
        $url = "http://trade.500.com/jczq/index.php?playid=312&g=2";

        // 验证URL安全性
        if (!$this->isUrlSafe($url, $allowedHosts)) {
            return json(['error' => '不允许访问该URL']);
        }

        // 使用cURL替代file_get_contents
        $htmls = $this->safeCurlRequest($url);
        if ($htmls === false) {
            return json(['error' => '请求失败']);
        }

        $html = new simple_html_dom();
        $html->load($htmls);
        $main  = $html->find(".bet-tb-tr");//胜平负
        $xx    = $html->find(".bet-more-wrap");//其他
        $ogbx = [];
        var_dump($main);
	}

	/**
	 * 验证URL是否安全
	 */
	private function isUrlSafe($url, $allowedHosts = [])
	{
	    $parsedUrl = parse_url($url);

	    if (!$parsedUrl || !isset($parsedUrl['host'])) {
	        return false;
	    }

	    // 检查协议
	    if (!in_array($parsedUrl['scheme'], ['http', 'https'])) {
	        return false;
	    }

	    // 检查主机白名单
	    if (!empty($allowedHosts) && !in_array($parsedUrl['host'], $allowedHosts)) {
	        return false;
	    }

	    // 防止访问内网地址
	    $ip = gethostbyname($parsedUrl['host']);
	    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
	        return false;
	    }

	    return true;
	}

	/**
	 * 安全的cURL请求
	 */
	private function safeCurlRequest($url, $timeout = 10)
	{
	    $ch = curl_init();
	    curl_setopt_array($ch, [
	        CURLOPT_URL => $url,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_TIMEOUT => $timeout,
	        CURLOPT_CONNECTTIMEOUT => 5,
	        CURLOPT_FOLLOWLOCATION => false, // 禁止重定向
	        CURLOPT_MAXREDIRS => 0,
	        CURLOPT_SSL_VERIFYPEER => true,
	        CURLOPT_SSL_VERIFYHOST => 2,
	        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; SafeBot/1.0)',
	        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
	        CURLOPT_MAXFILESIZE => 1024 * 1024 * 5, // 5MB限制
	    ]);

	    $result = curl_exec($ch);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);

	    if ($result === false || $httpCode !== 200) {
	        return false;
	    }

	    return $result;
	}
}