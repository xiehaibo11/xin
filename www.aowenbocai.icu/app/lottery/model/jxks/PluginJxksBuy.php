<?php
namespace app\lottery\model\jxks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginJxksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jxks';
    }
}