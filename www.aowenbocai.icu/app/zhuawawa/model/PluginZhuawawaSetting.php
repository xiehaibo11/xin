<?php
namespace app\zhuawawa\model;

use think\Model;

class PluginZhuawawaSetting extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getList()
    {
        return $this->order('bet_money', 'asc')->select();
    }

    public function getGiftList($level = 0)
    {
        if ($level > 0) {
            $this->where('bet_money', $level);
        }
        return $this->field(['id', 'title', 'icon'])->select();
    }
    public function getGift()
    {
        return $this->field(['id', 'title'])->where('status', 1)->select();
    }
}
