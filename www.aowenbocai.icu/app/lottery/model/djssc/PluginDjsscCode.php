<?php
namespace app\lottery\model\djssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginDjsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'djssc';
    }
}