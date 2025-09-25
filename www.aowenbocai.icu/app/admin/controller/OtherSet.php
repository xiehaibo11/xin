<?php
namespace app\admin\controller;

use think\Controller;
use core\Setting;

class OtherSet extends Base
{
    public function index()
    {
        
        $setting = Setting::get(['accountSid', 'accountToken', 'appId', 'serverIP', 'serverPort', 'softVersion','tempId', 'tel_checked']);
        $this->assign('setting', $setting);
        return $this->fetch('index',['title' => '其他设置']);
    }

    public function set($data)
    {
        $config = json_decode($data, true);
        if(isset($config['smptpass']) && $config['smptpass'] == '******'){
            unset($config['smptpass']);
        }
        $setting = Setting::set($config);        
        return ['code' => 1];
    }

    /**快速登录接口信息 */
    public function qqConnect()
    {
        $setting = Setting::get(['qqurl', 'qqappkey', 'qqappid', 'weiurl', 'weiappsecret', 'weiappid', 'weiopenurl', 'weiopenappsecret', 'weiopenappid', 'wxsm_checked', 'wx_checked','qq_checked']);
        $this->assign('setting', $setting);
        return $this->fetch('qq',['title' => '其他设置']);
    }

    /**分享接口 */
    public function shareImg()
    {
        $setting = Setting::get(['share_title','share_desc','share_link','share_img','is_share']);
        $this->assign('setting', $setting);
        return $this->fetch('share',['title' => '分享设置']);
    }
    /**支付接口信息 */
    public function paySet()
    {
        $setting = Setting::get(['mch_id', 'weiUrl', 'weinotify']);
        $this->assign('setting', $setting);
        return $this->fetch('payset',['title' => '其他设置']);
    }

    /**彩票设置 */
    public function lottery()
    {
        $setting = Setting::get(['openBd','Bdpercent','isAuto', 'isGain', 'openDaley','lottery_unit', 'star_num', 'join_isOpen', 'unit_isOpen', 'rebate_isOpen', 'lottery_status', 'mode2_min_money',
        'mode_unit_value', 'rebate_max', 'max_chase']);
        if ($setting['mode_unit_value']) {
            $setting['mode_unit_value'] = explode(',', $setting['mode_unit_value']);
        }
        $this->assign('setting', $setting);
        return $this->fetch('', ['title' => '彩票设置']);
    }

    /**游戏设置 */
    public function game()
    {
        $setting = Setting::get(['game_status', 'free_coupon_coin', 'game_unit']);
        $this->assign('setting', $setting);
        return $this->fetch('', ['title' => '彩票设置']);
    }

    public function emails()
    {
        $setting = Setting::get(['email_checked','smptserver', 'smptuser', 'smptusermail','smptpass','smptport']);
        $setting['smptpass'] = '******';
        $this->assign('setting', $setting);
        return $this->fetch('');
    }

}
