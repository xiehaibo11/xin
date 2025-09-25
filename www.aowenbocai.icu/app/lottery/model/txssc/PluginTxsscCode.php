<?php
namespace app\lottery\model\txssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginTxsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'txssc';
    }
}