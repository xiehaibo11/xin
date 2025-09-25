<?php
namespace app\lottery\model\ff11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginFf11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff11';
    }

}