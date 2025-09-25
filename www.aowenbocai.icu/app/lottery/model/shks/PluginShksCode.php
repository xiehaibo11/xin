<?php
namespace app\lottery\model\shks;

use app\lottery\model\common\BaseCode;

class PluginShksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'shks';
    }
}