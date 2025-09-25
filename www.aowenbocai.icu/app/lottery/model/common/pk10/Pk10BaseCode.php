<?php
namespace app\lottery\model\common\pk10;

use app\lottery\model\common\BaseCode;

class Pk10BaseCode extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
    }

    public function histryCode($row = 1)
    {
        $res = $this->order('id', 'desc')->paginate($row);
        return $res;
    }

}