<?php
namespace app\index\controller;

use app\common\controller\Count;
use app\index\model\User;
use think\Controller;
use think\Env;
use think\Session;
use core\Setting;

class Common extends Controller
{
    public $user;

    public function __construct()
    {
        parent::__construct();
        $setting = Setting::get(['login_in','login_way','reg_way','pc_status','moblie_status','pc_domain','moblie_domain']);
        $module = \think\Request::instance()->module();
        if (!$setting['login_in']) {
            cookie('login_in_status', 0);
        } else {
            cookie('login_in_status', 1);
        }
        header('Access-Control-Allow-Origin: *');
        //session('sid', '_x53XsUHaNFHzOHi5j8RM5jLWzpy3a2ly');
        $cookie_login = cookie('has_login');
        if (!session('sid') and $cookie_login) {
            $safe_key = Env::get('authorization_token');
            $str        = substr($cookie_login,0,32);
            $sid        = substr($cookie_login,32);
            //校验
            if (md5($sid . $safe_key) == $str) {
                $user = User::get(['sid' => $sid]);
                if (!empty($user)) {
                    session('uid', $user['id']);
                    session('sid', $sid);
                    cookie('bAuth', true);
                }
            }
        }
        !isset($user) ? $user = User::get(['sid' => session('sid')]) : null;
        if(!$setting['pc_status'] and !isMobile()){
            return  $this->redirect(url('index/WebStop/index'));
        }
        /**进入模块 3   1 手机版 2 电脑版*/
        $goModule = session('?goModule') ? (session('goModule') ? 2 : 1) : 3;
        if (!empty($user)) {
            cookie('bAuth', 1);
            if(!$setting['moblie_status'] && $module != 'web' && $goModule != 3){
                $this->redirect('/web');
            }
            if(!$setting['pc_status'] && $module == 'web'){
                return json(['网站升级中，请稍后访问']);
            }
            $request= \think\Request::instance();
            $acname= $request->action();
            if(empty($user->nickname) && $acname!='setnickname'){
                return  $this->redirect(url("index/user/setNickname"));
            }
            Count::init();
            $user->action_time = date('Y-m-d H:i:s');
            $user->save();
            $user->append(['level']);
            $this->user = $user->toArray();
            if(session("?turnUrl")){
                Session::delete('turnUrl');
            }
            return;
        } else {
            cookie('bAuth', null);
            // 为未登录用户设置默认值
            $this->user = [
                'id' => 0,
                'sid' => '',
                'nickname' => '游客',
                'username' => '',
                'money' => '0.00',
                'record' => '0.00',
                'points' => 0,
                'level' => 0,
                'tel' => '',
                'email' => '',
                'extname' => '',
                'spent' => '0',
                'photo' => '/static/images/no-photo.png',
                'agents' => '0.00',
                'award' => '0.00',
                'diamonds' => 0,
                'game_money' => '0.00',
                'recharge_money' => '0.00',
                'qq' => '',
                'wei' => '',
                'sex' => 0,
                'birth' => '',
                'safe_password' => '',
                'type' => 0,
                'rebate' => [
                    'ssc' => 0,
                    'ks' => 0,
                    'syxw' => 0,
                    'pk10' => 0,
                    'pc28' => 0
                ],
                'top_agents' => 0
            ];
        }
        session('turnUrl', $_SERVER['REQUEST_URI']);
        $moblieModule = $goModule == 3 ? $setting['moblie_status'] && isMobile() : $setting['moblie_status'] && $goModule == 1;
        if(!$moblieModule && $module == 'web'){
            $setting['login_in'] && $this->redirect(url("/web/Login"));
            return;
        }

        //判断是什么方式进入网站
        $login_way = explode(',',$setting['login_way']);
        $reg_way = explode(',',$setting['reg_way']);
        if (isWeixin() && (in_array('wei',$login_way) || in_array('wei',$reg_way))) {
            return $this->redirect(url("/index/Weilogin/load"));
            //die;
        }
        if ($moblieModule) {
            //return $this->redirect(url("/index/login"));
        }else{
            return $setting['login_in'] ? $this->redirect(url("/web/Login")) : $this->redirect(url("/web"));
        }
    }
}
