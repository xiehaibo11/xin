<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Recharge as ARecharge;

class Recharge extends Base
{

    public function index()
    {
        $recharge= new ARecharge;
        $res = $recharge->getList();
        $page = $res ->render();
        $res -> append(['userinfo']);
        $res = $res->toarray();

        $all = $recharge->getStatis();
        $new = [0,0];
        if($all){
            $all = $all->toarray();
            foreach($all as $value){
                $new[$value['statuss']] = $value['money'];
            }
        }
        
        $this->assign('statis', $new);
        $this->assign('page', $page);
        $this->assign('list', $res['data']);
        $this->assign('query', request()->get());
        return $this->fetch('' , ['title' => '充值管理']);
    }

    /**
     * 拒绝充值
    */
    public function refuse($id = '')
    {
        if ($this->request->isPost()) {
            $post  = request()->post();
            $recharge = new ARecharge;
            $remark = $post['remark'];
            $res = $recharge->where('id', $id)->update(['remark' => $remark, 'statuss' => 2]);
            if (!$res)  return $this->error('处理失败');
            return $this->success('处理成功', url('admin/recharge/index'));
        }
        return $this->fetch('refuse' , ['title' => '拒绝操作']);
    }

    public function rechargeDo($id = '')
    {
        $recharge = new ARecharge;
        $info = $recharge->find($id);
        if ($info['statuss'] != 0) return $this->error('订单已处理');
        $res = $recharge->diamonds($info['money'], $info['userid'], $info['type']);
        if ($res) {
            $recharge->save(['statuss'=>1], ['id' => $id]);
            $this->success('处理成功');
        }
        $this->error('处理失败');
    }

    public function deleteId($id = '', $day = 0)
    {
        $recharge = new ARecharge;
        if($id){
            $res = $recharge->destroy($id);
        }
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d", strtotime("-".$day." day", $today));
            $res = $recharge ->destroy(['create_time' => ['<', $start]]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

    /**获取未处理订单 */
    public function getNum($is_json = 1)
    {
        $res['num'] = (new ARecharge)->whereIn('name', ['微信扫码充值', '支付宝扫码充值', '银行卡转账充值'])->where('statuss', 0)->count();
        $res['err'] = $res['num'] > 0 ? 0 : 1;
        return $is_json ? json($res) : $res;
    }
}