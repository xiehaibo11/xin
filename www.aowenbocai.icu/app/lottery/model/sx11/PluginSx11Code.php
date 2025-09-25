<?php
namespace app\lottery\model\sx11;

use app\lottery\model\common\BaseCode;

class PluginSx11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sx11';
    }
}