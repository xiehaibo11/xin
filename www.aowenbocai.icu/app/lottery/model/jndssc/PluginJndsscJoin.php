<?php
namespace app\lottery\model\jndssc;

use app\lottery\model\common\BaseJoin;

class PluginJndsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jndssc';
    }
}