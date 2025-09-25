<?php
namespace app\lottery\model\hubks;

use app\lottery\model\common\BaseJoin;

class PluginHubksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hubks';
    }
}