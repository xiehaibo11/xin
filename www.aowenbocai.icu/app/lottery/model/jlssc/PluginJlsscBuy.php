<?php
namespace app\lottery\model\jlssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginJlsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlssc';
    }
    

}