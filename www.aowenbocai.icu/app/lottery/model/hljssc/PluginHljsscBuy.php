<?php
namespace app\lottery\model\hljssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginHljsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hljssc';
    }
    

}