<?php
namespace app\lucky\model;

use think\Model;
use think\Validate;

class PluginLuckyAward extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getPlanArrAttr($value, $data)
    {
        $plan = json_decode($data['plan'], true);
        if(isset($data['type'])){
            $result = array_unique($plan);
        }else{
            $result = array_count_values($plan);
        }
        return $result;
    }

    public function getPlanListAttr($value, $data)
    {
        $plan = json_decode($data['plan'], true);
        $ids = array_unique($plan);
        $info = (new PluginLucky)->field('sign, name')->where('sign', 'in', $ids)->select();
        if(!$info){
            return '其他,其他,其他';
        }
        $info = $info->toArray();
        $id_name = ['0'=> '其他'];
        foreach ($info as $key => $value) {
            $id_name[$value['sign']] = $value['name'];
        }
        $new = array_count_values($plan);
        $name = '';
        foreach ($new as $key => $value) {
            $name .= $id_name[$key].',';
            if($value > 1){
                $num = $value - 1; 
                for($i = 0; $i < $num; $i++){
                    $name .= $id_name[$key].',';
                }
            }
        }
        return rtrim($name, ',');
    }
    public function getStatusNameAttr($value, $data)
    {
        return $data['type'] == 1 ? '幸运奖' : '普通奖';
    }
    public function getList($type = 0, $where = '')
    {
        $data = request()->get();
        if($where != ''){
            $this->where($where);
        }

        if($type == 1){
            return $this->where('status' ,1)->paginate(15, false, ['query' => $data]);
        }
        return $this->order('id DESC')->paginate(15, false, ['query' => $data]);
    }

    public function addAward($data)
    {
        $rule = [
            'type' => 'require|number|length:1',
            'one' => 'require|number',
            'two' => 'require|number',
            'multiple' => 'require|number',
            'sp' => 'number',
        ];
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if(!$result){
            return ['err' => 1, 'msg' => $validate->getError()];
        }
        $save = [
            'type' => $data['type'],
            'multiple' => $data['multiple'],
        ];
        if($data['type'] == 1){
            if($data['one'] != $data['two']){
                return ['err' =>1 ,'msg' => '幸运奖必须两个都相同'];
            }
            if(!$this->get($data['pid'])){
                return ['err' =>2 ,'msg' => '上级不存在'];
            }
            $save['plan'] = json_encode([$data['one'], $data['two']]);
            $save['sp'] = $data['sp'];
            $save['sort'] = $data['pid'];
        }else{
            $new = [$data['one'], $data['two'], $data['three']];
            $_new = array_unique($new);
            if(count($_new) == 3){
                return ['err' =>1 ,'msg' => '至少两个选项相同'];
            }
            $save['plan'] = json_encode($new);
        }
        return $this->save($save);
    }

    public function getAllAward($type = 0)
    {
        $com = $this->field('plan, multiple')->where('status', 1)->where('type', 0)->select();
        if($type == 1){
            $com->append(['planList']);
        }else{
            $com->append(['planArr']);
        }
        $com = $com ? ($com->toArray()) : [];
        $luck =  $this->field('plan, multiple,type')->where('status', 1)->where('type', 1)->select();
        if($type == 1){
            $luck->append(['planList']);
        }else{
            $luck->append(['planArr']);
        }
        $luck = $luck ? ($luck->toArray()) : [];
        return ['com' => $com, 'luck' => $luck];
    }
}
