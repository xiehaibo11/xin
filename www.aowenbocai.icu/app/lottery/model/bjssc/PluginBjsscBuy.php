<?php
namespace app\lottery\model\Bjssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginBjsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bjssc';
    }
    

}