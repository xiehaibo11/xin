<?php
namespace app\lottery\model\hubks;

use app\lottery\model\common\ks\KsBaseBuy;

class PluginHubksBuy extends KsBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hubks';
    }
}