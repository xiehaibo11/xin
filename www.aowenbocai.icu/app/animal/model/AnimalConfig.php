<?php

namespace app\animal\model;

use think\Db;
use think\Model;
use think\Validate;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;

class AnimalConfig extends Model
{
    protected $name = 'game_animal_data';//表名
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';
    protected $status_array;

    protected function initialize()
    {
        parent::initialize();
        $this->status_array = [0 => '禁用', 1 => '显示'];
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value,$data)
    {
        $html  = $value == 0 ?  '<font style="color: red;">' .$this->status_array[$value]. '</font>':'<font style="color: #5cb85c;" >' .$this->status_array[$value]. '</font>';
        return $html;
    }

    /**
     *  status选择项
     */
    public function statusOption($value = "")
    {
        $html = '';
        foreach ($this->status_array as $k => $v) {
            $selected = $k == $value ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
        }
        return $html;
    }

    public function edit($data)
    {
        $validate = new Validate([
            'title|名称' => 'require',
            'type|类型' => 'require|unique:game_animal_data',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $res = $this->update($data);//记录开奖号码

        if (!$res) {
            return ["code" => 0, 'msg' => '数据库操作失败'];
        }
        return ["code" => 1, 'msg' => '编辑成功'];
    }

    public function add($data)
    {
        $validate = new Validate([
            'title|名称' => 'require',
            'type|类型' => 'require|unique:game_animal_data',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $res = $this->save($data);
        if (!$res) {
            return ["code" => 0, 'msg' => '数据库操作失败'];
        }
        return ["code" => 1, 'msg' => '添加成功'];
    }



}
