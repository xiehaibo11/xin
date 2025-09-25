<?php
namespace app\lottery\model\jnd28;

use app\lottery\model\common\pc28\Pc28BaseBuy;

class PluginJnd28Buy extends Pc28BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnd28';
    }
}