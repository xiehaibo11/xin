<?php
namespace app\lottery\model\hebks;

use app\lottery\model\common\BaseCode;

class PluginHebksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hebks';
    }
}