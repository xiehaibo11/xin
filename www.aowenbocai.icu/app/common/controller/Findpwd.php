<?php
namespace app\common\controller;

use think\Controller;
use app\web\model\User;
use core\Sms;
use core\Setting;
use app\common\controller\Email;

class Findpwd extends Controller
{

    /**
     * 账号验证
     */ 
     public function checkName()
     {
        if(request()->isPost()){
            session('?findPwd') && session('findPwd', null);
            $data =request()->post();
            $username = $data['username'];
            $setting = (new Setting)->get(['tel_checked', 'email_checked']);
            $user = (new User)->where('username|tel|email', $username)->find();
            if(!$user){
                 return json(['err' => 1, 'msg' => '用户不存在']);
            }
            $info['tel'] = $user->tel && $setting['tel_checked'] ? substr_replace($user->tel, '****', 3, 4) : '';
            $info['email'] = $setting['email_checked'] ? $user->email : '';
            if($info['email']){
                $emailArr = explode('@', $info['email']);
                $len = mb_strlen($emailArr[0]);
                $info['email'] = substr_replace($emailArr[0], '****', 2, $len-4)."@".$emailArr[1];
            }
            if($info['email'] || $info['tel']){
                session('findPwd', [
                    'email' => $user->email,
                    'tel' => $user->tel
                ]);
            }
            return json(['err' => 0, 'msg' => '验证成功' ,'data' => $info]);
        }
     }

    /**
     * 发送短信验证码
     */
     public function sendTelMsg()
     {
        if(request()->isGet()){
            if(!session('?findPwd') && !session('findPwd')['tel']){
                return json(['err' => 1, 'msg' => '操作出错<01>']);
            }
            $res = Sms::setSmsCode(session('findPwd')['tel'], 'find_pwd');
            return $res;
        }
     }

    /**
     * 发送邮箱验证码
     */
     public function sendEmailMsg()
     {
         if(request()->isGet()){
            if(!session('?findPwd') && !session('findPwd')['email']){
                return json(['err' => 1, 'msg' => '操作出错<02>']);
            }
            $res = (new Email)->send(session('findPwd')['email'], 'find_pwd', '找回密码');
            return json($res);
         }
     }

    /**
     * 安全验证 提交
     */
     public function checkYzm()
     {
        if(request()->isPost()){
            $data = request()->post();

            if(!isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '请输入验证码']);
            }
            $checked = 0;
            /**邮箱验证 */
            if($data['changeWay'] == 2){
                $checked = 1;
                $where['email'] = session('findPwd')['email'];
                $res = (new Email)->checkSend(session('findPwd')['email'], 'find_pwd', $data['yzm']);
                if($res['err']){
                    return json($res);
                }
            } 
            /**手机验证*/
            if($data['changeWay'] == 1){
                $checked = 1;
                $where['tel'] = session('findPwd')['tel'];
                $res = sms::getSmsCode(session('findPwd')['tel'], 'find_pwd', $data['yzm']); 
                if($res['err']){
                    return json($res);
                }
                session($res['smskey'], null);
            }
            if(!$checked){
                return json(['err' => 1, 'msg' => '验证方式出错']);
            }
            $user = (new User)->where($where)->find();
            if($user){
                session('findPwd', null);
                session('findPerson', $where);
                return json(['err' => 0, 'msg' => '验证成功']); 
            }
            return json(['err' => 0, 'msg' => '该验证方式下，没有对应的用户']); 
        }
     }

     /**
      * 重置密码
      */
      public function resetPwd()
      {
          if(request()->isPost()){
              $data = request()->post();
              if(!session('?findPerson')){
                  return json(['err' => 1, 'msg' => '操作错误<03>']);
              }

              if($data['pwd'] != $data['pwd2']){
                return json(['err' => 1, 'msg' => '两次密码输入不一致']);
              }
              $usermodel = new User;
              $user = $usermodel->where(session('findPerson'))->find();
              if(!$user){
                  return json(['err' => 1, 'msg' => '操作错误<001>']);
              }
              $password = md5($data['pwd']);
              if($password == $user->password){
                  return json(['err' => 1, 'msg' => '新密码不能与之前密码一样']);
              }
              $res = $usermodel->where('id' , $user->id)->setField('password', $password);
              if($res){
                  session('findPerson', null);
                  return json(['err' => 0, 'msg' => '密码找回成功']);
              }
              return json(['err' => 1, 'msg' => '密码重置失败']);

          }
      }
}
