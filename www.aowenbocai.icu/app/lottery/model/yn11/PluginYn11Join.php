<?php
namespace app\lottery\model\yn11;

use app\lottery\model\common\BaseJoin;

class PluginYn11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'yn11';
    }
}