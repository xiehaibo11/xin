<?php
namespace app\lottery\model\ah11;

use app\lottery\model\common\BaseCode;

class PluginAh11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ah11';
    }
}