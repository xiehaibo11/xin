<?php
namespace app\lottery\model\gd11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginGd11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gd11';
    }

}