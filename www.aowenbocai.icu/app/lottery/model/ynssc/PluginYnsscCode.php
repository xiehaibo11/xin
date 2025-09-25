<?php
namespace app\lottery\model\ynssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginYnsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynssc';
    }
}