<?php
namespace app\lottery\model\jlssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginJlsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlssc';
    }
}