<?php
namespace app\common\controller;

use core\Setting;
use think\Controller;
use core\Smtp;
use app\admin\model\User;
use app\agents\model\Agents;

class Email extends Controller
{
    public function send($email, $type, $title)
    {
        $setting = Setting::get(['email_checked']);
        if (!$setting['email_checked']) return ['err' => 0];
        $res = (new User)->where('email', $email)->find();
        $typeArr = ['register', 'lock', 'new_email'];
        if($res && in_array($type, $typeArr)){
            return ['err' => 1 , 'msg' => '该邮箱已存在，请重新输入'];
        }
        if(!$res && !in_array($type, $typeArr)){
            return ['err' => 1 , 'msg' => '该邮箱不存在，请确认'];
        }
        $res = Smtp::sendEmail($email, $type, $title);
        return $res;
    }

    public function checkSend($email, $type, $yzm = '')
    {
        $setting = Setting::get(['email_checked']);
        if(strlen($yzm) != 6 and $setting['email_checked']){
            return ['err' => 1, 'msg' => '验证码错误01'];
        }
        $typeArr = ['register', 'lock', 'new_email'];
        $res = (new User)->where('email', $email)->find();
        if($res && in_array($type, $typeArr)){
            return ['err' => 1 , 'msg' => '该邮箱已存在，请重新输入'];
        }
        if(!$res && !in_array($type, $typeArr)){
            return ['err' => 1 , 'msg' => '该邮箱不存在，请确认'];
        }
        $res = Smtp::checkEmailCode($email, $type, $yzm);
        if(!$res['err']){
            isset($res['smskey']) ? session($res['smskey'], null) : null;
            $res['msg'] = '验证正确';
        }
        return $res;
    }

}