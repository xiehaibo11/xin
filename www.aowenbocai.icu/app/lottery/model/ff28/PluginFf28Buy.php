<?php
namespace app\lottery\model\ff28;

use app\lottery\model\common\pc28\Pc28BaseBuy;

class PluginFf28Buy extends Pc28BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff28';
    }
}