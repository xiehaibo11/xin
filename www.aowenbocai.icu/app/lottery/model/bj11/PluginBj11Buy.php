<?php
namespace app\lottery\model\Bj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginBj11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bj11';
    }

}