<?php
namespace app\lottery\model\nmks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginNmksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmks';
    }
}