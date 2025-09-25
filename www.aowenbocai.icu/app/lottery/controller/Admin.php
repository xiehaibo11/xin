<?php

namespace app\lottery\controller;

use app\admin\controller\Base;
use app\admin\model\Ext;
use app\admin\model\User;
use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use app\common\model\MoneyHistory;
use app\lottery\model\common\BaseBuy;
use app\lottery\model\common\BaseCode;
use app\lottery\model\common\BaseExpect;
use app\web\controller\Common;
use think\Cache;
use think\Db;
use core\Page;
use core\Setting;

class Admin extends Base
{
    public function index($name)
    {
        return $this->fetch('', ['title' => '后台管理', 'name' => $name]);
    }

    /**重新统计所有彩种遗漏 */
    public function reCountMiss($name = '')
    {
        if (!$name) {
            $name_array = (new ExtShowList)->where('type', 1)->column('name');
        } else {
            $name_array = [$name];
        }
        $award_mode = new Award();
        foreach ($name_array as $v) {
            $v = trim($v, '/');
            $setting = LotteryCommon::getSetting($v);
            $setting->setValue(LotteryCommon::getSettingValue($v, 'miss'), '');
            $code_list = LotteryCommon::getModel($v, 'code')->field('expect, code')->order('expect desc')->limit(0, 30)->select()->toArray();
            if (!empty($code_list)) {
                $award_mode->miss($v, $code_list);
            }
        }
        return $this->success('统计遗漏成功');
    }

    /**验证该彩种是否存在 */
    public function checkLotteryExist($name)
    {
        $res = (new ExtShowList)->where('name','in',['/'.$name, $name])->where('type', 1)->column('title');
        return $res ? $res[0] : ['err' => 1];
        
    }


    /**出票 超时不出票 未达到条件不出票*/
    public function outOfTicket($id, $lotteryId)
    {
        $lottery = strtolower(explode('|', $lotteryId)[0]);
        $tableEx = $this->checkLotteryExist($lottery);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $table = ucfirst($lottery);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }

        $buy =  'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Buy';
        $res = (new $buy)->get($id);
        if(!$res){
            return json(['err' => 1, 'msg' => '参数错误<01>']);
        }
        $res = $res->toArray();
        if($res['lottery_id'] != $lotteryId){
            return json(['err' => 1, 'msg' => '参数错误<02>']);
        }

