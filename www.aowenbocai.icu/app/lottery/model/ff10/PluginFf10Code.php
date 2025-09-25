<?php
namespace app\lottery\model\ff10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginFf10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff10';
    }

}
