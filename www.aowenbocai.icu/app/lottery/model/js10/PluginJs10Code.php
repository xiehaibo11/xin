<?php
namespace app\lottery\model\js10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginJs10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js10';
    }

}
