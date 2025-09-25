<?php
namespace app\lottery\model\bj28;

use app\lottery\model\common\pc28\Pc28BaseBuy;

class PluginBj28Buy extends Pc28BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bj28';
    }
}