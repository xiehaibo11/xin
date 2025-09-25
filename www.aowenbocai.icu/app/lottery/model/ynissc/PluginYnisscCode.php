<?php
namespace app\lottery\model\ynissc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginYnisscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynissc';
    }
}