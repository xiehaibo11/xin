<?php
namespace app\lottery\model\sxi11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginSxi11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sxi11';
    }

}