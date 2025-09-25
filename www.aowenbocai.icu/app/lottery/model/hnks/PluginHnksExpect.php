<?php
namespace app\lottery\model\hnks;

use app\lottery\model\common\BaseExpect;

class PluginHnksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnks';
    }
}