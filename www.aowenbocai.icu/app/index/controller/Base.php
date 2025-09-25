<?php
namespace app\index\controller;

use core\Setting;

class Base extends Common
{

    public function __construct()
    {
        parent::__construct();
        $setting = Setting::get(['site_name','lottery_unit', 'moblie_status', 'game_unit']);
        $setting['coin_name'] = $setting['game_unit'];
        $goModule = session('?goModule') ? (session('goModule') ? 2 : 1) : 3;
        $moblieModule = $goModule == 3 ? $setting['moblie_status'] && isMobile() : $setting['moblie_status'] && $goModule == 1;
        if (empty($this->user) && $moblieModule) {
            return  $this->redirect('/index');
        }

        $this->assign('setting', $setting);
        $this->assign('user', $this->user);
        $this->assign('site_name', $setting['site_name']);
    }
}