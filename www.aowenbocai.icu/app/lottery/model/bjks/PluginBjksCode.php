<?php
namespace app\lottery\model\bjks;

use app\lottery\model\common\BaseCode;

class PluginBjksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bjks';
    }
}