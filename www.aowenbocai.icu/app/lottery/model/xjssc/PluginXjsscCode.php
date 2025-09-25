<?php
namespace app\lottery\model\xjssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginXjsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xjssc';
    }
}