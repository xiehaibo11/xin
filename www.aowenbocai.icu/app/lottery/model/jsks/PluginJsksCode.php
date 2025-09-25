<?php
namespace app\lottery\model\jsks;

use app\lottery\model\common\BaseCode;

class PluginJsksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsks';
    }
}