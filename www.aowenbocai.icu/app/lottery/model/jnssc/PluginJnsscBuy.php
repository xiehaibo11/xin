<?php
namespace app\lottery\model\jnssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginJnsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnssc';
    }
    

}