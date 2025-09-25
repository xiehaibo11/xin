<?php

namespace core;

use think\Session;
use core\Setting;
use think\Validate;
class Smtp
{
    static public  function sendEmail($email, $type, $title)
    {
        return self::emailDeals($email, $type, $title);
    }

    static public  function emailDeals($email, $type, $title)
    {
        /**验证邮箱是否正确 */
        $validate = new Validate([
            'email|邮箱'   => 'require|email',
        ]);

        $result = $validate->check(['email' => $email]);
        if (!$result) {
            return ["err"=>1, "msg"=>$validate->getError()];
        }
        
        $setting = Setting::get(['email_checked','smptserver', 'smptuser', 'smptusermail','smptpass','smptport']);
        if(!$setting['email_checked']){
            return json(['err' => 1, 'msg' => '网站还未开启邮箱验证']);
        }
        
        /**判断用户是否频繁发送邮箱 */
        $session_emailtype = 'type_'.$type.'_email_'.$email;
        if(Session::has($session_emailtype)){
            $smsdata = session($session_emailtype);
            $endSend = $smsdata['endtime'] - 480;
            if(time() < $endSend){
                return ['err' => 3, 'msg' => "邮箱发送间隔时间为2分钟,请勿频繁发送"];
            }
        }

        //引入文件
        vendor('PHPMailer.phpmailer');
        vendor('PHPMailer.smtp');  
        $mail= new \PHPMailer();
        if($setting['smptport'] && $setting['smptport'] != 25){
            $mail->SMTPSecure = 'ssl';
            $mail->Port = $setting['smptport'];
        }
        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet='UTF-8';
        // // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($email);
        // // 设置邮件正文
        $code = rand(100000,999999); 
        $mail->Body='您的验证码是'.$code.',10分钟内有效，如非本人操作请忽略。（提示：请勿泄露给他人！）';
        // // 设置邮件头的From字段。//发件人
        $mail->From = $setting['smptusermail'];
        // // 设置发件人名字
        $mail->FromName = $setting['smptuser'];
        // // 设置邮件标题
        $mail->Subject=$title.'验证码';
        // // 设置SMTP服务器。
        $mail->Host = $setting['smptserver'];
        // // 设置为"需要验证"
        $mail->SMTPAuth=true;
        // // 设置用户名和密码。
        $mail->Username = $setting['smptusermail'];
        $mail->Password = $setting['smptpass'];
        // 发送邮件。
        if ($mail->Send()) {
            $session_info = ['email' =>$email, 'yzm' =>$code, 'endtime' =>(time()+600)];
            session($session_emailtype,$session_info);
            return ['err' => 0, 'msg' => '发送成功'];
        } else {
            return ['err' => 1, 'msg' => '发送失败'];
 
        }
    }
    
    /**
     * 验证验证码
     */
    static public function checkEmailCode($email, $type,$yzm)
    {
        $setting = Setting::get(['email_checked']);
        if (!$setting['email_checked']) return ['err' => 0];
        $smskey = 'type_'.$type.'_email_'.$email;
        if(!session($smskey)) {
            return ['err' =>1 ,'msg' => '验证码不正确'];
        }
        $smsdata = session($smskey);
        if($smsdata['endtime'] < time()) {
            // return ['err' =>2 ,'msg' =>'验证码超时'];
        }

        if($smsdata['yzm'] != $yzm){
            return ['err' =>3 ,'msg' =>'验证码不正确'];
        }
        return ['err' => 0, 'smskey' => $smskey];
    }

    /**发送邮件消息--用于后台设置 */
    static public function adminSendEmail($email, $title, $content)
    {
        return self::emailDeals($email, $title, $content);
    }

    static public function adminEmail($email, $title, $content)
    {
        /**验证邮箱是否正确 */
        $validate = new Validate([
            'email|邮箱'   => 'require|email',
            'title|标题'   => 'require',
            'content|内容'   => 'require',
        ]);

        $result = $validate->check($data);
        if (!$result) {
            return ["err"=>1, "msg"=>$validate->getError()];
        }
        
        $setting = Setting::get(['email_checked','smptserver', 'smptuser', 'smptusermail','smptpass']);
        if(!$setting['email_checked']){
            return json(['err' => 1, 'msg' => '网站还未开启邮箱验证']);
        }
        
        //引入文件
        vendor('PHPMailer.phpmailer');
        vendor('PHPMailer.smtp');  
        $mail= new \PHPMailer();
        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet='UTF-8';
        // // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($email);
        // // 设置邮件正文
        $mail->Body=$content;
        // // 设置邮件头的From字段。//发件人
        $mail->From = $setting['smptusermail'];
        // // 设置发件人名字
        $mail->FromName = $setting['smptuser'];
        // // 设置邮件标题
        $mail->Subject=$title;
        // // 设置SMTP服务器。
        $mail->Host = $setting['smptserver'];
        // // 设置为"需要验证"
        $mail->SMTPAuth=true;
        // // 设置用户名和密码。
        $mail->Username = $setting['smptusermail'];
        $mail->Password = $setting['smptpass'];
        // 发送邮件。
        if ($mail->Send()) {
            return ['err' => 0, 'msg' => '发送成功'];
        } else {
            return ['err' => 1, 'msg' => '发送失败'];
 
        }
    }
    
}
