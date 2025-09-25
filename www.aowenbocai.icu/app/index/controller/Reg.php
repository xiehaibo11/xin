<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;
use app\agents\model\Agents;
use app\index\model\MsgArticle;
use app\common\model\MoneyHistory;
use core\Setting;
use think\Session;
use core\Sms;

class Reg extends Controller
{
    public function __construct()
    {
        $this->baseModel  = new User();//当前模型
        parent::__construct();
    }

    /**
     * 注册
     */
    public function index($link = 'index')
    {
        // $link = request()->get()['link'];
        //判断是什么方式进入网站
        $setting = Setting::get(['reg_way']);
        $reg_way = explode(',',$setting['reg_way']);
        if (isWeixin() && in_array('wei',$reg_way)) {
            $link = $link == 'index' ? '' : $link;
            (new Weilogin)->tocheck('', $link);
            die;
        }
       $this->assign('link',$link);
       return $this->fetch('index', ['title' => '会员注册']);
    }
   

    /**
     * 获取注册方式
     */
    public function getType(){
        $setting = Setting::get(['reg_way','is_reg','is_tel_register','is_wx_register','is_qq_register']);
        $way =array_flip(explode(',', $setting['reg_way']));
        $reg_way['common'] = isset($way['common']) ? 1 : 0;
        $reg_way['tel'] = isset($way['tel']) && $setting['is_tel_register'] ? 1 : 0;
        $reg_way['wx'] = isset($way['wei']) && $setting['is_wx_register'] ? 1 : 0;
        $reg_way['qq'] = isset($way['qq']) && $setting['is_qq_register'] ? 1 : 0;

        return json(['err' => 0, 'data' => ['is_reg' => $setting['is_reg'], 'reg_way' => $reg_way]]);
    }

    /***
     * 注册---修改之后（手机号注册）
     */
    public function reg()
    {
        if(request()->post()){
            $data = input('post.');
            $user = new User;

            if(isset($data['link']) && $data['link'] != 'index'){
                $agent = new Agents;
                $agentinfo = $agent->get(['link' => $data['link']]);
                if(!$agentinfo) {
                    return json(['err' =>3,'msg' =>'注册链接有误，请确认']);
                }
            }

            $info = $user->getInfoWhere(['tel' =>$data['tel']]);
            if($info){
                return json(['err' =>3,'msg' =>'该手机号已注册']);
            }

            $checksms = Sms::getSmsCode($data['tel'], 'reg', $data['yzm']);
            if($checksms['err'] > 0){
                return $checksms;
            }
            $res = $user ->register(['tel' =>$data['tel']]);
            if(!$res['code']) return $res;
              
            if(isset($data['link']) && $data['link'] != 'index'){
                $agentinfo = $agentinfo ->toArray();
                $agent ->updateNum($agentinfo['rightNum']-1,2);
                $user->save(['agents' => $agentinfo['username']], ['id' => $res['id']]);
            }

            $setting = Setting::get(['reg_award','reg_msg', 'reg_msg_status']);
            if($setting['reg_award']){
                // 注册送奖励数据处理
                $moneyhistory = new MoneyHistory;
                $regSend = [
                        'userid'=>$res['id'],
                        'money' =>$setting['reg_award'],
                        'ext_name' => 'index/Reg',
                        'remark' => '注册赠送'
                    ];
                $moneyhistory ->write($regSend);
            }
            if($setting['reg_msg_status'] == 1){
                // 开启注册消息通知
                $message = ['userid' =>$res['id'],'content' =>$setting['reg_msg']];
                $send = new MsgArticle;
                $send->add($message);
            }
            
            /**删除短信Session*/
            Session::delete($checksms['smskey']);
            return json(['err' =>0, 'msg' =>'注册成功']);
        }
        return $this->fetch('index', ['title' => '会员注册']);
    }


    /**
     * 注册--之前
     * @return json
     */
    public function reg_before()
    {
        if (request()->isPost()) {
            $data = input('post.');
           // 如果是通过代理链接注册
           if($data['link'] != 'index'){
                $agent = new Agents;
                $info = $agent->get(['link' => $data['link']]);
                if(!$info) {
                    return json(['err' =>3,'msg' =>'注册链接有误，请确认']);
                }
            }
            $res = $this->baseModel->register_before($data);
            if (!$res['code']) {
                return $this->error($res['msg']);
            }
            
            if($data['link'] != 'index'){
                $info = $info ->toArray();
                $agent ->updateNum($info['rightNum']-1,2);
                (new User)->save(['agents' => $info['username']], ['id' => $res['id']]);
            }
            
            $setting = Setting::get(['reg_award','reg_msg', 'reg_msg_status']);
            if($setting['reg_award'] != 0){
                // 注册送奖励数据处理
                $moneyhistory = new MoneyHistory;
                $regSend = [
                        'userid' =>$res['id'],
                        'money' =>$setting['reg_award'],
                        'ext_name' => 'index/Reg',
                        'remark' => '注册赠送',
                        'type' => 4
                    ];
                $moneyhistory ->write($regSend);
            }
            if($setting['reg_msg_status'] == 1){
                // 开启注册消息通知
                $message = ['userid' =>$res['id'],'content' =>$setting['reg_msg']];
                $send = new MsgArticle;
                $send->add($message);
            }
            
            return json(['err' =>0, 'msg' =>'注册成功']);
        }
        return $this->fetch('index', ['title' => '会员注册']);

    }
   

    /**
     * 发送验证码
     */
    public function sendsms()
    {
        if(request()->get()){
            $tel = input('get.tel');
            $user = new User;
            $info = $user->getInfoWhere(['tel' =>$tel]);
            if($info) return ['err' =>1,'msg' =>'该手机号已注册'];
            $type = input('get.type');
            $res = Sms::setSmsCode($tel , 'reg');
            return $res;
        }
    }
}
