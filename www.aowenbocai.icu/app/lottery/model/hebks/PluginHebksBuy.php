<?php
namespace app\lottery\model\hebks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginHebksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hebks';
    }
}