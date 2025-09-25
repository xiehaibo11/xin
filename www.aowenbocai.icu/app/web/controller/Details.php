<?php
namespace app\web\controller;

use app\admin\model\Ext;
use app\common\controller\LotteryCommon;
use app\common\model\MoneyHistory;
use app\common\model\FlowerHistory;
use app\common\model\Recharge;
use app\common\controller\Extend;
use app\common\model\Statis;
use app\index\model\ExtShowList;
use app\index\model\ExtShowList as ShowList;
use app\common\model\GameMoneyHistory as Diamonds;
use app\admin\model\ShopOrder;
use app\lottery\model\common\BaseBuy;
use app\lottery\model\common\BaseJoin;
use think\Db;
use core\Page;
use think\Collection;

class Details extends UserBase
{
    public function index()
    {
        return $this->fetch('index',['title' => '账户明细']);
    }

    /**时间搜索time 1 2 3 4 */
    private function initParam()
    {
        $data = request()->param();
        if (isset($data['time']) and $data['time']) {
            switch ($data['time']) {
                case 1://今天
                    $data['starttime'] = date("Y-m-d");
                    break;
                case 2://昨天
                    $data['starttime'] = date("Y-m-d", strtotime("-1 day"));
                    $data['endtime'] = date("Y-m-d", strtotime("-1 day"));
                    break;
                case 3://本月
                    $data['starttime'] = date("Y-m-01",time());
                    break;
                case 4://上一月
                    $timestamp = time();
                    $data['starttime'] = date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
                    $data['endtime'] = date('Y-m-d',strtotime($data['starttime'] . " +1 month -1 day"));
                    break;
            }
        }
        return $data;
    }
    /**获取资金明细 */
    public function list()
    {
        $MoneyHistory = new MoneyHistory;
        $data = $this->initParam();
        $user_array = [];
        $model = (new \app\web\model\User());
        if(!empty($data['userid'])){//查看代理下面的所有投注
            if ($data['userid'] != $this->user['id']) {
                $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
                if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            }
            if (!empty($data['name'])) {
                $model->where('username|nickname', 'like', '%' . $data['name'] . '%');
            }
            $user_array = $model->where('agents', $data['userid'])->column('id');
            $user_array = $user_array ? $user_array : [];
            if ($data['userid'] != $this->user['id']) {
                array_push($user_array, $data['userid']);
            }
        } else {
            array_push($user_array, $this->user['id']);
        }
        if(!empty($data['id'])){
            $MoneyHistory->where('id', $data['id']);
        }
        if(!empty($data['starttime'])){
            $MoneyHistory->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $MoneyHistory->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if(!empty($data['source'])){
            $gametype = explode('_', $data['source']);
            if(isset($gametype[1])){
                $MoneyHistory->where("remark", "LIKE", '%:'.strtoupper($gametype[1])."%");
            }else{
                $MoneyHistory->where('ext_name', $gametype[0]);
            }
        }
        
        if(isset($data['trade']) && $data['trade'] != ''){
            $MoneyHistory->where('type', $data['trade']);
        }
        if(!empty($data['remark'])){
            $MoneyHistory->where("remark", "LIKE", '%:'.$data['remark']."%");
        }

        if(isset($data['sz'])){
            $sign = $data['sz'] ? 'gt' : 'lt';
            $MoneyHistory->where('money', $sign, 0);
        }
        if(isset($data['sort']) && isset($data['type'])){

        }
        $list = $MoneyHistory->whereIn('userid', $user_array)->order('id desc')->paginate(15,'',['query' => $data]);
        foreach ($list as &$item) {
            $ext_info = (new Extend)->getInfo($item['ext_name']);
            $item['ext_info'] = [
                'logo' => isset($ext_info['logo']) ? $ext_info['logo'] : '',
                'title' => isset($ext_info['title']) ? $ext_info['title'] : ''
            ];
            $user_info = $model->find($item['userid']);
            $item['nickname'] = $user_info['nickname'];
            $item['username'] = $user_info['username'];
        }
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
    }

    /**获取鲜花明细（钻石）  */
    public function flowerList()
    {
        $data = $this->initParam();
        $Diamonds = new Diamonds;
        $user_array = [];
        $model = (new \app\web\model\User());
        if(!empty($data['userid'])){//查看代理下面的所有投注
            if ($data['userid'] != $this->user['id']) {
                $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
                if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            }
            if (!empty($data['name'])) {
                $model->where('username|nickname', 'like', '%' . $data['name'] . '%');
            }
            $user_array = $model->where('agents', $data['userid'])->column('id');
            $user_array = $user_array ? $user_array : [];
            if ($data['userid'] != $this->user['id']) {
                array_push($user_array, $data['userid']);
            }
        } else {
            array_push($user_array, $this->user['id']);
        }
        if(!empty($data['starttime'])){
            $Diamonds->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $Diamonds->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if(!empty($data['source'])){
            $gametype = explode('_', $data['source']);
            if(isset($gametype[1])){
                $Diamonds->where("remark", "LIKE", '%:'.strtoupper($gametype[1])."%");
            }else{
                $Diamonds->where('ext_name', $gametype[0]);
            }
        }
        
        if(isset($data['trade']) && $data['trade'] != ''){
            $Diamonds->where('type', $data['trade']);
        }

        if(isset($data['sz'])){
            $sign = $data['sz'] ? 'gt' : 'lt';
            $Diamonds->where('money', $sign, 0);
        }
        if(isset($data['sort']) && isset($data['type'])){

        }
        $list = $Diamonds->whereIn('userid', $user_array)->order('id desc')->paginate(15,'',['query' => $data]);
        foreach ($list as &$item) {
            $user_info = $model->find($item['userid']);
            $item['nickname'] = $user_info['nickname'];
            $item['username'] = $user_info['username'];
        }
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
    }

    /**获取充值明细 */
    public function rechargeList()
    {
        $data = $this->initParam();
        $user_array = [];
        $model = (new \app\web\model\User());
        if(!empty($data['userid'])){//查看代理下面的所有投注
            if ($data['userid'] != $this->user['id']) {
                $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
                if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            }
            if (!empty($data['name'])) {
                $model->where('username|nickname', 'like', '%' . $data['name'] . '%');
            }
            $user_array = $model->where('agents', $data['userid'])->column('id');
            $user_array = $user_array ? $user_array : [];
            if ($data['userid'] != $this->user['id']) {
                array_push($user_array, $data['userid']);
            }
        } else {
            array_push($user_array, $this->user['id']);
        }
        $Recharge = new Recharge;
        if(!empty($data['starttime'])){
            $Recharge->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $Recharge->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if(isset($data['retype']) && $data['retype'] != ''){
            $typeList = ['微信充值', '支付宝充值','微信扫码充值','支付宝扫码充值'];
            $Recharge->where('name', 'like' , "%".$typeList[$data['retype']]."%");
        }
        if(isset($data['reresult']) && $data['reresult'] != ''){
            $Recharge->where('statuss', $data['reresult']);
        }
        if(isset($data['typetype']) && $data['typetype'] != ''){
            $Recharge->where('type', $data['typetype']);
        }
        $list = $Recharge->whereIn('userid', $user_array)->order('id desc')->paginate(15,'',['query' => $data]);
        foreach ($list as &$item) {
            $user_info = $model->find($item['userid']);
            $item['nickname'] = $user_info['nickname'];
            $item['username'] = $user_info['username'];
        }
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
    }

    /**兑换列表 */
    public function exchangeList()
    {
        $data = $this->initParam();
        $user_array = [];
        $model = (new \app\web\model\User());
        if(!empty($data['userid'])){//查看代理下面的所有投注
            if ($data['userid'] != $this->user['id']) {
                $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
                if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            }
            if (!empty($data['name'])) {
                $model->where('username|nickname', 'like', '%' . $data['name'] . '%');
            }
            $user_array = $model->where('agents', $data['userid'])->column('id');
            $user_array = $user_array ? $user_array : [];
            if ($data['userid'] != $this->user['id']) {
                array_push($user_array, $data['userid']);
            }
        } else {
            array_push($user_array, $this->user['id']);
        }
        $ShopOrder = new ShopOrder;
        if(isset($data['type'])){
            $ShopOrder->where('type', $data['type']);
        }

        if(!empty($data['starttime'])){
            $ShopOrder->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $ShopOrder->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        
        if(isset($data['status'])){
            $ShopOrder->where('status', $data['status']);
        }
        $sortArr = ['ASC', 'DESC'];
        if(isset($data['sortid'])){
            $ShopOrder->order('id', $sortArr[$data['sortid'] - 1]);
        }
        if(isset($data['sortmoney'])){
            $ShopOrder->order('money', $sortArr[$data['sortmoney'] - 1]);
        }
        if(isset($data['sorttime'])){
            $ShopOrder->order('create_time', $sortArr[$data['sorttime'] - 1]);
        }
        if(isset($data['sortstatus'])){
            $ShopOrder->order('status', $sortArr[$data['sortstatus'] - 1]);
        }

        $list = $ShopOrder->field('id,shop_id,num,create_time,address_id,money,status,userid, admin_remark')->whereIn('userid', $user_array)->order('id DESC')->paginate(14,'', ['query' => $data]);
        foreach ($list as &$item) {
            $user_info = $model->find($item['userid']);
            $item['nickname'] = $user_info['nickname'];
            $item['username'] = $user_info['username'];
        }
        $list->append(['name', 'address', 'status_code']);
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
    }

    /**获取游戏记录 ---先写彩票类，分类显示*/
    public function gameOne($gameid = '')
    {
        $extShow = (new ExtShowList);
        $extShow->where('type', 1)->where('status',0);
        if($gameid != ''){
            $extShow->where('id',  $gameid);
        }
        $tables = $extShow->column(['name','title']);
        $list = [];
        if(!empty($tables)){
            foreach ($tables as $key => $value) {
                $table_name = ltrim($key, '/');
                $name = 'app\lottery\model\\'.$table_name.'\Plugin'.ucfirst($table_name).'Buy';
                $model = new $name;
                $list = $model->where('userid', $this->user['id'])->order('id DESC')->paginate(14);
            }
        }
        $list->append(['buyNum']);
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
    }

     /**彩种排序 */
     public function setSort($res, $data)
     { 
        $sort = [SORT_DESC, SORT_ASC];
        $type = ['total_money', 'status', 'create_time', 'buy', 'bouns'];
        /**排序类型 */
        $resType = isset($data['type']) ? in_array($data['type'], $type) : false;
        if(!$resType){
            $data['type'] = 'create_time';
            $data['sort'] = 0;
        }

        $sortArr = [];
        foreach ($res as &$value) {
            if(!isset($value['total_money']) && isset($value['total'])){
                $value['total_money'] = $value['total'];
            }
            $sortArr[] = $value[$data['type']];
        }
        array_multisort($sortArr, $sort[$data['sort']], $res);
        return $res;
        
     }

     public function games($gameid = '', $limit = 14)
     {
         $extShow = (new ExtShowList);
         if($gameid == ''){
             $extShow->where('type', 1)->where('status',0);
         }else{
             $extShow->where('name',  '/' . $gameid);
         }
         $tables = $extShow->column(['name','title','id']);
         $tables = array_values($tables);
         if(empty($tables)){
             return json(['err' => 0, 'msg' => '彩种不存在']);
         }
        $modelClass = new BaseJoin();
        $buyModel = new BaseBuy();
        if ($gameid) {
            $modelClass->where('ext_name', $gameid);
        }
         $data = $this->initParam();

        $user_array = [];
        $model = (new \app\web\model\User());
        if(!empty($data['userid'])){//查看代理下面的所有投注
            if ($data['userid'] != $this->user['id']) {
                $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
                if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            }
            if (!empty($data['name'])) {
                $model->where('username|nickname', 'like', '%' . $data['name'] . '%');
            }
            $user_array = $model->where('agents', $data['userid'])->column('id');
            $user_array = $user_array ? $user_array : [];
            if ($data['userid'] != $this->user['id']) {
                array_push($user_array, $data['userid']);
            }
        } else {
            array_push($user_array, $this->user['id']);
        }
        if (empty($user_array)) return json(['err' => 1, 'msg' => '没有下级会员']);
        $modelClass->whereIn('userid', $user_array);
        if(!empty($data['starttime'])){
            $modelClass->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $modelClass->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if(!empty($data['lottery_id'])){
            $buy_id = $buyModel->where('lottery_id', $data['lottery_id'])->column(['id']);
            if(empty($buy_id)){
                $list['gamename'] = $gameid ? $tables[0]['title'] : '所有彩种';
                $list['err'] = 1;
                return json($list);
            }
            $modelClass->where('buy_id', $buy_id[0]);
        }
        if(isset($data['join'])){//0自购 1发起合买 2参与合买 3 追号
            if($data['join'] < 3){
                $modelClass->where('join_status', $data['join']);
            } elseif($data['join'] == 3){
                $modelClass->where('is_chase', 1);
            }
        }
        if(isset($data['bounsStatus'])){
            switch ($data['bounsStatus']) {
                case '311':
                    $buy_id = $buyModel->where('is_join', 1)->where('status', 0)->where('end_time', '>', date('Y-m-d H:i:s', time()))->column(['id']);
                    break;
                case '0':
                    $buy_id = $buyModel->whereIn('status', [0, 1])->where('end_time', '<', date('Y-m-d H:i:s', time()))->column(['id']);
                    break;
                case '1'://中奖
                    $modelClass->where('status', 1);
                    break;
                case '2'://未中奖
                    $modelClass->where('status', 2);
                    break;
                default:
                    $buy_id = $buyModel->where('status', $data['bounsStatus'])->column(['id']);
                    break;
            }
            if (!in_array($data['bounsStatus'], [1, 2]) and empty($buy_id)) {
                $list['gamename'] = $gameid ? $tables[0]['title'] : '所有彩种';
                $list['err'] = 1;
                return json($list);
            }
            if(!empty($buy_id)){
                $modelClass->whereIn('buy_id', $buy_id);
            }
        }
        if(isset($data['type']) && isset($data['sort'])){
            $sort = $data['sort'] ? 'ASC' : 'DESC';
            $modelClass->order($data['type']." ".$sort);
        }
         $limit = $limit < 100 ? $limit : 100;
        $list = $modelClass->order('id DESC')->paginate($limit,false, ['query' => $data]);
        $list = $list->append(['BuyInfo', 'ext_txt', 'user_name'])->toArray();
        foreach ($list['data'] as &$v) {
            if ($v['BuyInfo']['is_join']) {
                $v['money'] = number_format(($v['money']/$v['BuyInfo']['total_share']) * $v['BuyInfo']['total_money'], 2);
            }
        }
        $list['err'] = 0;
        return json($list);
     }

     public function gameOther($gameid)
     {
        $tables = (new ExtShowList)->where('id',  $gameid)->column(['name','title']);
        if(empty($tables)){
            return json(['err' => 1,'data' => '' ]);
        }
        $keys = array_keys($tables)[0];
        $key = ltrim($keys,'/');
        $model = $this->setModel($tables, $key);
        $modelClass = new $model;

        $data = request()->get();
        if(!empty($data['userid'])){
            $this->user['id'] = $data['userid'];
        }
        if(!empty($data['starttime'])){
            $modelClass->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $modelClass->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        $list = $modelClass->where('userid', $this->user['id'])->order('id DESC')->paginate(14,false, ['query' => $data]);
        if (!in_array($key, ['dzp'])) {
            $list->append(['playInfo']);
        }
        $list = $list->toArray();
        $list['gamename'] = $tables[$keys];
        $list['err'] = 0;
        return json($list);

     }
     /**欢乐攻城人机数据 */
     public function attackNpc()
     {
        $modelClass = new \app\attack\model\BuyNpc;
        $data = request()->get();
        if(!empty($data['userid'])){
            $this->user['id'] = $data['userid'];
        }
        if(!empty($data['starttime'])){
            $modelClass->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $modelClass->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        $list = $modelClass->where('userid', $this->user['id'])->order('id DESC')->paginate(14,false, ['query' => $data]);
        $list->append(['playInfo']);
        $list = $list->toArray();
        $list['err'] = 0;
        return json($list);
     }

     public function setModel($tables, $key)
     {
        $table_suffix = ['buy', 'his', 'record', ''];
        if($key == 'animal' ){
            $model = 'app\animal\model\Record';
        }
        if($key == 'attack' || $key == 'fa'){
            $model = 'app\\'.$key.'\model\Buy';
        }
        if($key == 'dishu' ){
            $model = 'app\dishu\model\GameDishu';
        }
        if($key == 'poker'){
            $model = 'app\poker\model\GamePokerRecord';
        }
        if($key == 'dzp'){
            $model = 'app\common\model\GameDzpHistory';
        }

        $key = $key != 'fish50' ? $key : 'fish';
        if(!isset($model)){
            foreach ($table_suffix as $value) {
                $table = 'kr_plugin_'.$key.'_'.$value;
              
                 $exist = Db::query('show tables like "'.$table.'"');
                 if($exist){
                     $existKey = ucfirst($value);
                     break;
                 }
            }
            $existKey = isset($existKey) ? $existKey : '';
            $modelName = $key != 'fish' ? $key : 'fish50';
            $model = 'app\\'.$modelName.'\model\Plugin'.ucfirst($key).$existKey;
        }
        return $model;
     }

    /** 代理报表 */
    public function get_self_statistics()
    {
        $model = new Statis();
        $res = $model->getSelfStatistics($this->user['id']);
        return $res;
    }

    /** 获取走势数据 */
    public function get_trend_data($name, $limit = 30)
    {
        $ext_info = (new Ext())->where('name', $name)->find();
        if (!$ext_info) return json(['err' => 1, 'msg' => '彩种不存在']);
        $limit = $limit > 100 ? 100 : $limit;
        $code_model = LotteryCommon::getModel($name, 'code');
        $list = $code_model->order('expect desc')->paginate($limit)->each(function($item, $key) use($ext_info){
            if ($ext_info['expect_type']) {
                $item['sort_expect'] =  $item['expect'];
            } else {
                $item['sort_expect'] =  substr($item['expect'],8);
            }
            $item['code'] = $item['code_array'];
            return $item;
        });
        $res_data = [];
        $list = $list->toArray();
        krsort($list['data']);
        $cp_type = LotteryCommon::getCpType($name);
        switch ($cp_type) {
            case 'ssc':
                $miss_array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                break;
            case 'syxw':
                $miss_array = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'];
                break;
            case 'pk10':
                $miss_array = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
                break;
            case 'ks':
                $miss_array = ['1', '2', '3', '4', '5', '6'];
                break;
             case 'pc28':
                $miss_array = [];
                break;
        }
        $miss_num = [];
        foreach ($list['data'] as $v) {
            foreach ($miss_array as $key => $row) {
                if (in_array($row, $v['code'])) {
                    $miss_num[$key] = 0;
                } else {
                    $miss_num[$key] = isset($miss_num[$key]) ? $miss_num[$key]+ 1 : 1;
                }
            }
            array_push($res_data, [
                'sort_expect' => $v['sort_expect'],
                'code' => $v['code'],
                'expect' => $v['expect'],
                'miss' => $miss_num
            ]);
        }
        return json(['err' => 0, 'data' => $res_data]);
    }
}
