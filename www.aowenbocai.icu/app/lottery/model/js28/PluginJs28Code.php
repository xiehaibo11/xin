<?php
namespace app\lottery\model\js28;

use app\lottery\model\common\BaseCode;

class PluginJs28Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js28';
    }
}