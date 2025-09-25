<?php
namespace app\lottery\model\nx11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginNx11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nx11';
    }

}