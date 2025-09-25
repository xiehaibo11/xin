<?php
namespace app\lottery\model\ln11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginLn11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ln11';
    }

}