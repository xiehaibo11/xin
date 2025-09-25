<?php
namespace app\lottery\model\blsssc;

use app\lottery\model\common\BaseJoin;

class PluginBlssscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'blsssc';
    }
}