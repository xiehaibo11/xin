<?php
namespace app\lottery\model\gxks;

use app\lottery\model\common\BaseExpect;

class PluginGxksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gxks';
    }
}