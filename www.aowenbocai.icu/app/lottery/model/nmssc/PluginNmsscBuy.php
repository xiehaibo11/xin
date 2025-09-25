<?php
namespace app\lottery\model\nmssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginNmsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmssc';
    }
    

}