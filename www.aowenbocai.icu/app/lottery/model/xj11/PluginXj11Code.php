<?php
namespace app\lottery\model\xj11;

use app\lottery\model\common\BaseCode;

class PluginXj11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xj11';
    }
}