<?php
namespace app\lottery\model\jndssc;

use app\lottery\model\common\BaseExpect;

class PluginJndsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jndssc';
    }
}