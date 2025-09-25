<?php
namespace app\lottery\model\jsks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginJsksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsks';
    }
}