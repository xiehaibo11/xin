<?php
namespace app\lottery\model\mnlssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginMnlsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'mnlssc';
    }
}