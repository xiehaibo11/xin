<?php
namespace app\lottery\model\jxks;

use app\lottery\model\common\BaseExpect;

class PluginJxksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jxks';
    }
}