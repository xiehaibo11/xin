<?php
namespace app\lottery\model\sd11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginSd11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sd11';
    }

}