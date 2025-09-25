<?php
namespace app\lottery\model\jx11;

use app\lottery\model\common\BaseCode;

class PluginJx11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jx11';
    }
}