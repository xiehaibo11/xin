<?php
namespace app\lottery\model\azssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginAzsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'azssc';
    }
}