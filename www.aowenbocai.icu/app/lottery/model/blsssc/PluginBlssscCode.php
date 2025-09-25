<?php
namespace app\lottery\model\blsssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginBlssscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'blsssc';
    }
}