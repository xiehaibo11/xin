<?php
namespace app\admin\model;

use think\Model;
use think\Validate;
use app\admin\model\Log as ALog;

class User extends BaseModel
{
    protected $insert = ['reg_ip'];
    protected $readonly = ['username',"reg_ip"];

    /**
     * 修改器 - reg_ip
     * @return json
     */
    protected function setRegIpAttr($value)
    {
        return request()->ip();
    }


    /**
     * 登录
     * @param  array $data  用户名
     * @return array
     */
    public function login($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require',
            'password|密码'   => 'require'
        ]);
        if (!$validate->check($data)) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['password'] = md5($data['password']);
        $user = $this->where($data)->find();
        if (!$user) {
            return ["code"=>0,"msg"=>"账号密码不正确"];
        }
        $admin=Admin::where(['userid'=>$user['id'],'status'=>1])->find();
        if (!$admin){
            return ["code"=>0,"msg"=>"您没有权限进入后台"];
        }
        $res = $user->toArray();
        session('sid', $res['sid']);
        session('uid', $res['id']);
        (new ALog)->createLog(1,"后台登录");
        return  ["code"=>1];
    }

    /**
     * 添加
     * @param  array $data 表单提交的值
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require|unique:user',
            'password|密码'   => 'require|confirm:' . $data['password2']
        ]);
        $validate->scene('admin', ['username', 'password']);
        $result = $validate->scene('admin')->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['password'] = md5($data['password']);

        $admin = $this->allowField(['username', 'password'])->save($data);

        if (!$admin) {
            return ["code"=>0,"msg"=>"添加失败"];
        }
        $this->save(['userid' => $this->id]);//admin表记录

        if (!$this->update([
            'id' => $this->id,
            'sid' => $this->id . '_' . getRandChar(32)
        ])) {
            return;
        }
        return ["code"=>1,"id"=>$this->id];
    }

    /**
     * 修改
     * @param  array $data  表单提交的值
     * @return array
     */
    public function edit($data)
    {
        $validate = new Validate([
            'password|密码'   => 'confirm:' . $data['password2']
        ]);
        $validate->scene('admin', [ 'password']);
        $allowField=['money', 'status', 'sid', 'update_time'];
        if ($data['password']!=""){
            $result = $validate->scene('admin')->check($data);
            if (!$result) {
                return ["code"=>0,"msg"=>$validate->getError()];
            }
            $data['password'] = md5($data['password']);
            $data['sid']= $data['id'] . '_' . getRandChar(32);
            array_push($allowField,"password");
        }


        $admin = $this->allowField($allowField)->save($data,['id'=>$data['id']]);

        if (!$admin) {
            return ["code"=>0,"msg"=>"修改失败"];
        }
        return ["code"=>1,"id"=>$data['id']];
    }
}
