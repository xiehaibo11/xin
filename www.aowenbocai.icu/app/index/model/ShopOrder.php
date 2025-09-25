<?php
namespace app\index\model;

use think\Model;
use app\common\model\MoneyHistory;
use think\Validate;

class ShopOrder extends Model
{
    protected $resultSetType = 'collection';
    protected  $updateTime = false;

    /**
     * 获取器 - 商品信息
     * @return string
     */
    public function getShopInfoAttr ($value,$data)
    {
        $info = (new Shop)->find($data['shop_id']);
        return $info;
    }

    /**获取器--用户信息 */
    public function getUserInfoAttr ($value,$data)
    {
        $user = (new User)->get($data['userid']);
        $list = ['0' => '待处理', '1' => '已处理', '7' => '已撤销'];
        return ['nickname' => $user['nickname'] , 'statusName' => $list[$data['status']],'username' => $user['username'], 'agents' => $user['agents']];
    }

    /**
     * 创建订单
     * @return string
     */
    public function write ($data)
    {
        $validate = new Validate([
            'userid|用户ID'  => 'require',
            'shop_id|产品ID'   => 'require',
            'address_id|收货地址ID'   => 'require'
        ]);
        if (!$validate->check($data)) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        if (!$this->save($data)) {
            return ['code' => 0, 'msg' => '创建订单失败'];
        }
        return ['code' => 0, 'msg' => '创建订单成功'];
    }

    public function getStatis($userid = '')
    {
        return $this->getGetData(1, $userid);
    }

    public function getGetData($type = 0, $userid = '')
    {
        if($userid != ''){
            $this->where('userid', 'in', $userid);
        }
        $data = request()->get();
        if(isset($data['today'])){
            $this->where('create_time', '>=' , date("Y-m-d"));
        }
        if($type){
            return $this->Sum('money');
        }else{
            return $this->order('id desc')->paginate();
        }
    }

    /**
     * 撤单处理
     * @return string
     */
    public function backOrder ($user, $id)
    {
        $info = $this->where('status', 0)->where('userid', $user['id'])->find($id);
        if (!empty($info)) {
            (new MoneyHistory)->write([
                'userid' => $user['id'],
                'money'  => $info['money'],
                'ext_name' => 'index/Shop',
                'type' => 3,
                'remark' => '兑换商品撤单'
            ]);

            $res = $this->where('id', $id)->update(['status'=> 7]);
            if ($res) {
                return ['code' => 1];
            } else {
                return ['code' => 0, 'msg' => '数据操作失败'];
            }
        }
        return ['code' => 0, 'msg' => '参数不对'];
    }

}
