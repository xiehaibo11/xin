<?php

namespace app\animal\model;

use think\Db;
use think\Model;
use think\Validate;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;

class Record extends Model
{
    protected $name = 'game_animal_record';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $code_type;

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getNameAttr($value, $data)
    {
        $animal = (new AnimalConfig())->where('type',$data['animal_type'])->find();
        return empty($animal) ? '未找到' : $animal['title'];
    }

    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['username'];
    }

    /**
     * 获取器 - user_info
     */
    public function getUserInfoAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        $res = [];
        if (!empty($user_info)) {
            $res = [
                'userid' => $user_info['id'],
                'nickname' => $user_info['nickname'],
                'photo' => $user_info['photo'],
            ];
        }
        return $res;
    }

    /**
     * 获取器 - status_text
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '未击杀', 1 => '击杀'];
        return $status[$data['status']];
    }

    public function getPlayInfoAttr($value, $data)
    {
       $animal = (new AnimalConfig())->where('type',$data['animal_type'])->find();
       $_data['animal'] =  empty($animal) ? '未找到' : $animal['title'];
        return $_data;
    }
}
