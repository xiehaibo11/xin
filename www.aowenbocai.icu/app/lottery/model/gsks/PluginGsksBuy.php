<?php
namespace app\lottery\model\gsks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginGsksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gsks';
    }
}