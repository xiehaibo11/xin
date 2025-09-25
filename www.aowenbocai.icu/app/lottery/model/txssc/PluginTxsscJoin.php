<?php
namespace app\lottery\model\txssc;

use app\lottery\model\common\BaseJoin;

class PluginTxsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'txssc';
    }
}