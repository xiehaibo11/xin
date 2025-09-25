<?php
namespace app\lottery\model\xy28;

use app\lottery\model\common\pc28\Pc28BaseBuy;

class PluginXy28Buy extends Pc28BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xy28';
    }
}