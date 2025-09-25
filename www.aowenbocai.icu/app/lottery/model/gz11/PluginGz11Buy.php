<?php
namespace app\lottery\model\gz11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginGz11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gz11';
    }

}