<?php
namespace app\common\model;

use think\Model;
use app\index\model\User;
use app\admin\model\ExtShowList;
use think\Validate;

class InvitationCode extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取器 - Rebate
     * @return array
     */
    public function getRebateAttr($value)
    {
        $data = $value ? json_decode($value, true) : [];
        return $data;
    }

    /**
     * 获取器 - Rebate
     */
    public function getNickNameAttr($value, $data)
    {
        $user = User::get($data['userid']);
        return $user['nickname'] . '(' . $user['nickname'] .')';
    }

    /**
     * 获取器 - Rebate
     */
    public function getTypeTxtAttr($value, $data)
    {
        $type_array = [
            1 => '普通会员',
            2 => '代理会员',
        ];
        return $type_array[$data['type']];
    }

    /**
     * 生成邀请码
     */
    public function create_invite_code()
    {
        return mt_rand(1000000, 9999999);
    }

        /**
     * 添加
     * @param  array  $data
     * @return array
     */
    public function add($data = [], $user, $id = '')
    {
        $validate = new Validate([
            'type|会员类型' => 'require',
            'rebate|返点' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if (!is_array($data['rebate'])) {
            $data['rebate'] = json_decode($data['rebate'], true);
        }
        foreach ($data['rebate'] as $key => $v) {
            if ($user['rebate'][$key] < $v) return ["code" => 0, "msg" => "返点值设置错误"];
        }
        $data['rebate'] = json_encode($data['rebate']);
        if (!$id) {
            $data['code'] = $this->create_invite_code();
            $r = true;
            while($r) {
                $r = $this->where("code", $data['code'])->find();
                if ($r) {
                    $data['code'] = $this->create_invite_code();
                }
            }
        }
        $data['userid'] = $user['id'];
        if ($id) {
            $res = $this->allowField(true)->save($data, ['id' => $id]);
        } else {
            $res = $this->allowField(true)->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "添加失败"];
        }
        return ["code" => 1, "msg" => "添加成功"];
    }

    

}
