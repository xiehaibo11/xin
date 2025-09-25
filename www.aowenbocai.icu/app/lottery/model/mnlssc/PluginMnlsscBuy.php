<?php
namespace app\lottery\model\mnlssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginMnlsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'mnlssc';
    }
    

}