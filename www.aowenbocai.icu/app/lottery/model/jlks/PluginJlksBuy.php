<?php
namespace app\lottery\model\jlks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginJlksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlks';
    }
}