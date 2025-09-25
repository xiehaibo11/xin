<?php
namespace app\lottery\model\xjssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginXjsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xjssc';
    }
    

}