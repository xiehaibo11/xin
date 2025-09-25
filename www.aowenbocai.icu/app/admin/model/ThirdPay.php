<?php
namespace app\admin\model;

use think\Validate;

class ThirdPay extends BaseModel
{

    /**
     * 修改器 - reg_ip
     * @return json
     */
    protected function setRegIpAttr($value)
    {
        return request()->ip();
    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * @return json
     */
    public function add($data)
    {
        $validate = new Validate([
            'name|支付名称' => 'require',
            'post_url|提交地址' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if (isset($data['id'])) {
            $res = $this->allowField(true)->where('id', $data['id'])->update($data);
        } else {
            $res = $this->allowField(true)->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "添加失败"];
        }
        return ["code" => 1];
    }
}
