<?php
namespace app\lottery\model\ffks;

use app\lottery\model\common\BaseCode;

class PluginFfksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffks';
    }
}