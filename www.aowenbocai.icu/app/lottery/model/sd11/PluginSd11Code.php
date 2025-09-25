<?php
namespace app\lottery\model\sd11;

use app\lottery\model\common\BaseCode;

class PluginSd11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sd11';
    }
}