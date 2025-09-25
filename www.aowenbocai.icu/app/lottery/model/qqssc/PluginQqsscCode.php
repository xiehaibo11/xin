<?php
namespace app\lottery\model\qqssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginQqsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qqssc';
    }
}