<?php
namespace app\lottery\model\ffks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginFfksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffks';
    }
}