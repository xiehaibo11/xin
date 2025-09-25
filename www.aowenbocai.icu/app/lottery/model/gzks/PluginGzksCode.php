<?php
namespace app\lottery\model\gzks;

use app\lottery\model\common\BaseCode;

class PluginGzksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gzks';
    }
}