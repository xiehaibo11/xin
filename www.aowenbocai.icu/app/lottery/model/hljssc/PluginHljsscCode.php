<?php
namespace app\lottery\model\hljssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginHljsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hljssc';
    }
}