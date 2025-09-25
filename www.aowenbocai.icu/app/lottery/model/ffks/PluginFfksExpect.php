<?php
namespace app\lottery\model\ffks;

use app\lottery\model\common\BaseExpect;

class PluginFfksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffks';
    }
}