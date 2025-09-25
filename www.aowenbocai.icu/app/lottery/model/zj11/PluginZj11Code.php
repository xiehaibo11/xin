<?php
namespace app\lottery\model\zj11;

use app\lottery\model\common\BaseCode;

class PluginZj11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'zj11';
    }
}