<?php
namespace app\lottery\model\pk10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginPk10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'pk10';
    }

}
