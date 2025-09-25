<?php
namespace app\lottery\model\gz11;

use app\lottery\model\common\BaseJoin;

class PluginGz11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gz11';
    }
}