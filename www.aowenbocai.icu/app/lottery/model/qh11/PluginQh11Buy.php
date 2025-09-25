<?php
namespace app\lottery\model\qh11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginQh11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qh11';
    }

}