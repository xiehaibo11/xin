<?php
namespace app\lottery\model\jlssc;

use app\lottery\model\common\BaseJoin;

class PluginJlsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlssc';
    }
}