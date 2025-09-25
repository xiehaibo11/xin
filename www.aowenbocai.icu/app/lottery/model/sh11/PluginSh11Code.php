<?php
namespace app\lottery\model\sh11;

use app\lottery\model\common\BaseCode;

class PluginSh11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sh11';
    }
}