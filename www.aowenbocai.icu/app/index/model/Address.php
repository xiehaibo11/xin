<?php

namespace app\index\model;

use think\Model;
use think\Validate;

class Address extends Model
{
    protected $autoWriteTimestamp = false;
    protected $resultSetType = 'collection';

    /**
     * 添加/修改
     * @param $data  表单提交的值
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'name|联系人姓名' => 'require',
            'tel|电话号码' => 'require',
            'city|区域' => 'require',
            'address|详细地址' => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if ($data['id']) {
            $res = $this->where('id', $data['id'])->update($data);
        } else {
            $res = $this->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "提交失败"];
        }
        return ["code" => 1, "msg" => "提交成功"];
    }

    /**
     * 改变默认地址
     * @return array
     */
    public function changeStatus($data)
    {
        $this->where('userid', $data['userid'])->update(['status' => 0]);
        $res = $this->where('id', $data['id'])->update(['status' => 1]);
        if (!$res) {
            return ["code" => 0, "msg" => "设置失败"];
        }
        return ["code" => 1, "msg" => "设置成功"];
    }

    /**
     * 获取默认地址
     * @return array
     */
    public function getDefault($userid)
    {
        $res = $this->where('userid', $userid)->where('status', 1)->find();

        if (empty($res)) {
            $res = $this->where('userid', $userid)->where('id', ($this->max('id')))->find();
            if (empty($res)) { return ["code" => 0]; }
        }
        return ["code" => 1, "data" => $res];
    }

}
