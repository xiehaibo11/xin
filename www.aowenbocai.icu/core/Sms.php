<?php

    namespace core;

    use \org\REST;
    use think\Session;
    class Sms
    {
        /**
          * 发送模板短信
          * @param to 手机号码集合,用英文逗号分开
          * @param varchar $type 信息类型 
          */       
        static public function sendTemplateSMS($tel,$type)
        {     
            if(!preg_match("/^1[3456789]{1}\d{9}$/", $tel)){
                return ['err' => 4, 'msg' => "手机号格式不正确"];
            }
            /**判断用户是否频繁发送短信 */
            $session_type = 'type_'.$type.'_tel_'.$tel;
            if(Session::has($session_type)){
                $smsdata = session($session_type);
                $endSend = $smsdata['endtime'] - 480;
                if(time() < $endSend){
                    return ['err' => 3, 'msg' => "短信发送间隔时间为2分钟,请勿频繁发送"];
                }
            }
            $setting = Setting::get(['accountSid', 'accountToken', 'appId', 'serverIP', 'serverPort', 'softVersion','tempId']);
             // 初始化REST SDK
             $rest = new REST($setting['serverIP'],$setting['serverPort'],$setting['softVersion']);
             $rest->setAccount($setting['accountSid'],$setting['accountToken']);
             $rest->setAppId($setting['appId']);
            $code = rand(1000,9999); 
             $result = $rest->sendTemplateSMS($tel,[$code, 10],$setting['tempId']);
             if($result == NULL ) {
                 return ['err' =>1, 'msg' =>'验证码发送失败'];//原来break
             }
             if($result->statusCode!=0) {
                 return ['err' =>2, 'msg' =>$result->statusMsg[0]];
             }else{
                 // 获取返回信息
                 $smsmessage = $result->TemplateSMS;
                 $sid = (array) $smsmessage->smsMessageSid;
                 $endtime = time() + 600;
                 $session_info = ['tel' =>$tel, 'yzm' =>$code, 'endtime' =>$endtime];
                 session($session_type,$session_info);
                 return ['err' =>0];
             }
        }
        /**
          * 发送模板短信
          * @param to 手机号码集合,用英文逗号分开
          * @param varchar $type 信息类型 
          */    
          
        static public function setSmsCode($tel, $type)
        {
           return self::sendTemplateSMS($tel,$type);
        }

        /**
         * 验证验证码
         */
        static public function getSmsCode($tel, $type,$yzm)
        {
            $setting = Setting::get(['tel_checked']);
            if (!$setting['tel_checked']) return ['err' => 0];
            $smskey = 'type_'.$type.'_tel_'.$tel;
            if(!session($smskey)) {
                return ['err' =>1 ,'msg' => '验证码不正确'];
            }
            $smsdata = session($smskey);
            if($smsdata['endtime'] < time()) {
                return ['err' =>2 ,'msg' =>'验证码超时'];
            }

            if($smsdata['yzm'] != $yzm){
                return ['err' =>3 ,'msg' =>'验证码不正确'];
            }
            return ['err' => 0, 'smskey' => $smskey];
        }
    }
?>