<?php
namespace app\index\model;

use app\common\model\MoneyHistory;
use app\common\model\UserAction;
use think\Model;
use think\Validate;

class Shop extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - 剩余数量
     * @return string
     */
    public function getHasNumAttr ($value,$data)
    {
        $has_num = $data['total_num'] - $data['convert_num'];
        $has_num = $has_num >=0 ? $has_num : 0;
        return $has_num;
    }

    /**
     * 验证兑换数量
     * @return string
     */
    public function checkNum ($user, $id, $num)
    {
        $info = $this->find($id);
        $has_num = $this->getHasNumAttr('',$info);
        //判断用户余额
        if ($user['money'] < $info['integral']) {
            return ['code' => 0, 'msg' => '用户余额不足，不能兑换'];
        }
        if ($has_num > 0 and $num <= $has_num and $num > 0) {
            return ['code' => 1];
        }
        return ['code' => 0, 'msg' => '兑换失败,兑换数量不够'];
    }

    /**
     * 兑换处理
     * @return string
     */
    public function doConvert ($user, $data)
    {
        $id = intval($data['id']);
        $data['num'] = intval($data['num']);
        $info = $this->find($id);
        $has_num = $this->getHasNumAttr('',$info);
        //判断用户余额
        if ($user['money'] < $info['integral']) {
            return ['code' => 0, 'msg' => '用户余额不足，不能兑换'];
        }
        if ($has_num > 0 and $data['num'] <= $has_num and $data['num'] > 0) {
            $res = $this->where('id', $id)->setInc('convert_num',$data['num']);
            (new MoneyHistory)->write([
                'userid' => $user['id'],
                'money'  => -$info['integral'] * $data['num'],
                'ext_name' => 'index/Shop',
                'type' => 3,
                'remark' => '在兑换商品兑换了' .$data['num']. '个' . $info['name'] .''
            ]);
            (new UserAction)->write([
                'userid' => $user['id'],
                'content'  => '在兑换商品兑换了' .$data['num']. '个' . $info['name'] .'',
                'ext_name' => 'index/Shop',
            ]);
            (new ShopOrder())->write([
                'userid' => $user['id'],
                'remark'  => $data['remark'],
                'num'  => $data['num'],
                'shop_id' => $data['id'],
                'money' => $info['integral'] * $data['num'],
                'address_id' => $data['address_id'],
            ]);
            if ($res) {
                return ['code' => 1];
            } else {
                return ['code' => 0, 'msg' => '兑换失败'];
            }
        }
        return ['code' => 0, 'msg' => '兑换失败,兑换数量不够'];
    }


}
