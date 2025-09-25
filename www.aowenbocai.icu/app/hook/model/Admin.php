<?php
namespace app\admin\model;

use think\Validate;

class Admin extends BaseModel
{
    protected $autoWriteTimestamp = false;//自动更新时间

    public function user()
    {
        return $this->hasOne('User','id',"userid");
    }

    /**
     * 注册
     * @param  array $data  用户名
     * @return array
     */
    public function register($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require',
            'password|密码'   => 'require|confirm:' . $data['password2']
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }

        $data['password'] = md5($data['password']);
        $admin           = new User;
        $admin->username=$data['username'];
        $admin->password=$data['password'];
        $admin->save();
        if (!$admin) {
            return ["code"=>0,"msg"=>"添加失败"];
        }
        if (User::update([
            'id' => $admin->id,
            'sid' => $admin->id . '_' . getRandChar(32)
        ])) {
            $this->save(['userid'=>$admin->id]);
        }
        return ["code"=>1,"id"=>$admin->id];
    }


}
