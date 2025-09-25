<?php
namespace app\admin\controller;

use app\admin\model\User;
use think\Config;
use think\Controller;
use core\Setting;

class Base extends Controller
{
    protected $baseModel;
    protected $param;
    protected $id;
    protected $post;
    protected $admin;

    public function _initialize()
    {
        parent::_initialize();
        // $token = Config::get('kurui');
        // if (empty($token) || !$token['token'] || $token['token'] != $this->authorization_token) {
        //     die('您的网站未授权');
        // }
        $admin = User::get(['sid' => session('admin_sid')]);
        if (!empty($admin) and session('admin_sid')) {
            $this->admin = $admin->toArray();
            $login_name = Setting::get(['login_name'])['login_name'];
            $this->assign('loginName', $login_name);
        }else {
            
            $route_path = \think\Request::instance()->path();
            $url = explode('/', $route_path);
            if (isset($url[1]) && $url[1] == 'admin') {
                echo '404 Not Found';
                exit;
            }
            return $this->error("您还没有登录！",url("admin/Login/index"));
        }

    }

    /**
     * 删除
     */
    public function delete()
    {
        $id=$this->id;
        $info=$this->baseModel->get($id);
        if($info->delete()){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }


}