        if(strtotime($res['end_time']) < time()){
            return json(['err' => 2, 'msg' => '该单下单的时间已结束']);
        }
        if($res['is_join'] == 1){
            $setting = (new Setting)->get(['isAuto', 'openBd', 'Bdpercent']);
            $percent = $setting['openBd'] == 1 && floatval($setting['Bdpercent']) > 0 ? floatval($setting['Bdpercent']) : 100;
            $join =  'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Join';
            $buyAll = (new $join)->where('buy_id', $id)->sum('money');
            $nowPerent = (($buyAll + $res['assure_money']) / $res['total_money'] * 1000) /10;
            if($nowPerent < $percent){
                return json(['err' => 3, 'msg' => '合买未达到出票条件']);
            }
        }
        $expect = 'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Expect';
        $result = (new $expect)->where('buy_id', $id)->setField('status', 1);
        if($result){
            return json(['err' => 0, 'msg' => '出票成功']);
        }
        return json(['err' => 4, 'msg' => '出票失败']);
    }

    /** 一条条删除投注内容*/
    public function buyDelete($id, $lotteryId)
    {
        $lottery = strtolower(explode('|', $lotteryId)[0]);
        $tableEx = $this->checkLotteryExist($lottery);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $table = ucfirst($lottery);
        $buy =  'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Buy';
        $res = (new $buy)->where('id',$id)->delete();
        if($res){
            $join =  'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Join';
            (new $join)->where('buy_id', $id)->delete();
            $expect = 'app\lottery\model\\'.$lottery.'\Plugin'.$table.'Expect';
            (new $expect)->where('buy_id', $id)->delete();
            return json(['err' => 0, 'msg' => '删除成功']);
        }
        return json(['err' => 1, 'msg' => '删除失败']);
    }

    /**按时间删除投注内容 */
    public function buyDeleteDay($name, $day)
    {
        $tableEx = $this->checkLotteryExist($name);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $model = LotteryCommon::getModel($name, 'buy');
        $join =  LotteryCommon::getModel($name, 'join');
        $expect = LotteryCommon::getModel($name, 'expect');
        if($day == 1){
            $res = $model->wherr('id', '>=', 1)->delete();
            if($res){
                $join->where('buy_id', '>=', 1)->delete();
                $expect->where('buy_id', '>=',  1)->delete();
                return json(['err' => 0, 'msg' => '删除成功']);
            }
        }else{
            $today = strtotime(date("Y-m-d"));
            $start = $today - $day * 24 * 60 * 60;
            $startData = date("Y-m-d");
            $buyid = $model->where('create_time', '<=', $startData)->column('id');
            if(!empty($buyid) && $model->where('create_time', '<=', $startData)->delete()){
                $join->where('buy_id', 'IN', $buyid)->delete();
                $expect->where('buy_id', 'IN',  $buyid)->delete();
                return json(['err' => 0, 'msg' => '删除成功']);
            }
        }
        return json(['err' => 1, 'msg' => '删除失败']);
    }
    
    /**撤单 */
    public function returnTicket($id, $lotteryId)
    {
        $lottery = strtolower(explode('|', $lotteryId)[0]);
        $tableEx = $this->checkLotteryExist($lottery);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $table = strtolower($lottery);
        $expect = LotteryCommon::getModel($table, 'expect');
        $expectModel = new $expect;
        $ext_list = $expectModel->field('id, status, multiple')->where('buy_id', $id)->select();
        if(empty($ext_list)){
            return json(['err' => 1, 'msg' => '该单已损坏，无法撤单']);
        }
        $total_multiple = 0;
        $back_multiple = 0;
        $back_data = [];
        foreach ($ext_list as $value) {
            $total_multiple += $value['multiple'];
            if($value['status'] < 2){
                $back_data[] = [
                    'id' => $value['id'],
                    'status' => 7
                ];
                $back_multiple += $value['multiple'];
            }
        }
        $buy =  LotteryCommon::getModel($table, 'buy');
        $buyModel = new $buy;
        $buyRes = $buyModel->where('id', $id)->find();
        $buyRes->append(['joinData'])->toArray();
        $joinData = $buyRes['joinData'];
        $backMoney = floatval($buyRes['total_money'] / $total_multiple * 100) * $back_multiple / 100;
        if (!$backMoney) return json(['err' => 1, 'msg'=>'该订单撤单金额为0']);
        foreach ($joinData as $key => $value) {
            $return[$key] = ['money' => $value['money']/$buyRes['total_money'] * $backMoney, 'userid' => $value['userid'],'remark' => '系统撤单:'.$buyRes['lottery_id']];
        }
        if($buyRes['is_join'] == 1){
            $return[] = ['money'=> $buyRes['assure_money']/$buyRes['total_money'] * $backMoney, 'userid' => $buyRes['userid'],'remark' => '返回保底:'.$buyRes['lottery_id']];
        }
        $money = new MoneyHistory;
        foreach ($return as $key => $value) {
            $res = $money->write($value);
            $result = $res['code'] == 1 ? true : false;
        }
        
        if($result){
            $expectModel->saveAll($back_data);
            $buyModel->where('id', $id)->setField('status',7);
            return json(['err' => 0, 'msg'=>'撤单成功']);
        }
        return json(['err' => 3, 'msg'=>'撤单失败']);
    }

    /**
     * 开奖号码管理
     * @return mixed
     */
    public function code($name = '')
    {
        $ext_model = (new ExtShowList);
        if (!$name) {
            $ext_info = $ext_model->where('type',1)->where('status',0)->find();
            $name = trim($ext_info['name'], '/');
        }
        $tableEx = $this->checkLotteryExist($name);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $modelClass = LotteryCommon::getModel($name, 'code');
        $data = request()->get();
        if(!empty($data['expect'])) {
            $modelClass->where('expect', $data['expect']);
        }
        if(!empty($data['starttime'])) {
            $modelClass->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])) {
            $endtime = date('Y-m-d', strtotime($data['endtime'] . ' +1 day'));
            $modelClass->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        $list = $modelClass->order('id desc')->paginate(13, false, ['query' => $data]);
        $this->assign("list", $list);
        $this->assign("query", $data);
        $extList = $ext_model->where('type',1)->where('status',0)->column(['name', 'title']);
        return $this->fetch('code', ['title' => '开奖号码管理', 'name' => $name, 'lotteryName' => $tableEx,'extList' =>  $extList]);
    }

   /**派奖 */
   public function award($id, $name)
   {
        $id = intval($id);
        if(!$id || !$name){
            return json(['err' => 1, 'msg' => '参数错误<01>']);
        }
        $tableEx = $this->checkLotteryExist($name);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }

        $modelClass = LotteryCommon::getModel($name, 'code');
        $res = $modelClass->get($id);
        if(!$res){
            return json(['err' => 1, 'msg' => '参数错误<02>']);
        }
        $data[] = ['expect' => $res->expect, 'code' => $res->code];
        $res = $modelClass->setPrize($data);
        return json(['err' => 0, 'msg' => $res]);
   }
    /**
     * 删除开奖号码
     * @return mixed
     */
    public function codeDelete()
    {
        if(request()->isPost()){
            $data = request()->post();
            $class = LotteryCommon::getModel($data['name'], 'code');
            $res = $class->where('id', $data['id'])->delete();
            if (!$res) {
                return json(['err' => 1, 'msg'=> '删除失败']);
            } 
            return json(['err' => 0, 'msg'=> '删除成功']);
        }
    }

    public function codeDayDelete($day = 0, $name = '', $is_all = 0)
    {
        if ($is_all) {
            $model = new BaseCode();
        } else {
            $model = LotteryCommon::getModel($name, 'code');
        }
        $res = 0;
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day", $today));
            $res = $model ->destroy(['create_time' => ['<' , $start]]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

    public function get_model($name)
    {
        $isSSc = mb_substr($name, -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
        $isKs = mb_substr($name, -2, 2, 'utf-8') == 'ks' ? 1 : 0;
        $is28 = mb_substr($name, -2, 2, 'utf-8') == '28' ? 1 : 0;
        if ($name == 'pk10') return 'pk10';
        if ($isSSc) {
            $module = 'ssc';
        } elseif ($isKs) {
            $module = 'ks';
        } elseif ($is28) {
            $module = 'pc28';
        } else {
            $module = 'syxw';
        }
        return $module;
    }

    /**
     * 添加开奖号码
     * @return mixed
     */
    public function addCode($name)
    {   
        $tableEx = $this->checkLotteryExist($name);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        if (request()->isPost()) {
            $data = request()->Post();
            $code_model = LotteryCommon::getModel($name, 'code');
            $has_expect = $code_model->where('expect', $data['expect'])->find();
            if ($has_expect) {
                $this->error('已存在该期开奖');
            }else {
                $code = explode(',',$data['code']);
                $newCode = array_unique($code);
                /**开奖号码判断不全，稍后在加,目前只对时时彩和11选5位数进行判断 */
                $cp_type = LotteryCommon::getCpType($name);
                if(count($code) != 5 && in_array($cp_type, ['syxw', 'ssc'])&&$name!='ynssc'&&$name!='jlssc'){
                    $this->error('开奖号码错误');
                }
               if(count($newCode) != 10 && $cp_type == 'pk10'){
                    $this->error('开奖号码错误<02>');
               }
                if(count($code) != 3 && in_array($cp_type, ['ks', 'pc28'])){
                    $this->error('开奖号码错误<03>');
                }
                $data = [
                    'code' => $data['code'],
                    'expect' => $data['expect'],
                ];
                $res = $code_model->save($data);
                if ($res) {
                    (new Award)->miss($name, [$data]);/**遗漏 */
                    $this->success('添加开奖号码成功', url("code",['name' => $name]));
                }
                $this->success('添加开奖号码失败');
            }
        }
        return $this->fetch('add_code', ['title' => '添加开奖号码', 'name'=> $name, 'lotteryName' => $tableEx]);

    }

    /**
     * 修改开奖号码
     */
    public function editCode($name, $id)
    {   
        $tableEx = $this->checkLotteryExist($name);
        if(isset($tableEx['err'])){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $code_model = LotteryCommon::getModel($name, 'code');
        $info = $code_model->get($id);
        if (request()->isPost()) {
            $data = request()->Post();
            $has_expect = $code_model->where('expect', $data['expect'])->where('id', 'NEQ', $id)->find();
            if (!empty($has_expect)) {
                return $this->error('已存在该期开奖');
            } else {
                $res = $code_model->save($data, ['id' => $id]);
                if ($res) {
                    return $this->success('编辑开奖号码成功', url("code",['name' => $name]));
                } else {
                    return $this->error('编辑开奖号码失败');
                }
            }
        }
        $this->assign('info', $info);
        return $this->fetch('edit_code', ['title' => '修改开奖号码', 'name' => $name, 'lotteryName' => $tableEx]);

    }

   /**获取彩种记录*/
    public function allList($gameid ='')
    {
        $data = request()->get();
        $extShow = (new ExtShowList);
        if($gameid == ''){
            $extShow->where('type', 1)->where('status',0);
        }else{
            $extShow->where('id',  $gameid);
        }
        $tables = $extShow->column(['name','title','id']);
        $tables = array_values($tables); 
        if(empty($tables)){
            return json(['err' => 0,'data' => '', 'msg' => '您没有购买彩票程序，或彩票未开启' ]);
        }
         
        $extList = (new ExtShowList)->getRecodeExt(1);
        $lottery = count($tables) > 1 ? "所有彩种" : $tables[0]['title'];
        $buy_model = (new BaseBuy());
        if ($gameid) {
            $buy_model->where('ext_name', trim($tables[0]['name'], '/'));
        }
        if(!empty($data['username'])){
            $id = (new User)->where('nickname|username', 'like', "%".$data['username']."%")->column('id');
            if(empty($id)){
                $dataList = [
                    'data' => [],
                    'page' => ''
                ]; 
                return $this->fetch('all', ['title' => '历史记录', 'data'=> $dataList, 'extList' =>  $extList, 'lotteryName' => $lottery, 'query' => $data]);
            }
            $buy_model->whereIn('userid', $id);
        }
        if(!empty($data['userid'])){
            $buy_model->where('userid', $data['userid']);
        }
        if(!empty($data['starttime'])){
            $buy_model->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['lottery_id'])){
            $buy_model->where('lottery_id', $data['lottery_id']);
        }
        if(!empty($data['expect'])){
            $buy_id = (new BaseExpect())->where('expect', $data['expect'])->column('buy_id');
            $buy_model->whereIn('id', $buy_id);
        }
        if(!empty($data['endtime'])){
            $buy_model->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if(!empty($data['status'])){
            switch ($data['status']) {
                case 1;//进行中
                    $buy_model->where('status', 0);
                    break;
                case 2;//已中奖
                    $buy_model->where('status', 1);
                    break;
                case 3;//未中奖
                    $buy_model->where('status', 2);
                    break;
                case 4;//撤单
                    $buy_model->whereIn('status',  [6, 7, 8]);
                    break;
            }
        }
        if(isset($data['bounsStatus']) && $data['bounsStatus'] !=  ''){
            $buy_model->where('status', $data['bounsStatus']);
        }
        $res = $buy_model->order('create_time desc')->paginate(14, false,  ['query' => $data]);
        $page_html = $res->render();
        $res = $res->append(['buy_info', 'user_buy', 'nickname', 'betting', 'ext_name_txt'])->toArray();
        foreach ($res['data'] as &$v) {
            if ($v['is_join']) {
                $v['user_buy'] = number_format(($v['user_buy']/$v['total_share']) * $v['total_money'], 2);
            } else {
                $v['user_buy'] = number_format($v['user_buy'], 2);
            }
            $info = (LotteryCommon::getModel($v['ext_name'], 'code'))->where('expect', $v['buy_info']['expect'])->find();
            $v['code'] = $info ? $info->getData('code') : '';
        }
        return $this->fetch('all', ['title' => '历史记录', 'data'=> $res, 'extList' =>  $extList, 'lotteryName' => $lottery, 'query' => $data, 'page_html' => $page_html]);
     }

     /**彩种排序 */
     public function setSort($res, $data)
     { 
        $sort = [SORT_DESC, SORT_ASC];
        $type = ['total_money', 'status', 'create_time', 'buyprecent','ure_finsh','record','ure'];
        /**排序类型 */
        $resType = isset($data['type']) ? in_array($data['type'], $type) : false;
        if(!$resType){
            $data['type'] = 'ure_finsh';
            $data['sort'] = 0;
        }

        $sortArr = [];
        foreach ($res as $value) {
            $sortArr[] = $value['create_time'];
        }
        array_multisort($sortArr, SORT_DESC, $res);
        return $res;
        
     }

    /**系统开奖列表页面 */
    public function push_system_code($name, $expect)
    {
        $list = cache('system_code_' . $name);
        if (!$list) return $this->error('参数错误');
        $list = json_decode($list, true);
        $info = [];
        foreach ($list as $v) {
            foreach ($v as $row) {
                if ($row['expect'] == $expect) {
                    $info = $row;
                }
            }
        }
        //判断当前时间是否可以开奖
        $now = time();
        $expect_time = strtotime($info['create_time']);
        if ($expect_time > $now) return $this->error('开奖时间未到，不能开奖');
        $ext_model = new Ext();
        $ext_info = $ext_model->where('name', $name)->find();
        $expect = $ext_info['expect_type'] ? explode('-', $info['expect'])[1] : '20' . str_replace('-', '', $info['expect']);
        $has_info = LotteryCommon::getModel($name, 'code')->where('expect', $expect)->find();
        if ($has_info) return $this->error('该期已经开奖了');
        $res = LotteryCommon::getModel($name, 'code')->save([
            'code' => $info['code'],
            'expect' => $expect
        ]);
        if (!$res) return $this->error('开奖执行数据库失败');
        return $this->success('开奖成功');
    }

    /**系统开奖列表页面 */
    public function edit_system_code($name, $expect)
    {
        $list = cache('system_code_' . $name);
        if (!$list) return $this->error('参数错误');
        $list = json_decode($list, true);
        $info = [];
        foreach ($list as $v) {
            foreach ($v as $row) {
                if ($row['expect'] == $expect) {
                    $info = $row;
                }
            }
        }
        if (request()->isPost()) {
            $data = request()->Post();
            $data['system_code'] = isset($data['system_code']) ? $data['system_code'] : [];
            foreach ($list as &$v) {
                foreach ($v as &$row) {
                    if ($row['expect'] == $expect) {
                        $row['code'] = $data['code'];
                    }
                }
            }
            Cache::set('system_code_' . $name, json_encode($list));
            return $this->success('修改成功', url('system_code', 'name=' . $name));
        }
        $this->assign('info', $info);
        $this->assign("name", $name);
        return $this->fetch('', ['title' => '编辑系统开奖']);
    }

    /**系统开奖列表页面 */
    public function system_code($name = '', $yesterday = 0)
    {
        $system_list = (new Ext())->where('is_system_code', 1)->column('title, name', 'id');
        if (empty($system_list)) {
            return $this->redirect(url('system_code_admin'));
        }
        if (!$name) {
            foreach ($system_list as $v) {
                $name = $v['name'];
                break;
            }
        }
        $lotteryName = (new Ext())->where('name', $name)->find();
        if (!$lotteryName) return $this->error('彩种不存在');
        $list = Cache::get('system_code_' . $name);
        $list = json_decode($list, true);
        if ($yesterday) {
            $zt_date = date("Y-m-d",strtotime("-1 day"));
            $list = isset($list[$zt_date]) ? $list[$zt_date] : [];
        } else {
            $now_date = date("Y-m-d");
            $list = isset($list[$now_date]) ? $list[$now_date] : [];
        }
        $ext_model = new Ext();
        $ext_info = $ext_model->where('name', $name)->find();
        $code_model = LotteryCommon::getModel($name, 'code');
        foreach ($list as &$v) {
            $expect = $ext_info['expect_type'] ? explode('-', $v['expect'])[1] : '20' . str_replace('-', '', $v['expect']);
            $has_info = $code_model->where('expect', $expect)->find();
            if (!$has_info) {
                $v['status'] = 0;
            } else {
                $v['status'] = 1;
            }
        }
        $this->assign("list", $list);
        $this->assign("extList", $system_list);
        $this->assign("lotteryName", $lotteryName['title']);
        $this->assign("name", $name);
        $this->assign("yesterday", $yesterday);
        return $this->fetch('', ['title' => '系统开奖']);
    }

    /**系统开奖管理 */
    public function system_code_admin()
    {
        $model = new Ext();
        if (request()->isPost()) {
            $data = request()->Post();
            $data['system_code'] = isset($data['system_code']) ? $data['system_code'] : [];
            $list = $model->where('is_system_code', 1)->column('id');
            foreach ($list as $v) {
                if (!in_array($v, $data['system_code'])) {
                    $model->where('id', $v)->update(['is_system_code' => 0]);
                }
            }
            if ($data['system_code']) {
                $model->whereIn('id', $data['system_code'])->update(['is_system_code' => 1]);
            }
            return $this->success('修改成功');
        }
        $list = $model->where('is_system_code', 1)->column('id');
        $data = LotteryCommon::getNavLottery();
        $this->assign('list', $data);
        $this->assign('select', $list);
        return $this->fetch('', ['title' => '系统开奖管理']);
    }
}
