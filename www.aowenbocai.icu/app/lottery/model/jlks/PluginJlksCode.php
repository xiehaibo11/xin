<?php
namespace app\lottery\model\jlks;

use app\lottery\model\common\BaseCode;

class PluginJlksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlks';
    }
}