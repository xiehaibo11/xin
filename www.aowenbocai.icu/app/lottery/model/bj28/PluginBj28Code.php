<?php
namespace app\lottery\model\bj28;

use app\lottery\model\common\BaseCode;

class PluginBj28Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bj28';
    }
}