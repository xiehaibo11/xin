<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Validate;

class Prop extends BaseModel
{
    protected $autoWriteTimestamp = false;//自动更新时间
    protected $resultSetType = 'collection';
    protected function initialize()
    {
        parent::initialize();
        $this->type_array = [
            1 => '技能卡',
            2 => '道具',
            3 => '入场券',
        ];
    }

    /**
     * 获取器
    */
    public function getTypeTxtAttr($value, $data)
    {
        return $data['type'] ? $this->type_array[$data['type']] : '没有类型';
    }

    /**
     * 获取器
     */
    public function getExtInfoAttr($value, $data)
    {
        if($data['ext_name'] == 'index/Shop'){
            return '商城兑换';
        }
        if($data['ext_name'] == '/game/poker/play'){
            return '扑克';
        }
        $ext_name = ['/'.$data['ext_name'], $data['ext_name']];
        $info = (new ExtShowList)->get(['name' => ['in' , $ext_name]]);
        if($info){
            $info = $info->toArray();
            return $info['title'];
        }else{
            return $data['ext_name'];
        }
    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * @return json
     */
    public function add($data)
    {
        if (isset($data['id'])) {
            $rule = [
                'name|名称' => 'require',
            ];
        } else {
            $rule = [
                'name|名称' => 'require',
                'param_name|变量' => 'require|unique:prop'
            ];
        }
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if (isset($data['id'])) {
            $res = $this->allowField(['type', 'ext_name', 'name', 'img_url', 'desc'])->where('id', $data['id'])->update($data);
        } else {
            $res = $this->allowField(['type', 'ext_name', 'name', 'img_url', 'param_name', 'desc'])->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "添加失败"];
        }
        return ["code" => 1];
    }

}
?>