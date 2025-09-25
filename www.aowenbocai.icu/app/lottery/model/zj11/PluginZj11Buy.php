<?php
namespace app\lottery\model\zj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginZj11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'zj11';
    }

}