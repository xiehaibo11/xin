<?php
namespace app\lottery\model\jndssc;

use app\lottery\model\common\ssc\SscBaseBuy;

class PluginJndsscBuy extends SscBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jndssc';
    }
    

}