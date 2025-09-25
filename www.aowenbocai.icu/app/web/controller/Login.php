<?php
namespace app\web\controller;

use think\Controller;
use think\Session;
use app\web\model\User;
use app\index\controller\Qqlogin;
use core\Setting;

class Login extends Controller
{
    /**
     * 会员登陆
     * @return json
     */
    public function index()
    {
        
        (new Common)->getInit('');
        $setting = Setting::get(['login_way','tel_checked','qq_checked','wx_checked','is_reg', 'wxsm_checked']);
        $way =array_flip(explode(',', $setting['login_way']));
        $login_way['common'] = isset($way['common']) ? 1 : 0;
        $login_way['tel'] = isset($way['tel']) && $setting['tel_checked'] ? 1 : 0;
        $login_way['wx'] = isset($way['wei']) && $setting['wx_checked'] ? 1 : 0;
        $login_way['wxsao'] = isset($way['wei']) && $setting['wxsm_checked'] ? 1 : 0;
        $login_way['qq'] = isset($way['qq']) && $setting['qq_checked'] ? 1 : 0;
        return $this->fetch('index', ['title' => '欢迎登录', 'data' => collection($login_way)]);
    }

    /**
     * 退出登录
     * @return json
     */
    private function logout_com()
    {
        Session::delete('uid');
        Session::delete('sid');
        cookie('bAuth',null);
        cookie('has_login',null);
    }

    /**
     * 退出登录
     * @return json
     */
    public function logout()
    {
        $this->logout_com();
        return $this->redirect(url('web/login/index'));
    }

    /**
     * 退出登录
     * @return json
     */
    public function wap_logout()
    {
        $this->logout_com();
        return json(['err' => 0, '退出成功']);
    }

    public function Login()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = (new User)->loginName($data);
            if (!$res['err'])  return json($res);
            $this->sendLoginPrize($res['data']);
            $active = new Active($res['data']);
            $active->addInterface(6,1);
            $returnData = ['err' =>0, 'msg' =>'登录成功','url' =>"/web",'nickanme' => $res['data']['nickname'],'money' => $res['data']['money']];
            if(session("?turnUrl")){
                $returnData['url'] = session("turnUrl");
            }
            return json($returnData);
        }
    }
    
	/**判断用户是否已经登录 */
    public function checkLogin()
    {
        if(session("?sid")){
            $user = User::get(['sid' => session('sid')]);
            return json(['err' => 0, 'nickname' => $user->nickname, 'money' => $user->money]);
        }
        return json(['err' => 1, 'msg' => '您还未登录，请先登录']);
    }

    /**微信登录 */
    public function weiLogin($link = '')
    {
        $setting = Setting::get(['weiopenappid','weiopenurl']);
        $csrf = md5(uniqid(rand(), TRUE))."_0_".$link;
        session('login_csrf',$csrf);
        $setting['state'] = $csrf;
		$setting['weiopenurl'] = urlencode($setting['weiopenurl']);
        return $this->fetch('wei', ['title' => '微信登录', 'data' => $setting]);
    }
    /**QQ登录 */
    public function qq($link = '')
    {
        (new Qqlogin)->toLogin(0, $link,1);
    }
}
