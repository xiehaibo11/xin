<?php
namespace app\lottery\model\js28;

use app\lottery\model\common\BaseJoin;

class PluginJs28Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js28';
    }
}