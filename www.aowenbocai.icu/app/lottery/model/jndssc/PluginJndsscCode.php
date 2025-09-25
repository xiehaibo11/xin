<?php
namespace app\lottery\model\jndssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginJndsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jndssc';
    }
}