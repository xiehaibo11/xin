<?php
namespace app\lottery\model\hnssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginHnsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnssc';
    }
    

}