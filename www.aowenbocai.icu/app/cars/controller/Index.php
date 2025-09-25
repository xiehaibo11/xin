<?php
namespace app\cars\controller;

use app\common\model\UserBag;
use app\index\controller\Base;
use app\cars\model\PluginCars;
use app\cars\model\PluginCarsHis;
use app\cars\model\PluginCarsCode as Open;
use app\common\model\GameMoneyHistory;
use think\Controller;
use core\game\GridGame;
use core\game\Issue;
use core\game\Prize;

class Index extends Base
{
	static $config = [
        'begin_time' => '00:00:00',
        'end_time' => '24:00:00',
        'refresh_time' => 30 //单位“秒”
    ];

    public function index()
    {   
        
         return $this->fetch('index',['title' => '宝马奔驰']);
    }

    public function getSid()
    {
        return json(['sid' => $this->user['sid']]);
    }

    /**进入获取初始化信息 */
    public function getInfo()
    {
        $cache = new GridGame(1, $this->user['id']);
        $res = $cache->getInfo('', $this->user['money'],'', self::$config);
        $res['cars'] =  (new PluginCars)->column(['sign', 'multiple']);
        $setting = \core\Setting::get(['free_coupon_coin']);
        $coupon = (new UserBag())->where('param_name', 'cars_coupon')->where('userid', $this->user['id'])->find();
        $res['coupon'] = empty($coupon) ? 0 : $coupon['num'];
        $res['coupon_coin'] = $setting['free_coupon_coin'];
        return json(['err' =>0, 'data' => $res]);
    }

