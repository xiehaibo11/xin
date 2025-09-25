<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use core\Setting;
use app\common\controller\Wei;

class Weilogin extends Controller
{

    /**
     * 用户统一授权后，跳转到当前页面
     * URL 格式  redirect_uri/?code=CODE&state=STATE
     * @param code 作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。 
     * @param state 不是必须 ，传递参数
     */
    public function index($code, $state = 0)
    {
        $setting = Setting::get(['weiappid','weiappsecret']);
        $appid = $setting['weiappid'];/** 公众号的唯一标识 */
        $secret = $setting['weiappsecret'];  /**公众号的appsecret */

        if(!$code) {
            $this->error('授权失败');
        }
        $WeiCommon = new Wei;
        /**获取网页授权access_token  access_token缓存*/
        if(session("?token") && session('endtime') > time()){
            $token = session('token');
        }else{
            $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code ";
            $token = $WeiCommon->getContent($token_url);
            $token = json_decode($token, true);
            if(!$token['errcode']) {
                session('token',$token);
                $endtime = time() + $token['expires_in'];
                session('endtime', $endtime);
            }
        }
       
        
        /**判断是登录注册还是绑定 */
        $state = explode('*', $state);
        if($state[1] != 0){
            $this->lockWei($state[1], $token['openid']);
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
        /**授权成功操作网站信息 */
        $WeiCommon->setWebInfo($user_info, $state[2]);

    }

    /**
     * 进入授权页面 
    */
    public function tocheck($sid = 0, $link = '')
    {
        $setting = Setting::get(['weiappid','weiurl']);
        if(!$setting['weiappid'] || !$setting['weiurl']) $this->errror('该网站未设置微信登录方式','/index/Login');
        $appid = $setting['weiappid'];/** 公众号的唯一标识 */
        $redirect_uri = urlencode($setting['weiurl']); 
        /**type用于判断是绑定还是登录注册*/
        $rand = md5(uniqid(rand(), TRUE));
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$rand."*".$sid."*".$link."#wechat_redirect";
        header("Location:".$url);
    }
    public function load()
    {
        return $this->fetch('login/load', ['title' => '加载中...','type' =>1]);
    }
    
    /**用户锁定微信 */
    public function lockWei($sid, $openid)
    {
        $user = new User;
        $info = $user->get(['sid' => $sid]);
        $wei = $user->get(['wei' => $openid]);
        if(!$info) {
            $this->success('用户信息查询失败', '/index/User/lockInfo');
        }
        if($wei) {
            $this->success('该微信已被绑定', '/index/User/lockInfo');
        }
        $info = $info->toArray();
        if($info['wei']){
            $this->success('您已绑定微信不需再绑定', '/index/User/lockInfo');
        }
        $res = $user->save(['wei' => $openid], ['sid' => $sid]);
        if($res){
            $this->redirect('/index/User/lockInfo');
        }else{
            $this->success('绑定失败', '/index/User/lockInfo');
        }
    }

    public function imgAsccess()
    {
        $setting = Setting::get(['weiappid','weiappsecret']);
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$setting['weiappid']."&secret=".$setting['weiappsecret'];
        $res = json_decode((new Wei)->getContent($url), true);
        if(!$res['errcode']){
            $codeurl = $this->create_code($res['access_token'], 'QR_SCENE', session('uid'));
            return json($codeurl);
        }
		return json($res);
    }

    /**生成二维码 */
    public function create_code($access_token, $name, $id)
    {
        switch($name){
            case  'QR_SCENE':
            $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
                break;
            case 'QR_LIMIT_SCENE':
                $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
                break;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $res = (new Wei)->getContent($url, $data);
        $res = json_decode($res, true);
        $codeUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($res['ticket']);
        return $codeUrl;
    }
}