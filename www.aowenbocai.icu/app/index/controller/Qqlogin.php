<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use core\Setting;
use app\index\model\User as BUser;
use app\agents\model\Agents;
use app\common\model\MoneyHistory;
use app\index\model\MsgArticle;
use app\index\controller\Result;
use core\Active;

class Qqlogin extends Controller
{

    /**数据回调 --获取access_token*/
    public function index()
    {
		$suErrResult = new Result;
        $data = request()->get();
        /**$data['code'] 存在则获取的是 Authorization Code 否则获取的是access_token*/
        if($data['code']){
            if($data['state'] != session("login_csrf")) {
                $suErrResult->errorInfo('操作错误','/index');
            }
            $csrf = explode('_', $data['state']);

            $setting = Setting::get(['qqurl','qqappid','qqappkey']);
            $url = "https://graph.qq.com/oauth2.0/token";
            $field = "?grant_type=authorization_code&client_id=".$setting['qqappid']."&client_secret=".$setting['qqappkey']."&code=".$data['code']."&redirect_uri=".urlencode($setting['qqurl']);
            $data = $this->getContent($url.$field);

            /**如果出错 */
            if(strpos($data, "callback") !== false){
                $lpos = strpos($data, "(");
                $rpos = strrpos($data, ")");
                $data  = json_decode(substr($data, $lpos + 1, $rpos - $lpos -1), true);
                $suErrResult->errorInfo("error : ".$data['error']." , msg : ".$data['error_description'], '');
            }
            $this->getOpenId($data, $setting, $csrf);
        }
    }

    /**获取用户openid*/
    public function getOpenId($string, $setting, $type)
    {  
        /**返回参数有access_token expires_in	refresh_token */
        parse_str($string, $data);
        $url = "https://graph.qq.com/oauth2.0/me?access_token=".$data['access_token'];
        $opendata = $this->getContent($url);
        $lpos = strpos($opendata, "(");
        $rpos = strrpos($opendata, ")");
        $opendata  = json_decode(substr($opendata, $lpos + 1, $rpos - $lpos -1), true);
		$suErrResult = new Result;
        if($opendata['code']) $suErrResult->errorInfo("code : ".$opendata['code']." , msg : ".$opendata['msg']);
        
        /**用于判断是否是绑定QQ用户 $type 为真进行绑定，为假进行qq用户信息获取*/
        if(!$type[1]){
            $this->getUserInfo($opendata, $data['access_token'], $setting, $type[3]);
        }else{
            $this->lockQq($opendata['openid'], $type[3]);
        }

    }

    /** 获取QQ用户信息*/
    public function getUserInfo($data, $access_token, $setting, $isMoblie)
    {
        $url = "https://graph.qq.com/user/get_user_info?access_token=".$access_token."&oauth_consumer_key=".$setting['qqappid']."&openid=".$data['openid'];
        $info = $this->getContent($url);
        $info = json_decode($info, true);
        $this->webInfo($info, $data['openid'], $isMoblie);
    }

    /**绑定用户QQ */
    public function lockQq($openid, $isMoblie)
    {
		$suErrResult = new Result;
        $user = new BUser;
        $info = $user->get(['sid' => session('sid')]);
        $qq = $user->get(['qq' => $openid]);
        $url = $isMoblie ? '/web/User/my?ac=5' : '/index/User/lockInfo';
        if(!$info) {
            $suErrResult->errorInfo('用户信息查询失败', $url);
        }
        if($qq) {
            $suErrResult->errorInfo('该QQ已被绑定', $url);
        }
        $info = $info->toArray();
        if($info['qq']){
            $suErrResult->errorInfo('您已绑定QQ不需再绑定', $url);
        }
        $res = $user->save(['qq' => $openid], ['sid' => session('sid')]);
        if($res){
            $suErrResult->successInfo('绑定成功', $url);
        }else{
            $suErrResult->errorInfo('绑定失败', $url);
        }
    }

