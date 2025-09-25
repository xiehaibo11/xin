<?php
namespace app\lottery\model\gx11;

use app\lottery\model\common\BaseCode;

class PluginGx11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gx11';
    }
}