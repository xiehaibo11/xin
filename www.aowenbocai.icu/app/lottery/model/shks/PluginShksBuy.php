<?php
namespace app\lottery\model\shks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginShksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'shks';
    }
}