<?php
namespace app\lottery\model\hn11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginHn11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hn11';
    }

}