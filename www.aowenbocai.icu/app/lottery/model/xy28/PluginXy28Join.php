<?php
namespace app\lottery\model\xy28;

use app\lottery\model\common\BaseJoin;

class PluginXy28Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xy28';
    }
}