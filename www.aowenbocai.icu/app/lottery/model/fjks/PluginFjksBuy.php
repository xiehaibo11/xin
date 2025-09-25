<?php
namespace app\lottery\model\fjks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginFjksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fjks';
    }
}