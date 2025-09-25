<?php
namespace app\lottery\model\hub11;

use app\lottery\model\common\BaseJoin;

class PluginHub11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hub11';
    }
}