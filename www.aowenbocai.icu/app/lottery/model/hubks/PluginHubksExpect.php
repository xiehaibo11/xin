<?php
namespace app\lottery\model\hubks;

use app\lottery\model\common\BaseExpect;

class PluginHubksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hubks';
    }
}