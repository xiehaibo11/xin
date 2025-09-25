<?php
namespace app\lottery\model\yn11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginYn11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'yn11';
    }

}