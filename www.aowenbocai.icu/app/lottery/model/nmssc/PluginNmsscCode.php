<?php
namespace app\lottery\model\nmssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginNmsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmssc';
    }
}