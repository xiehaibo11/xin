<?php
namespace app\lottery\model\gxks;

use app\lottery\model\common\BaseCode;

class PluginGxksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gxks';
    }
}