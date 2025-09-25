<?php
namespace app\cars\controller;

use app\admin\controller\Base;
use app\cars\model\PluginCars;
use app\cars\model\PluginCarsCode as Code;
use app\cars\model\PluginCarsHis as His;
use app\admin\model\ExtShowList;
use app\admin\model\User;
use core\Setting;

class Admin extends Base
{
    public function index($words = '')
    {
        $pageSize = 10;
        $Cars = new PluginCars;
        if($words != ''){
            $Cars->where('name', 'LIKE', "%$words%");
        }
        $list = $Cars->order("sort ASC, id DESC")->paginate($pageSize, false, ['query' => ['words' => $words]]);
        $setting = Setting::get(['carsper']);
        if(empty($setting)){
            $setting['carsper'] = 0;
            Setting::set(['carsper' => 0]);
        }
        $this->assign('carsper', $setting['carsper']);
        return $this->fetch('index',['title' => '设置', 'list' => $list, 'query' => ['words' => $words]]);
    }

  
    /**修改物品状态 */
    public function  setStatus()
    {
        $data = request()->param();
        if(!isset($data['value']) || !isset($data['id'])){
            $this->error('参数错误');
        }
        $res = (new PluginCars)->save(['status' => $data['value']], ['id' => $data['id']]);
        if($res){
            $str = $data['value'] == 1 ? '启用' : '禁用';
            $this->success($str.'成功');
        }
        $this->error('设置失败');
    }

    /**添加物品 */
    public function addList()
    {
        if(request()->IsPost()){
            $data = request()->post();
            $res = (new PluginCars)->addAward($data);
            if(isset($res['msg'])){
                return json($res);
            }
            return json(['err' => 0, 'msg' => '添加成功']);
        }
        return $this->fetch('add', ['title' => '添加物品']);
    }
    
    /**删除物品 */
    public function remove()
    {
        $data = request()->param();
        if(!isset($data['id'])){
            $this->error('参数错误');
        }
        $lucky = new PluginCars;
        $info = $lucky->get($data['id']);
        if(!$info){
            $this->error('该条件下数据不存在');
        }
        $info = $info->toArray();
        $lucky->where('sign', '>', $info['sign'])->setDec('sign', 1);
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
        $list->append(['showCode', 'userInfo']);
        $page = $list->render();
        $list = $list->toArray();
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("page",$page);
        return $this->fetch("betting", ['title' => '幸运赛车订单管理', 'query' => request()->get()]);
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
            $info = (new PluginCars)->get($id);
            if(!$info){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $res = (new PluginCars) ->save($data, ['id' => $data['id']]);
            if($res){
                return json(['err' => 0, 'msg' => '修改成功']);
            }
            return json(['err' => 2, 'msg' => '修改失败']);
        }
        $info = (new PluginCars)->get($id);
        if(!$info){
            $this->error('信息不存在');
        }
        $info = $info->toArray();
        return $this->fetch('edit', ['title'=> '修改奖金规则', 'data' => $info]);
    }

    /**获取开奖号码 */
    public function getOpenList()
    {
        $list = (new Code)->getList();
        $list->append(['showCode']);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return $this->fetch("open", ['title' => '幸运赛车开奖号码', 'query' => request()->get()]);
    }

    public function deleteOpen()
    {
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new Code)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new Code)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除成功');
    }

    public function changeSp()
    {
        if(request()->IsPost()){
            $data = request()->post();
            $saveData = $data['data'];
            $sp = array_column($saveData,"sp");
            $count = count($sp);
            
            if(array_sum($sp) != 10000){
                return json(['err' => '2', 'msg' => '概率总和应等于10000']);
            }

            $sort = array_column($saveData,"sort");
            $sort = array_unique($sort);
            $sort_count = count($sort);
            if($sort_count != $count){
                return json(['err' => '3', 'msg' => '排序不得有重复']);
            }
            $res = (new PluginCars)->saveAll($saveData);
            if(!$res){
                return json(['err' => 1, 'msg' => '修改失败']);
            }
            return json(['err' => 0, 'msg' => '修改成功']);
        }
        $info = (new PluginCars)->order('sort ASc')->select();
        $info = $info->toArray();
        return $this->fetch("sp", ['title' => '中奖率及排序设置', 'data' => $info]);
    }

    public function setCarsper()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!is_numeric($data['value'])){
                return json(['err' => 1, 'msg' => '盈利率只能是数字']);
            }
            if($data['value'] > 100){
                return json(['err' => 2, 'msg' => '盈利率不能超过100%']);
            }
             Setting::set(['carsper' => $data['value']]);
            $cars = new PluginCars;
            $info = $cars->column('id,sp');
            foreach ($info as $key => $value) {
                $save[$key]['id'] = $key;
                $num = (10000 / $value) * ((100 - $data['value']) / 100);
                $save[$key]['multiple'] = floor($num * 100) /100;
            }
            $res = $cars->saveAll($save);
            return json(['err' => 0, 'msg' => '修改成功']);
        }
    }
}
