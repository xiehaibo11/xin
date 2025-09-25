<?php
namespace app\web\controller;

use think\Controller;
use app\common\controller\Wei;
use app\index\controller\Result;
use core\Setting;

class Wlogin extends Controller
{
    public function index($code = '', $state = '')
    {
		$suErrResult = new Result;
		if($state != session("login_csrf")) {
			$suErrResult->errorInfo('操作错误','/index');
		}
        if(!$code) {
            $suErrResult->errorInfo('授权失败');
        }
        $setting = Setting::get(['weiopenappid','weiopenappsecret']);
        // $setting = [
        //     'weiopenappid' => 'wxe79bd3ce56ad914a',
        //     'weiopenappsecret' => '5870c4935cc68ab70a8860f6b5879086'
        // ];
        $appid = $setting['weiopenappid'];/** 公众号的唯一标识 */
        $secret = $setting['weiopenappsecret'];  /**公众号的appsecret */

		
        $WeiCommon = new Wei;
        /**获取网页授权access_token  access_token缓存*/
        if(session("?token") && session('endtime') > time()){
            $token = session('token');
        }else{
            $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code ";
            $token = $WeiCommon->getContent($token_url);
            $token = json_decode($token, true);
            if(!isset($token['errcode']) || !$token['errcode']) {
                session('token',$token);
                $endtime = time() + $token['expires_in'];
                session('endtime', $endtime);
            }
        }
       
        
        /**判断是登录注册还是绑定 */
        $state = explode('_', $state);
        if($state[1] != 0){
            $WeiCommon->lockWei();
        }
        
        $access_token = $token['access_token'];
        $openid = $token['openid'];/**用户唯一标识 */

        /**拉取用户信息 */
        $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
		$user_info = $WeiCommon->getContent($user_url);
        $user_info = json_decode($user_info, true);
        if(!$user_info['openid']) {
            return false;
        }
        $WeiCommon->setWebInfo($user_info, '');
        
    }
}