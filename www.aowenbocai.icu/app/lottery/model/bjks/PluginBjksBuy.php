<?php
namespace app\lottery\model\bjks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginBjksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bjks';
    }
}