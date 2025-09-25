<?php
namespace app\lottery\model\gzks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginGzksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gzks';
    }
}