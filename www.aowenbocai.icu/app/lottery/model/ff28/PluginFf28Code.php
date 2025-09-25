<?php
namespace app\lottery\model\ff28;

use app\lottery\model\common\BaseCode;

class PluginFf28Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff28';
    }
}