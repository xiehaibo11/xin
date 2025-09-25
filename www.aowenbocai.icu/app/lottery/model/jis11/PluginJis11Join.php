<?php
namespace app\lottery\model\jis11;

use app\lottery\model\common\BaseJoin;

class PluginJis11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jis11';
    }
}