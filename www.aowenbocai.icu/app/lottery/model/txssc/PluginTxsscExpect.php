<?php
namespace app\lottery\model\txssc;

use app\lottery\model\common\BaseExpect;

class PluginTxsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'txssc';
    }
}