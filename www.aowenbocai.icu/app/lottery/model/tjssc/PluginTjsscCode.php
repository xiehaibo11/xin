<?php
namespace app\lottery\model\tjssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginTjsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tjssc';
    }
}