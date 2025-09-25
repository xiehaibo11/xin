<?php
namespace app\lottery\model\fj11;

use app\lottery\model\common\BaseCode;

class PluginFj11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fj11';
    }
}