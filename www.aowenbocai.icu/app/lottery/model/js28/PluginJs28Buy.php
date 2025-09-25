<?php
namespace app\lottery\model\js28;

use app\lottery\model\common\pc28\Pc28BaseBuy;

class PluginJs28Buy extends Pc28BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js28';
    }
}