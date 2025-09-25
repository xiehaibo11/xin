<?php
namespace app\lottery\model\common\pk10;

use app\lottery\model\common\BaseJoin;

class Pk10BaseJoin extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
    }

    public function getListByBuyId($buy_id = '', $row = 14)
    {
        if (!empty($buy_id)) {
            $this->where('buy_id', $buy_id);
        }
        $res = $this->order('id', 'ASC')->paginate($row);
        return $res;
    }

}