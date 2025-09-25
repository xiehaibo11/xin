<?php
namespace app\admin\controller;

use core\Setting;
use app\common\model\MoneyHistory;
use app\common\model\Statis;
use app\admin\model\User;
use app\admin\model\ExtShowList;
use org\MyFunction;

class Statistics extends Base
{
    /**列表*/
    private function getWhere()
    {
        $where = [];
        $user = new User;
        $data = request()->param();
        $statis = new Statis;
        if(isset($data['words']) and $data['words']){
            $user->where('nickname|username', 'LIKE', "%".$data['words']."%");
            $userlist = $user->column(['id']);
            $where['userid'] = ['in', $userlist];
        }
        if(isset($data['type']) and $data['type']){
            $where['user_type'] = ['eq', $data['type']];
        }
        if (!$data['endtime']) {
            $last = $statis->order("id DESC")->find();
            if ($last) {
                $where['statis_time'] = ['eq', $last['statis_time']];
            }
        } else {
            $where['statis_time'] = ['eq', $data['endtime']];
        }
        return $where;
    }

    /**列表*/
    public function index()
    {
        $user = new User;
        $data = request()->param();
        $statis = new Statis;
        $setting = Setting::get(['recharge_award']);
        $where = $this->getWhere();
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        $list = $statis->where($where)->paginate($pageSize, false, ['query'=>$data]);
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'send', 'royalty', 'rebate', 'game_out', 'game_in', 'gain'];
        $sum_page = [];
        foreach ($list as &$v) {
            $start_statis = '';
            if ($data['starttime']) {
                $start_statis = $statis->where('userid', $v['userid'])->where('statis_time', $data['starttime'])->find();
            }
            foreach ($typeToStr as $row) {
                $v[$row] = $start_statis ? $v[$row] - $start_statis[$row] : $v[$row];
                $sum_page[$row] += $v[$row];
            }
            $user_info = $user->find($v['userid']);
            $v['nickname'] = $user_info['nickname'];
            $v['username'] = $user_info['username'];
            $v['type_name'] = $user_info['type_name'];
            $v['money'] += $v['game_money']/$setting['recharge_award'];
            $sum_page['money'] += $v['money'];
        }
        $sum_data = $this->getAllData($typeToStr);