    /**QQ登录注册 */
    public function webInfo($userinfo, $openid, $isMoblie)
    {
		$suErrResult = new Result;
        $user = new BUser;
        $setting = Setting::get(['login_way','reg_way','is_reg','reg_award','reg_msg', 'reg_msg_status','login_award','is_qq_login', 'is_qq_register']);
        $where['qq'] = $openid;
        $res = $user->get($where);
        $login = explode(',',$setting['login_way']);
        $reg = explode(',',$setting['reg_way']);
        /**判断该用户有没有注册 */
        if($res){
            if(!in_array('qq',$login) || $setting['is_qq_login'] != 1) {
                $url = $isMoblie == 1 ? '/web/Login/' : '/index/Login/';
                $suErrResult->successInfo('本网站不支持QQ登录，请使用用户名登录', $url);
            }
            $res = $res->toarray();
            $loginres = $user->wxLogin($where);
            if(!$loginres['code']) {
                $suErrResult->successInfo($loginres['msg']);
            }
            /**登录成功，进行奖励操作*/
            $this->sendLoginPrize($res);
            $active = new Active($res);
            $active->addInterface(6,1);
            if(session("?turnUrl")){
               $this->redirect(session("turnUrl"));
            }
            $url = $isMoblie == 1 ? '/web' : '/index';
            $this->redirect($url);
        }else{
            if($setting['is_reg'] == 0) {
                $url = $isMoblie == 1 ? '/web' : '/index';
                $suErrResult->successInfo('本网站未开启QQ注册功能', $url);
            }
            if(!in_array('qq',$reg)  || $setting['is_qq_register'] != 1) {
                $url = $isMoblie == 1 ? '/web/Reg' : '/index/Reg';
                $suErrResult->successInfo('本网站不支持QQ注册，请使用用户名或手机号注册', $url);
            }
            
            /**$csrf[2] != '' 是代理推荐链接注册 */
            $csrf = explode('_', session("login_csrf")); 
            if($csrf[2] != ''){
                $agentInfo = (new Agents)->get(['link' => $csrf[2]."_".$csrf[3]]);
                if(!$agentInfo){
                    $url = $isMoblie == 1 ? '/web/Reg' : '/index/Reg';
                    $suErrResult->errorInfo('该注册链接代理信息错误',  $url);
                }
                $agentInfo = $agentInfo ->toArray();
            }
            /**=========*/
            $data = [
                'nickname' => $userinfo['nickname'],
                'openid' => $openid,
                'qq' => $openid,
            ];
            
            $res = $user->register($data, $type);
            if(!$res['code']) {
                $suErrResult->errorInfo($res['msg']);
            }

            /**注册成功，更新代理信息 */
            if($csrf[2] != ''){
                (new Agents) ->updateNum($agentInfo['rightNum']-1,2);
                $newSave['agents'] = $agentInfo['username'];
            }

            if($setting['reg_award'] != 0){
                // 注册送奖励数据处理
                $MoneyHistory = new MoneyHistory;
                $regSend = [
                    'userid' =>$res['id'],
                    'money' =>$setting['reg_award'],
                    'ext_name' => 'index/wxlogin/Reg',
                    'remark' => '注册赠送金币',
                    'type' => 4
                ];
                $MoneyHistory ->write($regSend);
            }

            if($setting['reg_msg_status'] == 1){
                // 开启注册消息通知
                $message = ['userid' =>$res['id'],'content' =>$setting['reg_msg']];
                $send = new MsgArticle;
                $send->add($message);
            }
            /**缓存头像 */
            $photoUrl = $userinfo['figureurl_qq_2'] ? $userinfo['figureurl_qq_2'] : $userinfo['figureurl_qq_1'];
            $image = $this->getContent($photoUrl);
            $name = '/uploads/personal/'.$res['id'].'.png';
            file_put_contents('.'.$name,$image);
            $newSave['photo'] = $name;

            $user->update($newSave, ['id' => $res['id']]);
           
           
            if(session("?turnUrl")){
               $this->redirect(session("turnUrl"));
            }
            $url = $isMoblie == 1 ? '/web' : '/index';
            $this->redirect($url);
        }
    }

    
    /**
     * 登录送奖励
     */
    public function sendLoginPrize($info)
    {
        $system = Setting::get(['login_award']);
        if($system['login_award']){
            $MoneyHistory = new MoneyHistory;
            $count = $MoneyHistory->where(['create_time' => ['>=', date("Y-m-d")],'remark' => '登录赠送金币'])->count();
            if(!$count){
                $regSend = [
                    'userid' =>$info['uid'],
                    'money' =>$system['login_award'],
                    'ext_name' => 'index/Login',
                    'remark' => '登录赠送金币',
                    'type' => 4
                ];
                $MoneyHistory ->write($regSend);
            }
        }
    }

    /**curl获取网页信息 */
    public function getContent($url)
    {
        $ch = curl_init();// 创建一个新cURL资源
        curl_setopt($ch , CURLOPT_URL , $url);// 设置URL和相应的选项
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证CURLOPT_SSL_VERIFYHOS
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , 1);// 设置URL和相应的选项
        $data = curl_exec($ch);// 抓取URL并把它传递给浏览器
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $data;
    }

    /**去登录QQ */
    public function toLogin($type = 0, $link = '', $moblie = 0){
        $geturl = "https://graph.qq.com/oauth2.0/authorize";
        $setting = Setting::get(['qqurl','qqappid','qqappkey']);
		$suErrResult = new Result;
        if(!$setting['qqappid'] || !$setting['qqappkey'] || !$setting['qqurl']) $suErrResult->errorInfo('本网站未设置QQ登录方式','/index/Login');
        /**用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回*/
        $csrf = md5(uniqid(rand(), TRUE))."_".$type."_".$link."_".$moblie;
        session('login_csrf',$csrf);
        $field = "?response_type=code&client_id=".$setting['qqappid']."&redirect_uri=".urlencode($setting['qqurl'])."&state=".$csrf;
        $field .= !$moblie ? "&display=mobile" :  "&display=pc";
        header("Location:".$geturl.$field);
    }
}