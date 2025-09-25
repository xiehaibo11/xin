<?php
namespace app\lottery\model\js11;

use app\lottery\model\common\BaseCode;

class PluginJs11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js11';
    }
}