        return $this->fetch('', ['title' => '资金统计', 'list' => $list, 'sum_data' => $sum_data, 'sum_page' => $sum_page, 'query' => $data]);
    }

    /**
     * 资金统计excel导出
     */
    public function statisticsExportExcel()
    {
        $user = new User;
        $data = request()->param();
        $statis = new Statis;
        $setting = Setting::get(['recharge_award']);
        $where = $this->getWhere();
        $list = $statis->where($where)->select()->toArray();
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'send', 'royalty', 'rebate', 'game_out', 'game_in', 'gain'];
        foreach ($list as &$v) {
            $start_statis = '';
            if ($data['starttime']) {
                $start_statis = $statis->where('userid', $v['userid'])->where('statis_time', $data['starttime'])->find();
            }
            foreach ($typeToStr as $row) {
                $v[$row] = $start_statis ? $v[$row] - $start_statis[$row] : $v[$row];
            }
            $user_info = $user->find($v['userid']);
            $v['nickname'] = $user_info['nickname'] . "(" . $user_info['username'] . ")";
            $v['type_name'] = $user_info['type_name'];
            $v['money'] += $v['game_money']/$setting['recharge_award'];
        }
        $sum_data = $this->getAllData($typeToStr);
        array_push($list, $sum_data);
        $excel_title = ["用户昵称（用户名）", "会员类型", "余额", "投注", "中奖", "游戏币转出", "游戏币转入", "充值", "兑换", "活动", "提成", "返点", "盈利"];//标题
        $excel_filed = ["nickname", "type_name", "money", "spend", "award", "game_out", "game_in", "recharge", "change", "send", "royalty", "rebate", "gain"];//数据字段
        $excel_width = ["20", "10", "10", "10", "10","13","13","10","10","10","10","10","10"];
        MyFunction::comm_exportExcel($excel_title, $excel_filed, $excel_width, $list, "资金统计数据" . date('Ymd', time()));
    }

    /**统计获取当前条件下所有数据综合 */
    public function getAllData($typeToStr)
    {
        $statis = new Statis;
        $data = request()->param();
        $sum_field = '';
        $where = $this->getWhere();
        $res = [];
        foreach ($typeToStr as $row) {
            $sum_field .= ($sum_field ? ',' : '') . 'SUM(`' . $row . '`) as `'. $row .'`';
        }
        $endAll = $statis->field($sum_field . ', SUM(money) as money, SUM(game_money) as game_money')->where($where)->select()->toArray();

        if ($data['starttime']) {
            $where['statis_time'] = ['eq', $data['starttime']];
            $startAll = $statis->field($sum_field)->where($where)->select()->toArray();
        }
        if (!empty($endAll)) {
            foreach ($endAll[0] as $key => $v) {
                $res[$key] = $v;
                if (isset($startAll) and !empty($endAll)) {
                    $res[$key] -= $startAll[0][$key];
                }
            }
        }
        $res['money'] += $res['game_money'];
        return $res;
    }

    /**代理列表*/
    public function agent_index($userid = '', $is_today = 1)
    {
        $user = new User;
        $data = request()->param();
        $statis = new Statis;
        if (!$userid) {
            $user->where('type', 2)->where('agents', 0);
        } else {
            $user->where(function($query) use ($userid){
                $query->whereOr('id', $userid)
                    ->whereOr('agents', $userid);
            });
        }
        if (isset($data['words'])) {
            $user->where('username|nickname', 'like', '%' . $data['words'] . '%');
        }
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        $list = $user->field('id, nickname, type, username')->order('agents asc')->paginate($pageSize, false, ['query' => $data]);
        $page_html = $list->render();
        $list = $list->append(['type_name'])->toArray();
        $setting = Setting::get(['recharge_award']);
        foreach ($list['data'] as &$value) {
            if ($is_today) {
                $value['statistics'] = $statis->getTodayStatistics($value['id'], $value['id'] == $userid ? true : false);
                $value['statistics']['money'] += $value['statistics']['game_money']/$setting['recharge_award'];
            } else {
                $value['statistics'] = $statis->getAgentsStatistics($value['id'], 0, $value['id'] == $userid ? true : false);
            }
        }
        $sum_page = [];
        foreach ($list['data'] as &$value) {
            foreach ($value['statistics'] as $key => $row) {
                $sum_page[$key] = isset($sum_page[$key]) ? $sum_page[$key] + $row : $row;
            }
        }
        $this->assign('userid', $userid);
        $this->assign('is_today', $is_today);
        $view = 'agent_index';
        if ($is_today) {
            $view = 'today_agent_index';
        }

        return $this->fetch($view, ['title' => '资金统计', 'list' => $list['data'], 'sum_page' => $sum_page, 'page_html' => $page_html, 'query' => $data]);
    }

    /**
     * 资金统计excel导出
     */
    public function statisticsAgentExportExcel($userid = '')
    {
        $user = new User;
        $data = request()->param();
        $statis = new Statis;
        if (!$userid) {
            $user->where('type', 2)->where('agents', 0);
        } else {
            $user->where(function($query) use ($userid){
                $query->whereOr('id', $userid)
                    ->whereOr('agents', $userid);
            });
        }
        if (isset($data['words'])) {
            $user->where('username|nickname', 'like', '%' . $data['words'] . '%');
        }
        $list = $user->field('id, nickname, type, username')->order('agents asc')->select();
        $list = $list->append(['type_name'])->toArray();
        foreach ($list as &$value) {
            $statistics = $statis->getAgentsStatistics($value['id'], 0, $value['id'] == $userid ? true : false);
            $value['nickname'] .= '(' . $value['username'] . ')';
            foreach ($statistics as $key => $v) {
                $value[$key] = $v;
            }
        }
        $excel_title = ["用户昵称（用户名）", "会员类型", "团队余额", "团队投注", "团队中奖", "游戏币转出", "游戏币转入", "团队充值", "团队兑换", "团队活动", "团队提成", "团队返点", "团队盈利"];//标题
        $excel_filed = ["nickname", "type_name", "money", "spend", "award", "game_out", "game_in", "recharge", "_change", "send", "royalty", "rebate", "gain"];//数据字段
        $excel_width = ["20", "10", "10", "10", "10","13","13","10","10","10","10","10","10"];
        MyFunction::comm_exportExcel($excel_title, $excel_filed, $excel_width, $list, "代理资金统计数据" . date('Ymd', time()));
    }

    /**统计获取今天的数据 */
    public function getTodayAllData($typeToStr)
    {
        $moneyHistory = new MoneyHistory();
        $today = date("Y-m-d");
        $moneyHistory->field("SUM(money) as allMoney, type");
        $list = $moneyHistory->where('create_time', '>=', $today)->group('type')->select()->toArray();
        $res = [];
        foreach ($list as $key => $value) {
            $res[$typeToStr[$value['type']]] = round(abs($value['allMoney']), 4);
            foreach ($typeToStr as $row) {
                $res[$row] = isset($res[$row]) ? $res[$row] : 0;
            }
        }
        $setting = Setting::get(['recharge_award']);
        $total_money = (new User())->field('SUM(money) as money, SUM(game_money) as game_money')->where('type', 'neq', 0)->select()->toArray();
        $res['money'] = round($total_money[0]['money'] + $total_money[0]['game_money']/$setting['recharge_award'], 2);
        $res['gain'] = round($res['award'] - $res['spend'] + $res['send'] + $res['rebate'] + $res['royalty'] - $res['game_out'] + $res['game_in']);
        return $res;
    }

    /**图形统计 */
    public function graphList()
    {
        $money = new MoneyHistory;
        $sellte = $money->newSatis();
        if($sellte['err'] == 1){
            // $this->error('操作错误');
        }

        if(request()->IsAjax()){
            $data = request()->get();
            $userid = '';
            if(isset($data['value']) && $data['value'] !=''){
                $userid = (new User)->where('type', $data['value'])->column('id');
            }
            $month = (int) date("m");
            $statis = new Statis;
            $graphData = [];
            for($i = 0; $i < $month; $i++){
                $res = $statis->getGraphData($i, $userid);
                array_push($graphData, $res);
            }
            $graphData = $this->getData($graphData);
            return json(['err' => 0, 'data' =>['data' => $graphData, 'length' => $month]]);
        }
        return $this->fetch('graph', ['title' => '资金柱形图统计']);
    }

    public function graphAll()
    {
        if(request()->IsGet()){
            $year = date("Y");
            $data = request()->get();
            $userid = '';
            if(isset($data['value']) && $data['value'] !=''){
                $userid = (new User)->where('type', $data['value'])->column('id');
            }
            $money = new MoneyHistory;
            
            $data[] = (new Statis)->getGraphData(1001, $userid);
            $data = $this->getData($data);
            return json(['err' => 0, 'data' =>['data' => $data, 'length' => date("m")]]);  
        }
    }

    public function getData($data)
    {
        $res['spend'] = array_column($data, 'spend');
        $res['send'] = array_column($data, 'send');
        $res['gain'] = array_column($data, 'gain');
        $res['award'] = array_column($data, 'award');
        $res['recharge'] = array_column($data, 'recharge');
        $res['percentage'] = array_column($data, 'percentage');
        $res['change'] = array_column($data, 'change');
        return $res;
    }


    public function spAdGraph()
    {
        return $this->fetch('extgraph', ['title' => '奖金统计']);
    }

    public function serachGraph()
    {
       return (new Statis)->getAllStatis();
    }
    
    public function todayGraph()
    {
        $history = new MoneyHistory;
        $today = date("Y-m-d");
        $spend = $history->getSumMoney($today, $today, 6);
        $award = $history->getSumMoney($today, $today, 1);
        $data = [
            'spend' => -$spend ?? 0,
            'award' => $award ?? 0,
        ];
        return $data;
    }

    public function extOfGraph()
    {
        $exlist = (new  ExtShowList)->getUseList();
        $this->assign('extName', $exlist);
        return $this->fetch('extofgraph', ['title' => '模块奖金统计']);
    }

    public function getExtData()
    {
        $data = request()->param();
        $start = empty($data['startTime']) ? '' : $data['startTime'];
        $end = empty($data['endtime']) ? '' : $data['endtime'];
        
        $ext = ltrim($data['ext'], '/');
        $ext = [$ext,'/'.$ext];
        $history = new MoneyHistory;
        $spend = $history->getSumMoney($start, $end, 6, $ext);
        $award = $history->getSumMoney($start, $end, 1,$ext);
        $data = [
            'spend' => -$spend ?? 0,
            'award' => $award ?? 0,
        ];
        return $data;
    }

    public function getTodayList()
    {
        $data = request()->param();
        $user = new User;
        $moneyHistory = new MoneyHistory;
        $moneyHistory->field("userid");
        if(!empty($data['words'])){
            $user->where('username|nickname', 'like', '%'.$data['words'].'%');
            $userlist = $user->column(['id']);
            $moneyHistory->whereIn('userid', $userlist);
        } else {
            $userlist = $user->where('type', 'eq', 0)->column('id');
            $moneyHistory->whereNotIn('userid', $userlist);
        }
        $today = date("Y-m-d");
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        $list = $moneyHistory->where('create_time', '>=', $today)->group('userid')->paginate($pageSize, false, ['query'=>$data]);
        $page_html = $list->render();
        $list = $list->toArray();
        $setting = Setting::get(['recharge_award']);
        $sum_page = [];
        if(!empty($list)){
            $typeToStr = ['spend', 'award', 'recharge', 'change', 'send', 'royalty', 'rebate', 'game_out', 'game_in'];
            foreach ($list['data'] as &$value) {
                $userinfo = (new User)->find($value['userid']);
                if(!$userinfo){
                    continue;
                }
                $value['money'] = $userinfo['money'] + $userinfo['game_money']/$setting['recharge_award'];
                $value['game_money'] = $userinfo['game_money'];
                $value['nickname'] = $userinfo['nickname'];
                $value['username'] = $userinfo['username'];
                $value['user_type_name'] = $userinfo['type_name'];
                $allMoney = $moneyHistory->field('SUM(money) as money, type')
                    ->where('create_time', '>=', $today)
                    ->where('userid', $value['userid'])->group('type')->select()->toArray();
                foreach ($allMoney as $v2) {
                    $value[$typeToStr[$v2['type']]] = round(abs($v2['money']), 4);
                    $sum_page[$typeToStr[$v2['type']]] += $value[$typeToStr[$v2['type']]];
                }
                foreach ($typeToStr as $row) {
                    $value[$row] = isset($value[$row]) ? $value[$row] : 0;
                }
            }
            foreach ($list['data'] as &$value) {
                $value['gain'] = $value['award'] - $value['spend'] + $value['send'] + $value['rebate'] + $value['royalty'] - $value['game_out'] + $value['game_in'];
                $sum_page['money'] += $value['money'];
                $sum_page['gain'] += $value['gain'];
            }
            $sum_data = $this->getTodayAllData($typeToStr);
            $this->assign('list',$list['data']);
            $this->assign('page_html',$page_html);
            $this->assign('sum_page',$sum_page);
            $this->assign('sum_data',$sum_data);
        }else{
            $this->assign('_empty', 1);
        }
        return $this->fetch('today_index', ['title' => '当日资金明细统计','query' => $data]);
    }

     /**
     * 处理今日数据排序
     */
    public function dealTodayData($data, $sort)
    {
        $postSort = explode('_', $sort);
        $sortArr = ['spend','award','recharge','change','send','percentage'];
        $sortKey = $sortArr[$postSort[0]];
        $sortData = [];
        foreach($data as $key => $value){
            $sortData[] = $value[$sortKey];
        }
        if($sort != ''){
            $order = [SORT_ASC,SORT_DESC];
            $order = $order[$postSort[1]];
            array_multisort($sortData, $order, $data);
        }
        return $data;
    }
}
