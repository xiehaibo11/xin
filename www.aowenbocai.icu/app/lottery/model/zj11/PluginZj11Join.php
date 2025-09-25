<?php
namespace app\lottery\model\zj11;

use app\lottery\model\common\BaseJoin;

class PluginZj11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'zj11';
    }
}