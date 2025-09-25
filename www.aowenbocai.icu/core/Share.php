<?php
namespace core;

class Share
{ 
    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    public function getSignPackage($lasturl = '') {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER['HTTP_HOST'].$lasturl;
    
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
    
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    
        $signature = sha1($string);
    
        $signPackage = array(
          "appId"     => $this->appId,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        return $signPackage; 
      }
    
      private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
      }
    
      private function getJsApiTicket() {
        if(session("?jsapi_ticket") && session("jsapi_endtime") > time()){
          $ticket = session("jsapi_ticket");
        }else{
          $accessToken = $this->getAccessToken();
          // 如果是企业号用以下 URL 获取 ticket
          // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
          $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
          $res = json_decode($this->httpGet($url), true);
          $ticket = $res['ticket'];
          if(!$res['errcode']){
            session("jsapi_ticket", $ticket);
            $endtime = time() + $res['expires_in'];
            session("jsapi_endtime", $endtime);
          }
        }
        return $ticket;
      }
    
      private function getAccessToken() {
        if(session("?share_wx") && session("share_endtime") > time()){
            $access_token = session("share_wx");
        }else{
          // 如果是企业号用以下URL获取access_token
          // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
          $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
          $res = json_decode($this->httpGet($url), true);
          session("share_wx", $res['access_token']);
          $endtime = time() + $res['expires_in'];
          session("share_endtime", $endtime);
          $access_token = $res['access_token'];
        }
        return $access_token;
      }
    
      /**curl获取网页信息 */
      private function httpGet($url, $postData = '') 
      {
        $ch = curl_init();// 创建一个新cURL资源
        curl_setopt($ch , CURLOPT_URL , $url);// 设置URL和相应的选项
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证CURLOPT_SSL_VERIFYHOS
        if($postData){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , 1);// 设置URL和相应的选项
        $data = curl_exec($ch);// 抓取URL并把它传递给浏览器
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $data;
    }
}