<?php
namespace app\admin\model;

use think\Model;

class Log extends BaseModel
{
    protected $updateTime = false;
    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['username'];
    }

    /**
     * 创建行为日志
     * @param  string       $remark    日志备注
     * @param  string       $type      日志类型  默认为1  登录日志  2 修改密码
     * @return array
     */
    public function  createLog($type="1",$remark){
        $data   = [
            'create_ip'  => request()->ip(),
            'url'        => request()->pathinfo(),
            'remark'     => $remark,
            'userid'     => session('uid'),
            'type'       => $type
        ];
        $this->save($data);//存入数据库
    }

}
