<?php
namespace app\lottery\model\nm11;

use app\lottery\model\common\BaseJoin;

class PluginNm11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nm11';
    }
}