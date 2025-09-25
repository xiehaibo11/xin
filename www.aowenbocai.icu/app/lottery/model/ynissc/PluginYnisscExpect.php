<?php
namespace app\lottery\model\ynissc;

use app\lottery\model\common\BaseExpect;

class PluginYnisscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynissc';
    }
}