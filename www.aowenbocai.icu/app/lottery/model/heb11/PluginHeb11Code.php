<?php
namespace app\lottery\model\heb11;

use app\lottery\model\common\BaseCode;

class PluginHeb11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'heb11';
    }
}