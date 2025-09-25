<?php
namespace app\common\controller;

use think\Controller;
use app\index\model\User;
use core\Setting;
use app\common\model\MoneyHistory;
use app\agents\model\Agents;
use app\index\model\MsgArticle;
use app\index\controller\Result;
use core\Active;
use think\Session;

class Wei extends Controller
{
 
    /**curl获取网页信息 */
    public function getContent($url, $postData = '')
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
    /**
     * 授权成功操作网站信息
     * @param array $userInfo 微信用户信息
     */
    public function setWebInfo($userInfo, $link)
    {
        $loginWay = isMobile();
        $setting = Setting::get(['login_way','reg_way','is_reg','reg_award','reg_msg', 'reg_msg_status','is_wx_login', 'is_wx_register']);
        $data['wei'] = $userInfo['openid'];
        $user = new User;
        $res = $user->getInfoWhere($data);
        $login = explode(',',$setting['login_way']);
        $reg = explode(',',$setting['reg_way']);
        /**
         * $res 为真就是已注册用户，直接进行登录操作
         * $res 为假，该微信用户还未注册网站，进行网站注册操作
        */
        if($res){
            if(!in_array('wei',$login) || $setting['is_wx_login'] != 1) {
                $redirectUrl = !$loginWay ? '/web/Login' : '/index/Login/';
                $this->error('本网站不支持微信登录，请使用用户名登录',$redirectUrl);
            }
            $res = $res->toarray();
            $loginres = $user->wxLogin($data);
            if(!$loginres['code']) {
                $this->error($loginres['msg']);
            }

            /**登录成功，进行奖励操作*/
            $this->sendLoginPrize($res);
            /**判断有没绑定手机号，没绑定手机号的先绑定手机号*/
            //if(!$loginres['msg']['tel'])  $this->success('请先绑定手机号', '/index/User/set_phone');
            // $active = new Active($res);
            // $active->addInterface(6,1);
            if(session("?turnUrl")){
               $this->redirect(session("turnUrl"));
            }
            $redirectUrl = !$loginWay ? '/web' : '/index';
            $this->redirect($redirectUrl);
        }else{
            if($setting['is_reg'] == 0) {
                $redirectUrl = !$loginWay ? '/web/Login' : '/index/Login/';
                $this->success('本网站未开启注册功能',$redirectUrl);
            }
            if(!in_array('wei',$reg)  || $setting['is_wx_register'] != 1) {
                $redirectUrl = !$loginWay ? '/web/reg' : '/index/reg/';
                $this->success('本网站不支持微信注册，请使用用户名或手机号注册', $redirectUrl);
            }
            
            /**过滤微信名 */
            preg_match_all('/[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+/u', $userInfo['nickname'], $regname);
            $nickname = implode("", $regname[0]);
            if(empty($regname)){
                $nickname = substr($userInfo['openid'], 0,8);
            }
            $_data = [
                'openid' => $userInfo['openid'],
                'wei' => $userInfo['openid'],
                'nickname' => $nickname
            ];
            /**$link != '' 是代理推荐链接注册 */
            if(session("?turnUrl")){
                $turnUrl = explode('userRegLink', session("turnUrl"));
                if(count($turnUrl) > 1){
                    $link = ltrim($turnUrl[count($turnUrl) - 1], '='); 
                }
            }
            if($link != ''){
                $agentInfo = (new Agents)->get(['link' => $link]);
                if(!$agentInfo){
                    $this->error('该注册链接代理信息错误','/index/reg/');
                }
                $agentInfo = $agentInfo ->toArray();
            }
            /**=========*/
            $regres = $user->register($_data,0);
            if(!$regres['code']) {
                $this->error($regres['msg']);
            }

            /**注册成功，更新代理信息 */
            if($link != ''){
                (new Agents) ->updateNum($agentInfo['rightNum']-1,2);
                $newSave['agents'] = $agentInfo['username'];
            }
            /**注册成功，进行奖励操作*/
            if($setting['reg_award'] != 0){
                // 注册送奖励数据处理
                $MoneyHistory = new MoneyHistory;
                $regSend = [
                    'userid' =>$regres['id'],
                    'money' =>$setting['reg_award'],
                    'ext_name' => 'index/wxlogin/Reg',
                    'remark' => '注册赠送金币',
                    'type' => 4
                ];
                $MoneyHistory ->write($regSend);
            }
            if($setting['reg_msg_status'] == 1){
                // 开启注册消息通知
                $message = ['userid' =>$regres['id'],'content' =>$setting['reg_msg']];
                $send = new MsgArticle;
                $send->add($message);
            }

            /**缓存头像 */
            $image = $this->getContent($userInfo['headimgurl']);
            $name = '/uploads/personal/'.$regres['id'].'.png';
            file_put_contents('.'.$name,$image);
            $newSave['photo'] = $name;

            $user->update($newSave, ['id' => $regres['id']]);

            if(session("?turnUrl")){
                $this->redirect(session("turnUrl"));
            }
            $redirectUrl = !$loginWay ? '/web' : '/index';
            $this->redirect($redirectUrl);
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
            $count = $MoneyHistory->where(['create_time' => ['>=', date("Y-m-d")],'remark' => '登录赠送金币', 'userid' => $info['id']])->count();
            if(!$count){
                $regSend = [
                    'userid' =>$info['id'],
                    'money' =>$system['login_award'],
                    'ext_name' => 'index/Login',
                    'remark' => '登录赠送金币',
                    'type' => 4
                ];
                $MoneyHistory ->write($regSend);
            }
        }
    }
	
	  /**用户锁定微信 */
    public function lockWei()
    {
		$suErrResult = new Result;
        $user = new User;
        $isMoblie = isMobile();
        $url = $isMoblie ? '/web/User/my?ac=5' : '/index/User/lockInfo';
		if(!session('?sid') || !session('?token')){
            $suErrResult->errorInfo('参数错误', $url);
		}
		$openid= session('token')['openid'];
        $info = $user->get(['sid' => session('sid')]);
        $wei = $user->get(['wei' => $openid]);
        if(!$info) {
            $suErrResult->errorInfo('用户信息查询失败', $url);
        }
        if($wei) {
            $suErrResult->errorInfo('该微信已被绑定', $url);
        }
        $info = $info->toArray();
        if($info['wei']){
            $suErrResult->errorInfo('您已绑定微信不需再绑定', $url);
        }
        $res = $user->save(['wei' => $openid], ['sid' => session('sid')]);
        if($res){
            $suErrResult->successInfo('绑定成功',$url);
        }else{
            $suErrResult->errorInfo('绑定失败', $url);
        }
    }
}