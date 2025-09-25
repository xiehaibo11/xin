<?php
namespace app\web\controller;

use app\common\model\InvitationCode;
use think\Controller;
use app\index\model\User;
use app\web\model\User as AUser;
use app\agents\model\Agents;
use app\index\model\MsgArticle;
use app\common\model\MoneyHistory;
use core\Setting;
use core\Sms;
class Reg extends Controller
{
    
    /**
    * 会员注册
    * @return json
    */
   public function index($code='')
   {
    (new Common)->getInit('');
       $system = (new Setting)->get(['tel_checked','is_reg_code']);
       $this->assign('system', collection($system));

       $setting = Setting::get(['reg_way']);
       $way =array_flip(explode(',', $setting['reg_way']));
       $reg_way['common'] = isset($way['common']) ? 1 : 0;
       $reg_way['tel'] = isset($way['tel']) ? 1 : 0;

       return $this->fetch('login/reg', ['title' => '欢迎注册', 'data' => collection($reg_way), 'code' => $code]);
   }

   /**注册数据处理 */
   public function reg()
   {
       if(request()->isPost()){
            $data = request()->post();
            $isLink = isset($data['code']) && $data['code'];
            $codeModel = new InvitationCode();
            if($isLink){
                $code_info = $codeModel->get(['code' => $data['code']]);
                if(!$code_info) {
                    return json(['err' =>3,'msg' =>'邀请码错误']);
                }
            }
           $system = (new Setting)->get(['tel_checked','reg_award','reg_msg', 'reg_msg_status', 'reg_way']);
           $way =array_flip(explode(',', $system['reg_way']));
           $user = new User;
            if (isset($way['tel'])) {
                if(!preg_match("/^1[34578]{1}\d{9}$/", $data['tel'])){
                    return json(['err' => 4, 'msg' => "手机号格式不正确"]);
                }

                $info = $user->getInfoWhere(['tel' => $data['tel']]);
                if($info){
                    return json(['err' =>3,'msg' =>'该手机号已注册']);
                }
            }
            if(isset($way['tel']) && $system['tel_checked'] && isset($data['yzm'])){
                $checksms = Sms::getSmsCode($data['tel'], 'reg', $data['yzm']);
                if($checksms['err'] > 0){
                    return $checksms;
                }
            }
           if (isset($way['qq']) and (!isset($data['qq']) || !$data['qq'])) {
               return json(['err' =>3,'msg' =>'QQ号不能为空']);
           }
            $res = (new AUser)->register($data);
            if ($res['err']) {
                return json($res);
            }
            
            if($isLink){
                $codeModel->isUpdate(true)->save(['num' => $code_info['num'] + 1], ['id' => $code_info['id']]);
                $agents_info = $user->find($code_info['userid']);
                if ($agents_info) {
                    $top_agents_id = $agents_info['top_agents'] ? $agents_info['top_agents'] : $code_info['userid'];
                    $user->save(['agents' => $code_info['userid'], 'top_agents' => $top_agents_id, 'type' => $code_info['type'], 'rebate' => $code_info->getData('rebate')], ['id' => $res['id']]);
                }
            }
            
            if($system['reg_award'] != 0){
                // 注册送奖励数据处理
                $moneyhistory = new MoneyHistory;
                $regSend = [
                        'userid' =>$res['id'],
                        'money' =>$system['reg_award'],
                        'ext_name' => 'index/Reg',
                        'remark' => '注册赠送金币',
                        'type' => 4
                    ];
                $moneyhistory ->write($regSend);
            }
            if($system['reg_msg_status'] == 1){
                // 开启注册消息通知
                $message = ['userid' =>$res['id'],'content' =>$system['reg_msg']];
                $send = new MsgArticle;
                $send->add($message);
            }
            
            return json(['err' =>0, 'msg' =>'注册成功']);
       }
   }
}
