<?php
namespace app\lottery\model\jnd28;

use app\lottery\model\common\BaseJoin;

class PluginJnd28Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnd28';
    }
}