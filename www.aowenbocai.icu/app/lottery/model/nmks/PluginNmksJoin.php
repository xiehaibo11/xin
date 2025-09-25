<?php
namespace app\lottery\model\nmks;

use app\lottery\model\common\BaseJoin;

class PluginNmksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmks';
    }
}