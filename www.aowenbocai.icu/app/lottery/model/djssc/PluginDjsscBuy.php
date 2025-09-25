<?php
namespace app\lottery\model\djssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginDjsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'djssc';
    }
    

}