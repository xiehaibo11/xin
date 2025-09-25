<?php
namespace app\lottery\model\ssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginSscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ssc';
    }

}