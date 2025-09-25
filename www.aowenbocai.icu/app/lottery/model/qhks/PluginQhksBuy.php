<?php
namespace app\lottery\model\qhks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginQhksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qhks';
    }
}