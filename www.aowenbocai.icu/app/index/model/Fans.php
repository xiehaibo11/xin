<?php
namespace app\index\model;

use think\Model;

class Fans extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - user
     * @return string
     */
    public function getUserAttr ($value,$data)
    {
        if ($data['userid']) {
            $userid = $data['userid'];
        } else {
            $userid = $data['to_userid'];
        }
        $userinfo=(new User())->getInfo($userid);
        if ($userinfo['code']){
            return $userinfo['data'];
        }
        return [];
    }

    /**
     * 获取关注/粉丝数
     * @param int $userid 用户id
     * @param bool $fans 查看用户的粉丝或者关注 （true 粉丝）
     * @return int
     */
    static public function getCount($userid, $fans = 0) {
        $field = $fans ? 'to_userid' : 'userid';
        return self::where($field, $userid)->count();
    }
}
