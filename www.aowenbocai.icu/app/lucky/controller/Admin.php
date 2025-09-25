<?php
namespace app\lucky\controller;

use app\admin\controller\Base;
use app\lucky\model\PluginLucky;
use app\lucky\model\PluginLuckyAward as Award;
use app\lucky\model\PluginLuckyHis as His;
use app\admin\model\ExtShowList;
use app\admin\model\User;
use app\lucky\model\PluginLuckySetting;

class Admin extends Base
{
    public function index($words = '')
    {
        
        $pageSize = 10;
        $Lucky = new PluginLucky;
        if($words != ''){
            $Lucky->where('name', 'LIKE', "%$words%");
        }
        $list = $Lucky->order("id DESC")->paginate($pageSize, false, ['query' => ['words' => $words]]);
        return $this->fetch('index',['title' => '设置', 'list' => $list, 'query' => ['words' => $words]]);
    }

    /**中奖规则 */
    public function awardRule($pid = 0)
    {
        $pageSize = 10;
        $Award = new Award;
        $where['type'] = 0;
        if($pid != 0){
            $where['type'] = 1;
            $where['sort'] = $pid;
            $info = $Award->get($pid);
            if(!$info) $this->error('参数错误');
            $info->append(['planList']);
            $this->assign('info', $info);
        }
        $list = $Award->getList(0,$where);
        $profit = PluginLuckySetting::where('name', 'profit')->find();
        $this->assign('profit', $profit['value']);
        $list->append(['planList']);
        return $this->fetch('rule',['title' => '设置', 'list' => $list, 'pid'=> $pid]);
    }
  
