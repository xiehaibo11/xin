<?php
namespace app\lottery\model\nmks;

use app\lottery\model\common\BaseExpect;

class PluginNmksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmks';
    }
}