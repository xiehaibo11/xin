<?php
namespace app\fish50\model;

use think\Model;

class PluginFishData extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function fishList()
    {
        return $this->select();
    }
}
