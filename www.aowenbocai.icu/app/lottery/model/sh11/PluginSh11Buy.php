<?php
namespace app\lottery\model\sh11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginSh11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sh11';
    }

}