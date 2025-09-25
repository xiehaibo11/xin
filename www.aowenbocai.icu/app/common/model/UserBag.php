<?php
namespace app\common\model;

use app\admin\model\Prop;
use think\Model;
use app\index\model\User;

class UserBag extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getUserInfoAttr($value, $data)
    {
        $user = User::get($data['userid']);
        return ['nickname' => $user['nickname'], 'photo' =>  $user['photo']];
    }

    public function getNameAttr($value, $data)
    {
        $info = (new Prop())->where('param_name', $data['param_name'])->find();
        return (empty($info) and !$info['name']) ? '未定义' : $info['name'];
    }

    public function getUrlAttr($value, $data)
    {
        return url($data['ext_name']);
    }

    public function getDescAttr($value, $data)
    {
        $info = (new Prop())->where('param_name', $data['param_name'])->find();
        return (empty($info) and !$info['desc']) ? '未定义' : $info['desc'];
    }

    public function getPicAttr($value, $data)
    {
        $info = (new Prop())->where('param_name', $data['param_name'])->find();
        return empty($info) ? '' : $info['img_url'];
    }

    public function getList($ext_name, $where = [], $callback = null)
    {
        $where = [];
        if ($ext_name) {
            $this->where('ext_name', $ext_name);
        }
        if (!empty($where)) {
            $this->where($where);
        }
        if ($callback) {
            $res = $this->paginate(15, false, request()->param())->each($callback);
        } else {
            $res = $this->paginate(15, false, request()->param());
        }
        return $res;
    }

    /**
     * 道具加入会员背包
    */
    public function add($userid, $param_name, $num = 1)
    {
        $prop = (new Prop())->where('param_name', $param_name)->find();
        if (empty($prop)) return ['code' => 0, 'msg' => '道具不存在'];
        $has_info = $this->where('param_name', $param_name)->where('userid', $userid)->find();
        if (empty($has_info)) {
            $res = $this->save([
                'userid' => $userid,
                'param_name' => $param_name,
                'ext_name' => $prop['ext_name'],
                'num' => $num,
            ]);
        } else {
            $res = $has_info->save([
                'num' => $has_info['num'] + $num,
            ]);
        }
        if ($res) {
            ['code' => 1, 'msg' => '添加成功'];
        }
        return ['code' => 0, 'msg' => '添加失败'];
    }



}
