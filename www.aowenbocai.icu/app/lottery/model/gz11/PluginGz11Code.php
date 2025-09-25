<?php
namespace app\lottery\model\gz11;

use app\lottery\model\common\BaseCode;

class PluginGz11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gz11';
    }
}