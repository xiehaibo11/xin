<?php
namespace app\lottery\model\tjssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginTjsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tjssc';
    }
    

}