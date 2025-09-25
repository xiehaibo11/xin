<?php
namespace app\lottery\model\hnks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginHnksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnks';
    }
}