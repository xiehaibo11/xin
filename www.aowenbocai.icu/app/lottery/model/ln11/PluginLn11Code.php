<?php
namespace app\lottery\model\ln11;

use app\lottery\model\common\BaseCode;

class PluginLn11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ln11';
    }
}