<?php
namespace app\lottery\model\txssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginTxsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'txssc';
    }
    

}