<?php
namespace app\cars\model;

use think\Model;
use think\Validate;

class PluginCars extends Model
{
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    public function getPlanArrAttr($value, $data)
    {
        return ['sign' => $data['sign'], 'num' => 1];
    }
    public function getAwards()
    {
        return $this->where('status', 1)->order('sort ASC')->select();
    }

    public function addAward($data)
    {
        $rules = [
            'name' => 'require|chsAlphaNum',
            'multiple' => 'require|number'
        ];

        $validate = new Validate($rules);
        $checked = $validate->check($data);
        if(!$checked){
            return ['err' => 1, 'msg' => $validate->getError()];
        }
        $info = $this->where('name', $data['name'])->find();
        if($info){
            return ['err' => 2, 'msg' => '该物品已存在，请重新输入'];
        }
        $data['sign'] = $this->max('sign') + 1;
        $res = $this->save($data);
        if(!$res){
            return ['err' => 3, 'msg' => '添加失败'];
        }
        return true;
    }
}
