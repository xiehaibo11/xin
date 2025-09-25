<?php
namespace app\lottery\model\sx11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginSx11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sx11';
    }

}