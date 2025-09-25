<?php
namespace app\lottery\model\gsks;

use app\lottery\model\common\BaseCode;

class PluginGsksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gsks';
    }
}