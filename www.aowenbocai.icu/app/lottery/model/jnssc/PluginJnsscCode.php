<?php
namespace app\lottery\model\jnssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginJnsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnssc';
    }
}