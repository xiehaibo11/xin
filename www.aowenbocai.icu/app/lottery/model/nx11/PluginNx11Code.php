<?php
namespace app\lottery\model\nx11;

use app\lottery\model\common\BaseCode;

class PluginNx11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nx11';
    }
}