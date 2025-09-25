<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;
use app\index\model\Agents;
use core\Active;
use app\common\model\MoneyHistory;
use core\model\sendmsg;
use think\Session;
use app\admin\model\Log as ALog;
use core\Setting;
use core\Sms;
use app\web\model\User as webUser;

class Login extends Controller
{
    /**
     * AJAX登录
     * @return json
     */
    
    public function load()
    {
        return $this->fetch('index', ['title' => '会员登陆']);
    }

    public function index()
    {
        return $this->fetch('load', ['title' => '加载中...','type' => '']);
    }
    
    // 代理登录
    public function agent()
    {
        if(session('agentname') && session('agentId')) $this->redirect('Agents/index');
       return $this->fetch('agent', ['title' => '代理登陆']);
    }


    /**
     * 找回密码
    */
    public function reset()
    {
        return $this->fetch('',['title' => '充值密码']);
    }

    public function isAjax()
    {
        $get_page = request()->header()['get_page'];
        if (!$get_page && request()->isAjax()) {
            return true;
        }
        return false;
    }
    /**修改之后 */
    public function getWays(){
        $setting = Setting::get(['login_way','tel_checked','qq_checked','wx_checked','is_reg', 'wxsm_checked']);
        $way =array_flip(explode(',', $setting['login_way']));
        $login_way['common'] = isset($way['common']) ? 1 : 0;
        $login_way['tel'] = isset($way['tel']) && $setting['tel_checked'] ? 1 : 0;
        $login_way['wx'] = isset($way['wei']) && $setting['wx_checked'] ? 1 : 0;
        $login_way['wxsao'] = isset($way['wei']) && $setting['wxsm_checked'] ? 1 : 0;
        $login_way['qq'] = isset($way['qq']) && $setting['qq_checked'] ? 1 : 0;

        return json(['err' => 0, 'data' => $login_way, 'is_reg' => $setting['is_reg']]);
    }
    /**
     * 手机号登录
     * ==========流程========= 
     *1 验证改手机号是否已经注册 
     *2 验证手机号和验证码是否匹配
     *3 验证手机验证码是否过期
     *4 验证手机验证码是否正确
     * @return 
     */
    public function login()
    {
        if (request()->post()){
            $data = input('post.');
            $info = (new webUser)->where('tel', $data['tel'])->find();
            if(!$info) {
                return json(['err' =>1,'msg' =>'该手机号还未注册，请先注册']);
            }

            $info = $info->toarray();
            $checksms = Sms::getSmsCode($data['tel'],'login', $data['yzm']);
            if($checksms['err'] > 0){
                return $checksms;
            }
            $this->sendLoginPrize($info);
            session('sid', $info['sid']);
            session('uid', $info['id']);
    
            (new ALog)->createLog(1, "会员登录");
            
            /**删除短信Session*/
            Session::delete($checksms['smskey']);
            $active = new Active($this->user);
            $active->addInterface(6,1);
            $url = isMobile() ? '/index' : '/web';
            $returnData = ['err' =>0, 'msg' =>'登录成功','url' => $url,'nickanme' => $info['nickname'],'money' => $info['money']];
            if(session("?turnUrl")){
                $returnData['url'] = session("turnUrl");
            }
            return json($returnData);
        }
        return $this->fetch('index', ['title' => '会员登录']);
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


     /**
     * 用户名登录
     * @return 
     */
    public function loginbyname()
    {
        if (request()->isPost()) {
            $data = request()->post();

            // 验证码验证
            if (isset($data['captcha'])) {
                $captcha = new \captcha\Captcha();
                if (!$captcha->check($data['captcha'])) {
                    return json(['err' => 1, 'msg' => '验证码错误']);
                }
            }

            $res = (new webUser)->loginName($data);
            if($res['err'])  return json($res);
            $this->sendLoginPrize($res['data']);
            $active = new Active($res['data']);
            $active->addInterface(6,1);
            $url = isMobile() ? '/index' : '/web';
            $returnData = ['err' =>0, 'msg' =>'登录成功','url' => $url,'nickanme' => $res['data']['nickname'],'money' => $res['data']['money']];
            if(session("?turnUrl")){
                $returnData['url'] = session("turnUrl");
            }
            return json($returnData);
        }
        return $this->fetch('index', ['title' => '会员登陆']);
    }
    
    /**
     * 代理登录
     * @return json
     */
    public function agentLogin()
    {
        if ($this->isAjax()) {
            $agent = new Agents;
            $res = $agent->login(request()->post());
            if (!$res['code']) {
                $this->error($res['msg'],url("index/Login/agent"));
            }
            return $this->success('登录成功', 'Agents/index');
        }
        return $this->fetch('index', ['title' => '代理登陆']);
    }

    /**
     * 退出登录
     * @return json
     */
    public function logout()
    {
        Session::delete('uid');
        Session::delete('sid');
        cookie('bAuth',null);
        cookie('has_login', null);
        return $this->redirect(url('index/Login/index'));
    }


    /**
     * 发送验证码
     */
    public function sendSms()
    {
        if(request()->get()){
            $data = request()->get();
            $user = new User;
            $info = $user->getInfoWhere(['tel' =>$data['tel']]);
            if(!$info) {
                return json(['err' =>1,'msg' =>'该手机号还未注册，请先注册']);
            }
            $res = Sms::setSmsCode($data['tel'] , 'login');
            return $res;
        }
    }

    public function resetPassword()
    {
        
    }

    public function getCompanyConfig()
    {
        $setting = Setting::get(['company','company_person','company_qq','company_email','company_wx', 'company_img']);
        return json(['err' => 0,'data' => $setting]);
    }

    /**设置电脑版手机版切换 */
    public function changePc($value)
    {
       $value = abs(intval($value)) >= 1 ? 1 : 0;
        session('goModule', $value);
    }

    /**获取验证码 */
    public function getVerify()
    {
        header("Content-Type: image/png");
        $config = [
            // 验证码图片高度
            'imageH'   => 34,
            // 验证码图片宽度
            'imageW'   => 110,
            // 验证码字体大小(px)
            'fontSize' => 14,
            'length' => 4
        ];
        $captcha = new \captcha\Captcha($config);
        return $captcha->entry();
    }
}
