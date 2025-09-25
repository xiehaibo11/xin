<?php
namespace app\lottery\model\jnd28;

use app\lottery\model\common\BaseCode;

class PluginJnd28Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnd28';
    }
}