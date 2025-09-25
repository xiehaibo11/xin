<?php
namespace app\lottery\model\qqssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginQqsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qqssc';
    }
    

}