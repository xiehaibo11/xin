<?php
namespace app\lottery\model\hlj11;

use app\lottery\model\common\BaseCode;

class PluginHlj11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hlj11';
    }
}