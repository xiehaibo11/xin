<?php
namespace app\lottery\model\jxks;

use app\lottery\model\common\BaseCode;

class PluginJxksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jxks';
    }
}