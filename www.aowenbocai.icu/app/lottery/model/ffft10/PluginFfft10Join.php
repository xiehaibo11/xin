<?php
namespace app\lottery\model\ffft10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginFfft10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffft10';
    }
}
