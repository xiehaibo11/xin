<?php
namespace app\lottery\model\hub11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginHub11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hub11';
    }

}