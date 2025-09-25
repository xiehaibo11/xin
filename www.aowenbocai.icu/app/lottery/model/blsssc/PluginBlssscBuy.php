<?php
namespace app\lottery\model\blsssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginBlssscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'blsssc';
    }
    

}