<?php
namespace app\lottery\model\ff11;

use app\lottery\model\common\BaseCode;

class PluginFf11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff11';
    }
}