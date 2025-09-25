<?php
namespace app\lucky\model;

use think\Model;
use think\Validate;

class PluginLucky extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getList($userid)
    {
        $this->where('userid', $userid);
        $res = $this->order('id', 'desc')->paginate(20);
        return $res;
    }
    public function addLucky($data)
    {
        $rule = ['name'=>'require|chsAlphaNum'];
        $validata = new Validate($rule);
        $check = $validata->check($data);
        if(!$check){
            return ['err' => 2, 'msg' => $validata->getError()]; 
        }
        $info  = $this->where('name', $data['name'])->find();
        if($info){
            return ['err' => 3, 'msg' => '该物品已存在，请重新输入'];
        }
        $maxSign = $this->max('sign');
        $data['sign'] = $maxSign + 1; 
        $res = $this->save($data);
        if($res){
            return ['err' => 0, 'msg' => '添加成功'];
        }
        return ['err' => 4, 'msg' => '添加失败'];
    }
}
