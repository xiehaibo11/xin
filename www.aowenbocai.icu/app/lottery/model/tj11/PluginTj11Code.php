<?php
namespace app\lottery\model\tj11;

use app\lottery\model\common\BaseCode;

class PluginTj11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tj11';
    }
}