<?php
namespace app\cars\model;

use think\Model;
use think\Validate;
use app\admin\model\User;

class PluginCarsHis extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**获取器--获取投注标识 */
    public function getCodeListAttr($value, $data)
    {
        return json_decode($data['code'], true);
    }
    /**续投 获取最后一期投注记录 */
    public function getCodeAttr($value, $data)
    {
        $code = json_decode($data['code'], true);
        $newCode= [];
        foreach ($code as $key => $value) {
            $key = $key ? $key : 10000;
            $newCode[$key] = $value;
        }
        return json_encode($newCode);
    }
    /**获取器- 获取投注列表*/
    public function getShowCodeAttr($value, $data)
    {
        $code = json_decode($data['code'], true);
        $str = '';
        foreach ($code as $key => $value) {
            $name = (new PluginCars)->where('sign', $key)->column('name');
            $str .= empty($name) ? '该物品不存在' : $name[0];
            $str .= '=>'.$value." ";
        }
        return $str;
    }

    public function getPlayInfoAttr($value, $data)
    {
        $code = json_decode($data['code'], true);
        $str = '';
        foreach ($code as $key => $value) {
            $name = (new PluginCars)->where('sign', $key)->column('name');
            $str .= empty($name) ? '该物品不存在' : $name[0];
            $str .= ":".$value." ";
        }
        $_data['code'] = $str;
        $open = (new PluginCarsCode)->get(['issue'=> $data['issue']]);
        $_data['open'] = '';
        if ($open) {
            $open = $open->append(['show_code'])->toArray();
            $_data['open'] = $open['show_code'];
        }
        return $_data;
    }

    public function getUserInfoAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            $user = $user->toArray();
            return ['nickname' => $user['nickname'], 'username' => $user['username']];
        }
        return ['nickname' => '该会员不存在', 'username' => '...'];
    }
    public function addData($data)
    {
        $rule = [
            'userid'=>'require|number',
            'code'=>'require',
            'money'=>'require',
            'issue'=>'require',
        ];
        $validata = new Validate($rule);
        $check = $validata->check($data);
        if(!$check){
            return ['err' => 1, 'msg' => $validata->getError()];
        }

        return $this->save($data);
    }


    public function getList($where = '')
    {
        $data = request()->get();
        if($where != ''){
            $this->where($where);
        }
        if(!empty($data['username'])){
            $userid = (new User)->where('username|nickname', 'like', "%".$data['username']."%")->column("id");
            $this->where('userid', 'in', $userid);
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

        if(isset($data['name']) && $data['name'] != ''){
            $this->where('name', $data['name']);
        }
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC', 'bouns DESC'];
            $this->order($sort[$data['sort']]);
        }
        return $this->order('id DESC')->paginate(15, false,['query' => $data]);
    }
}
