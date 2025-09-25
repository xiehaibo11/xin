<?php
namespace app\fish50\model;

use think\Model;

class Setting extends Model
{

    protected $name = 'plugin_fish_setting';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function add($data)
    {
        foreach ($data as $key => $v){
            $has_info = $this->where('name', $key)->find();
            if (!empty($has_info)) {
                $this->where('name', $key)->update(['value' => $v]);
            }
        }
        return ['code' => 1];
    }

}
