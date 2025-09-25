<?php
namespace app\lottery\model\hnks;

use app\lottery\model\common\BaseCode;

class PluginHnksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnks';
    }
}