<?php
namespace app\lottery\model\jisks;

use app\lottery\model\common\BaseExpect;

class PluginJisksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jisks';
    }
}