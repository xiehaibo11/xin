<?php
namespace app\lottery\model\ahks;

use app\lottery\model\common\BaseCode;

class PluginAhksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ahks';
    }
}