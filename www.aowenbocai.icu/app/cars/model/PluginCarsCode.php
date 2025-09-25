<?php
namespace app\cars\model;

use think\Model;
use think\Validate;

class PluginCarsCode extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getShowCodeAttr($value, $data)
    {
        $code = json_decode($data['code'], true);
        $name = (new PluginCars)->where('sign', $code)->column('name');
        return empty($name) ? '该标识已不存在' : $name[0];
        echo "</br>";
        print_r($code);
    }

    public function addOpen($data)
    {
        $rule = [
            'code'=>'require',
            'issue'=>'require',
        ];
        $validata = new Validate($rule);
        $check = $validata->check($data);
        if(!$check){
            return ['err' => 2, 'msg' => $validata->getError()]; 
        }

        return $this->save($data);
    }

    public function getList()
    {
        $data = request()->get();
        if(!empty($data['name'])){
            $sign = (new PluginCars)->where('name', 'like', "%".$data['username']."%")->column("id");
            $this->where('sign', 'in', $sign);
        }
        if(!empty($data['issue'])){
            $this->where('issue', $data['issue']);
        } 
        if(!empty($data['starttime'])){
            $this->where('create_time', ">=", $data['starttime']);
        } 

        if(!empty($data['endtime'])){
            $this->where('create_time', "<=", $data['endtime'].' 29:59:59');
        }
        
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC', 'bouns DESC']; 
            $this->order($sort[$data['sort']]);
        }
        return $this->order('id DESC')->paginate(15, false,['query' => $data]);
    }
}
