<?php
namespace app\lottery\model\xy28;

use app\lottery\model\common\BaseCode;

class PluginXy28Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xy28';
    }
}