<?php
namespace app\lottery\model\gs11;

use app\lottery\model\common\BaseCode;

class PluginGs11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gs11';
    }
}