<?php
namespace app\lottery\model\ahks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginAhksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ahks';
    }
}