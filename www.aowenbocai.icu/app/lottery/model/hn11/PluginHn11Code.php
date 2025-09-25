<?php
namespace app\lottery\model\hn11;

use app\lottery\model\common\BaseCode;

class PluginHn11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hn11';
    }
}