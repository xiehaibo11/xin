<?php
namespace app\lottery\model\ssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginSscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ssc';
    }
}