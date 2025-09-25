<?php
namespace app\common\controller;

use app\admin\model\User;
use think\Config;
use think\Controller;

class Base extends Controller
{
    protected $authorization_ip;//授权ip
    protected $authorization_key;//授权的key
    protected $authorization_token;//token

    public function _initialize()
    {
        $this->authorization_ip = Config::get('baseConfig.authorization_ip');
        $this->authorization_key = Config::get('baseConfig.authorization_key');
        $this->authorization_token = Config::get('baseConfig.authorization_token');
        $server_ip = $this->serverIP();
        if ($server_ip != $this->authorization_ip) {
            // die('您的网站未授权,服务器IP:'.$server_ip);
        }
    }
    private function serverIP(){
        return gethostbyname($_SERVER['SERVER_ADDR']);
    }
}
