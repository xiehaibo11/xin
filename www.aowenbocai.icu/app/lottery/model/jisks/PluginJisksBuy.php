<?php
namespace app\lottery\model\jisks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginJisksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jisks';
    }
}