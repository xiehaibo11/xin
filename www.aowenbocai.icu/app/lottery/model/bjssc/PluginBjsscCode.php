<?php
namespace app\lottery\model\Bjssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginBjsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bjssc';
    }
}