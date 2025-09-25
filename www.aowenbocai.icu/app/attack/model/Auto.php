<?php

namespace app\attack\model;

use app\index\model\User;
use think\Db;
use think\Model;
use think\Validate;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;

class Auto extends Model
{
    protected $name = 'plugin_attack_auto';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $betting_type;

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['nickname'];
    }


    /**
     * 双方准备后 判断最小投注  生成对局记录
     */
    public function add($data, $user_id)
    {
        $validate = new Validate([
            'win_money|赢取金币' => 'require|number',
            'lose_money|输的金币' => 'require|number'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $has_info = $this->where('userid', $user_id)->find();
        $data['status'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s', time());
        if (empty($has_info)) {
            $res = $this->allowField(['userid','win_money', 'lose_money', 'create_time'])->save($data);
        } else {
            $res = $this->allowField(['win_money', 'lose_money', 'create_time'])->save($data, ['id' => $has_info['id']]);
        }
        if (!$res) {
            return ['code' => 0, 'msg' => '数据执行失败'];
        }
        return ['code' => 1];

    }

    /**
     * 双方准备后 判断最小投注  生成对局记录
     */
    public function delete_info($user_id)
    {
        $has_info = $this->where('userid', $user_id)->find();
        if (empty($has_info)) return ['code' => 1, 'msg' => '数据不存在'];
        if($this->where('userid', $user_id)->delete()){
            return ['code' => 1, 'msg' => '删除成功'];
        } else {
            return ['code' => 0, 'msg' => '删除失败'];
        }
    }



}
