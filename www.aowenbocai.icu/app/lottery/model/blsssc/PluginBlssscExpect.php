<?php
namespace app\lottery\model\blsssc;

use app\lottery\model\common\BaseExpect;

class PluginBlssscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'blsssc';
    }
}