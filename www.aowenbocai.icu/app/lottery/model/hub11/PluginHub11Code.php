<?php
namespace app\lottery\model\hub11;

use app\lottery\model\common\BaseCode;

class PluginHub11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hub11';
    }
}