<?php
namespace app\lottery\model\jlks;

use app\lottery\model\common\BaseExpect;

class PluginJlksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlks';
    }
}