<?php
namespace app\admin\controller;

use app\admin\model\User;
use think\Controller;
use think\Session;

class Login extends Controller
{
    public function __construct()
    {
        $this->baseModel  = new User();//当前模型
        parent::__construct();
    }

    /**
     * 登录
     * @return json
     */
    public function index()
    {
        if (request()->isPost()) {
            $res = $this->baseModel->login(request()->post());
            if (!$res['code']) {
                return $this->error($res['msg']);
            } else {
                return $this->success('登录成功', url('/index'));
            }
        }
        return $this->fetch('index', ['title' => '后台登陆']);
    }

    /**获取验证码 */
    public function getVerify()
    {   header("Content-Type: image/png");
        $config =    [
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
    /**
     * 退出登录
     * @return json
     */
    public function logout()
    {
        Session::delete('admin_sid');
        $this->redirect(url("admin/login/index"));
    }

    public function reg()
    {
        return $this->baseModel->register(['username' => 'asdasd', 'password' => 'asdsad', 'password2' => 'f']);
    }

}
