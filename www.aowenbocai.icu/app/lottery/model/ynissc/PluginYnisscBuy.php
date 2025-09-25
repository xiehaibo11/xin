<?php
namespace app\lottery\model\ynissc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginYnisscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynissc';
    }
    

}