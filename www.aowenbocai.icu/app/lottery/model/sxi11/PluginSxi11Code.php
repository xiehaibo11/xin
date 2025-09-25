<?php
namespace app\lottery\model\sxi11;

use app\lottery\model\common\BaseCode;

class PluginSxi11Code extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sxi11';
    }
}