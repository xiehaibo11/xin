<?php

namespace app\admin\model;

use app\common\model\MoneyHistory;
use app \admin\model\Address;
use think\Model;
use think\Validate;

class ShopOrder extends BaseModel
{
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        $this->status_array = [0 => '未处理', 1 => '已处理', 7 => '撤销'];
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value, $data)
    {
        $html = $value == 0 ? '<font style="color: red;">' . $this->status_array[$value] . '</font>' : '<font style="color: #5cb85c;" >' . $this->status_array[$value] . '</font>';
        $html = $value == 7 ? '<font style="color: #999;">' . $this->status_array[$value] . '</font>' : $html;
        return $html;
    }

    /**
     * 获取器 - status
     */
    public function getStatusCodeAttr($value, $data)
    {
        return $data['status'];
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value, $data)
    {
        $user = User::get($data['userid']);
        return ['username'=>$user['username'],'nickname' => $user['nickname']];
    }

    /**获取器--获取地址 */
    public function getAddressAttr($value, $data)
    {
        $address = (new Address)->get($data['address_id']);
        return $address ? ($address->toArray()) : [];
    }
    /**
     * 获取器 - 产品名称
     * @return string
     */
    public function getNameAttr($value, $data)
    {
        $shop_info = Shop::get($data['shop_id']);
        if (empty($shop_info)) {
            return '产品已删除';
        }
        return $shop_info['name'];
    }

    public function edit($data)
    {
        $validate = new Validate([
            'id|商品ID' => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if ($data['status'] == 7) {
            $info = $this->find($data['id']);
            if ($info->getData('status') != 7) {
                $shop_info = Shop::get($info['shop_id']);
                (new MoneyHistory)->write([
                    'userid' => $info['userid'],
                    'type' => 3,
                    'money' => $info['money'],
                    'ext_name' => 'index/Shop',
                    'remark' => $data['admin_remark'] ? $data['admin_remark'] : '兑换撤销'
                ]);
                (new Shop)->where('id', $info['shop_id'])->setDec('convert_num',$info['num']);
            }
        }
        $res = $this->allowField(['status', 'remark', 'admin_remark'])->where('id', $data['id'])->update($data);

        if (!$res) {
            return ["code" => 0, "msg" => "处理失败"];
        }
        return ["code" => 1];
    }

    public function addData($data)
    {
        $rules = [
            'userid' => 'require|number',
            'shop_id' => 'require|number',
            'num' => 'require|number',
            'money' => 'require|number',
        ];

        $validate = new Validate($rules);
        if(!$validate->check($data)){
            return ['err' => 1, 'msg' => $validate->getError()];
        }
        return $this->save($data);
    }
}