    /**添加物品 */
    public function addLucky()
    {
        if(request()->IsPost()){
            $data = request()->post();
            if(!isset($data['goodsname'])){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $res = (new PluginLucky)->addLucky(['name' => $data['goodsname']]);
            return json($res);
        }
    }

    /**修改物品状态 */
    public function  setStatus()
    {
        $data = request()->param();
        if(!isset($data['value']) || !isset($data['id'])){
            $this->error('参数错误');
        }
        $res = (new PluginLucky)->save(['status' => $data['value']], ['id' => $data['id']]);
        if($res){
            $msg = '启用成功，相关中奖规则请手动启用';
            if($data['value'] == 0){
                (new Award)->where('plan', 'like', "%".$data['id']."%")->setField('status', 0);
                $msg = '禁用成功，相关中奖规则请已禁用';
            }
            $this->success($msg);
        }
        $this->error('设置失败');
    }

    /**添加中奖规则 */
    public function addAward($type = 0, $pid = 0)
    {
        if(request()->IsPost()){
            $data = request()->post();
            // print_r($data);die;
            $res = (new Award)->addAward($data);
            if(!$res || isset($res['err'])){
                $msg = isset($res['msg']) ? $res['msg'] : '添加失败';
                return json(['err' => 2, 'msg' => $msg]);
            }
            return json(['err' => 0, 'msg' => '添加成功']);
        }
        if($type == 1){
            $res = $pid != 0 ? 1 : 0;
            if($res){
                $res = (new Award)->get($pid);
            }
            if(!$res) $this->error('参数错误');
            $res->append(['planList']);
            $this->assign('info', $res);
        }
        $Lucky = new PluginLucky;
        $list = $Lucky->where('status', 1)->select();
        return $this->fetch('add', ['title' => '添加中奖规则', 'goods' => $list, 'type' => $type, 'pid' => $pid]);
    }

    /**修改中奖组合状态 */
    public function setAwardStatus()
    {
        $data = request()->param();
        if(!isset($data['value']) || !isset($data['id'])){
            $this->error('参数错误');
        }
        $res = (new Award)->save(['status' => $data['value']], ['id' => $data['id']]);
        if($res){
            $this->success('设置成功');
        }
        $this->error('设置失败');
    }
    
    /**删除物品 */
    public function remove()
    {
        $data = request()->param();
        if(!isset($data['id'])){
            $this->error('参数错误');
        }
        $lucky = new PluginLucky;
        $info = $lucky->get($data['id']);
        if(!$info){
            $this->error('该条件下数据不存在');
        }
        $res = $lucky->where('id', $data['id'])->delete();
        if(!$res){
            $this->error('删除数据失败');
        }
        (new Award)->where('plan', 'like', "%".$data['id']."%")->delete();
        $this->success('删除数据成功');
    }
    
    /**删除物品奖励 */
    public function removeAward()
    {
        $data = request()->param();
        if(!isset($data['id'])){
            $this->error('参数错误');
        }
        $lucky = new Award;
        $info = $lucky->get($data['id']);
        if(!$info){
            $this->error('该条件下数据不存在');
        }
        /**删除相关的幸运奖 */
        $lucky->where('sort', $data['id'])->where('type', 1)->delete();
        $res = $lucky->where('id', $data['id'])->delete();
        if(!$res){
            $this->error('删除数据失败');
        }
        $this->success('删除数据成功');
    }

    /**获取下注记录 */
    public function record()
    {
        $list = (new His)->getList();
        $list->append(['name','codeRs', 'PlanList']);
        $page = $list->render();
        $list = $list->toArray();
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("page",$page);
        return $this->fetch("betting", ['title' => '幸运77订单管理', 'query' => request()->get()]);
    }

    /**删除数据记录 */
    public function deleteLucky()
    {
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new His)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new His)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除成功');
    }

    public function openAward($id)
    {
        $info = (new His)->get($id);
        if(!$info){
            $this->error('数据错误');
        }

        $info = $info->toArray();
        if(!empty($info['plan'])){
            $this->error('该数据已获取开奖数据');
        }
        $user = (new User)->get($info['userid']);
        if(!$user){
            $this->error('该用户不存在');
        }
        $user->visible(['id','money']);
        $user = $user->toArray();
        $open_res = (new Common) ->openAward($info['code']);
        /**中奖写入资金明细*/
        $award_res = (new His)->setAward($info['money'], $open_res, $id, $user);
        if(isset($award_res['award_money'])){
            $this->success('处理成功');
        }
        $this->success('处理失败');
    }

    public function edit($id)
    {
        if(request()->IsPost()){
            $data = request()->post();
            $award = new Award;
            $info = $award->get($id);
            if(!$info){
                return $this->error('参数错误');
            }
            $res = $award ->save($data, ['id' => $data['id']]);
            if($res){
                return $this->success('修改成功', url('awardrule'));
            }
            return $this->error('修改失败');
        }
        $award = new Award;
        $info = $award->get($id);
        if(!$info){
            $this->error('信息不存在');
        }
        $info->append(['planList', 'status_name']);
        $info = $info->toArray();
        return $this->fetch('edit', ['title'=> '修改奖金规则', 'data' => $info]);
    }
    
    public function changeSp()
    {
        if(request()->IsPost()){
            $data = request()->post();
            $saveData = $data['data'];
            $sp = array_column($saveData,"sp");
            $count = count($sp);
            
            if(array_sum($sp) != 1000){
                return json(['err' => '2', 'msg' => '概率总和应等于1000']);
            }

            $sort = array_column($saveData,"sort");
            $sort = array_unique($sort);
            $sort_count = count($sort);
            if($sort_count != $count){
                return json(['err' => '3', 'msg' => '排序不得有重复']);
            }
            $res = (new Award)->saveAll($saveData);
            if(!$res){
                return json(['err' => 1, 'msg' => '修改失败']);
            }
            return json(['err' => 0, 'msg' => '修改成功']);
        }
        $award = new Award;
        $info = $award->order('sort ASC')->order('sp ASC')->select();
        $info->append(['planList', 'status_name']);
        $info = $info->toArray();
        return $this->fetch('sp', ['title'=> '修改中奖率', 'data' => $info]);
    }

    public function setProfit()
    {
//        $model = (new Award());
//        $list = $model->select();
//        $total_sp = 0;
//        $total_money = 0;
//        foreach ($list as $key => $value) {
//            $total_sp += intval($value['sp']) * 100;
//            $total_money += intval($value['sp']) * $value['multiple'] * 100;
//        }
//        var_dump(($total_sp - $total_money)/$total_sp * 2);
//        var_dump($total_sp);
//        var_dump($total_money);
//        return json(['err' => 0, 'msg' => '修改成功']);
        if(request()->isPost()){
            $data = request()->post();
            if(!is_numeric($data['value'])){
                return json(['err' => 1, 'msg' => '盈利率只能是数字']);
            }
            if($data['value'] > 100){
                return json(['err' => 2, 'msg' => '盈利率不能超过100%']);
            }
            $model = (new Award());
            PluginLuckySetting::where('name', 'profit')->update(['value' => $data['value']]);
//            $list = $model->where('id', 'neq', '2')->select();
//            $total_sp = 0;
//            $total_money = 0;
//            foreach ($list as $key => $value) {
//                $total_sp += intval($value['sp']);
//                $total_money += intval($value['sp']) * $value['multiple'];
//            }
//            $sp = ($total_money - $total_sp)/0.9;
//            $sp += $sp * (100 - $data['value'])/100;
//            $res = $model->where('id', '2')->update(['sp' => $sp]);
            return json(['err' => 0, 'msg' => '修改成功']);
        }
    }

}
