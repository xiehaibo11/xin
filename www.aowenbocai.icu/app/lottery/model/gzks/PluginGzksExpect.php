<?php
namespace app\lottery\model\gzks;

use app\lottery\model\common\BaseExpect;

class PluginGzksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gzks';
    }
}