    public function join()
    {
        if(request()->IsPost()){
            $data = request()->param();
            // $data['copy_last'] = json_encode([['money'=>55, 'plan' =>3],['money'=>55, 'plan' =>4],['money'=>55, 'plan' =>1],['money'=>55, 'plan' =>6]]);
            if(!(isset($data['money']) && isset($data['plan'])) && !isset($data['copy_last'])){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $cache = new GridGame(1, $this->user['id'], $data['plan'] ?? '', $this->user['money']);
            $res = $cache->bet($data['copy_last'] ?? '', $data['money'] ?? '');
            return json($res);
        }
    }

    /**下注确认 */
    public function checkOrder($issue)
    {
        $issueModel = new Issue(self::$config);
		$now_issue = intval($issueModel->timeToIssue()['issue']);
		if($now_issue != intval($issue)){
			return json(['err' => 3, 'msg' => '操作错误']);
		}
        $cache = new GridGame(1, $this->user['id'], '', $this->user['money']);
        $res = $cache->checkOrder();
        if(isset($res['msg'])){
            return json($res);
        }
        $data = $res['data'];
        $his = [
            'userid'=> $data['userid'],
            'code' => $data['plan'],
            'money' => $data['money'],
            'issue' => $issue,
        ];
        $his_res = (new PluginCarsHis)->addData($his);
        if(isset($his_res['err']) || !$his_res) {
            return json(['err' => 1, 'msg' => '写入数据错误']);
        }
        $money_his = [
            'userid' => $data['userid'],
            'money' => -$data['money'],
            'remark' => '幸运汽车'.$issue
        ];
        $money_res = (new GameMoneyHistory)->write($money_his);
        if($money_res['code'] == 1){
            return json(['err' => 0, 'msg' => '下注成功', 'money' => $money_res['afterMoney']]);
        }
        return json(['err' => 2, 'msg'=> '资金明细写入错误']);
    }


    /**清除缓存投注 */
    public function cleanBetting()
    {
        $cache = new GridGame(1, $this->user['id'], '', '');
        $res = $cache->clean();
        if(!$res['err']){
            $res['money'] = $this->user['money'];
        }
        return json($res);
    }
	
	public function getOpen($issue)
	{
		
        $info = (new Open)->where('issue', $issue)->column('code');
		if(empty($info)){
			return json(['err' => 1, 'msg' => '']);
		}
		return json(['err' => 0, 'data' => $info[0]]);
	}
    public function award($issue)
    {
        /**获取期号---传入OR在线获取 */
        //$issue = (new Issue)->timeToIssue()['issue'];
        /**获取奖项相关的的赔率及概率 */
        $cars = new PluginCars;
        $award = $cars->getAwards();
        $award->visible(['sign', 'multiple', 'sp']);
        $award->append(['PlanArr']);
        $award = $award->toArray();
        if(!$award){
            return json(['err' => 1, 'msg' => '该游戏还未添加中奖项']);
        }
        $max = $cars->max('sign');
        $prize = new Prize;
        $res = $prize->createCode($award, $max);
        // $res = [
        //     'code' => 4,
        //     'sp_award' => 0.2,
        //     'sign' => 4
        // ];
        /**开奖号码入库 */
        $open = new Open;
        $info = $open ->where('issue', $issue)->find();
        if(!$info){
            $open_data = [
                'issue' => $issue, 
                'code' => json_encode($res['code']),
                'multiple' => $res['sp_award'],
                'sign' => $res['sign']
            ];
            $open_res = $open->addOpen($open_data);
            if(!$open_res){
                return json(['err' => 2, 'msg' => '开奖号码入库失败']);
            }
        }else{
            $res = $info->toArray();
            $res['sp_award'] = $res['multiple'];
        }
        /**派奖 -- 返回中奖数据*/
        $history = new PluginCarsHis;
        $list = $history->where('issue', $issue)->where('status', 0)->select();
        $list->append(['codeList']);     
        $list = $list->toArray();

        if(!$list){
            return json(['err' => 3, 'msg' => '您未参与游戏或已派奖']);
        }
        /**修改状态 */
        $history->where('issue', $issue)->setField('status', 1);
        $award_res = $prize->openAward($res, $list);
        if(empty($award_res)){
            return json(['err' => 5, 'msg' => '很遗憾你未中奖']);
        }
        
        /**派奖 -- 根据中奖数据以及游戏本身的奖金计算计算金额更改数据 */
        $saveAll = [];
        $multiple = $res['sp_award'];
        $history_money = [];
        foreach ($award_res as $value) {
            $bouns = $value['money'] * $multiple;
            $saveAll[] = [
                'id' => $value['id'],
                'bouns' => $bouns
            ];
            if(array_key_exists($value['userid'], $history_money)){
                $history_money[$value['userid']] += $bouns;
            }else{
                $history_money[$value['userid']] = $bouns;
            }
        }
        $history->saveAll($saveAll);

        foreach ($history_money as $key => $value) {
            $money_history = [
                'userid' => $key,
                'money' => $value,
                'type' => 1,
                'remark' => '游戏中奖'
            ];
            $money_res = (new GameMoneyHistory)->write($money_history);
        }
        return json(['err' => 0, 'msg' => '派奖完成']);
    }

    /**获取派奖状态，用户页面 */
    public function getLastData($issue)
    {
        $history = new PluginCarsHis;
        $where = ['issue' => $issue, 'userid' => $this->user['id']];
        $list = $history->where($where)->count();
        if(!$list){
            return json(['err' => 1, 'msg' => '该期你未进行下注']);
        }
        $list = $history->where($where)->where('status', 1)->column('bouns');
        if(empty($list)){
            return json(['err' => 2, 'msg' => '等待派奖']);
        }
        $sum = array_sum($list);
        return json(['err' => 0, 'money'=> $sum]);
    }

    /**更新用户活动时间 */
    public function updateTime()
    {
        (new GridGame)->setActionTime($this->user['id'], 1);
    }

    /**获取用户所在房间其他用户 */
    public function getRoomInfo()
    {
        $res = (new GridGame)->room(1,$this->user['id'], 500);
        if(isset($res['err'])){
            return json($res);
        }
        $res = $res->toArray();
        return json(['err' => 0, 'data' => $res]);
    }
	
	/**获取历史记录 */
    public function getHis()
    {
        $res = (new PluginCarsHis)->getList(['userid' => $this->user['id']]);
        $res->append(['ShowCode']);
		$res = $res->toArray();
        $res['err'] = 0;
        return json($res);
    }
	
	/**获取开奖数据前6条*/
	public function getOpenList()
	{
        $info = (new Open)->order("id desc")->limit(5)->select();
		$info = $info->toArray();
		return json(['err' => 0, 'data' => $info]);
    }
    /**获取最后一期历史记录 */
    public function getLast()
    {
        $res = (new PluginCarsHis)->where('userid', $this->user['id'])->order('id DESC')->find();
        if($res){
            return json(['err' => 0, 'data' => $res]);
        }
        return json(['err' => 1, 'msg' => '您还未投注记录']);
    }
}
