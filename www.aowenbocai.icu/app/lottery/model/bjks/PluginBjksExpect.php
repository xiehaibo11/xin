<?php
namespace app\lottery\model\bjks;

use app\lottery\model\common\BaseExpect;

class PluginBjksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bjks';
    }
}