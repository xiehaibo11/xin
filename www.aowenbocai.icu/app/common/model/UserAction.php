<?php
namespace app\common\model;

use think\Model;
use app\index\model\User;

class UserAction extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getUserInfoAttr($value, $data)
    {
        $user = User::get($data['userid']);
        return ['nickname' => $user['nickname'], 'photo' =>  $user['photo']];
    }

    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }

    // public function getGoodCountAttr($value,$data)
    // {
    //     new Good;
    //     return 1;
    // }

    /**
     * 用户动态添加
     * @param  array  $data
     * @return array
     */
    public function write($data = [])
    {
        $userid = $data['userid'];
        $user = User::get($userid);
        if (!$user) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }
        $result = $this->validate([
            'userid|用户'  => 'require',
            'content|动态内容' => 'require',
            'ext_name|模块名' => 'alphaDash'
        ])->insert($data);
        if (false === $result) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '动态添加成功'];
    }


}
