<?php
namespace app\lottery\model\fjks;

use app\lottery\model\common\BaseCode;

class PluginFjksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fjks';
    }
}