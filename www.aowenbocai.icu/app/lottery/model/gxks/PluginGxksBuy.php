<?php
namespace app\lottery\model\gxks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginGxksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gxks';
    }
}