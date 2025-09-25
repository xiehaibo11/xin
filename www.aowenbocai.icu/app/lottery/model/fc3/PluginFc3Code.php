<?php
namespace app\lottery\model\Fc3;

use app\lottery\model\common\fc3\Fc3BaseCode;

class PluginFc3Code extends Fc3BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fc3';
    }
}