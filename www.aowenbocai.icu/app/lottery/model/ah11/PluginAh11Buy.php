<?php
namespace app\lottery\model\ah11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginAh11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ah11';
    }

}