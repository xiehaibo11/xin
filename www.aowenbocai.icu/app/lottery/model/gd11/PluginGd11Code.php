<?php
namespace app\lottery\model\gd11;

use app\lottery\model\common\BaseCode;

class PluginGd11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gd11';
    }
}