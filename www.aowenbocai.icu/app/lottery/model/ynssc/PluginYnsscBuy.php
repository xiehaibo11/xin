<?php
namespace app\lottery\model\ynssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginYnsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynssc';
    }
    

}