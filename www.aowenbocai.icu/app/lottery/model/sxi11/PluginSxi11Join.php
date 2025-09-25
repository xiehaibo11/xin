<?php
namespace app\lottery\model\sxi11;

use app\lottery\model\common\BaseJoin;

class PluginSxi11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sxi11';
    }
}