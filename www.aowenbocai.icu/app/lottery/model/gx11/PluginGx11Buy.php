<?php
namespace app\lottery\model\gx11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginGx11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gx11';
    }

}