<?php
namespace app\lottery\model\jisks;

use app\lottery\model\common\BaseJoin;

class PluginJisksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jisks';
    }
}