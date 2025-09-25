<?php
namespace app\lottery\model\azssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginAzsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'azssc';
    }
    

}