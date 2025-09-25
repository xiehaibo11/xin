<?php
namespace app\cars\controller;

use app\cars\model\PluginCars;
use app\cars\model\PluginCarsHis;
use app\cars\model\PluginCarsCode as Open;
use app\common\model\GameMoneyHistory;
use think\Controller;
use core\game\GridGame;
use core\game\Issue;
use core\game\Prize as APrize;

class Prize
{
	public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
       
    }

	static $config = [
        'begin_time' => '00:00:00',
        'end_time' => '24:00:00',
        'refresh_time' => 30 //单位“秒”
    ];
    public function test()
    {
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
        $prize = new APrize;
        $res = $prize->createCode($award, $max, 10);//10000概率基数 设置
        // $res = [
        //     'code' => 4,
        //     'sp_award' => 0.2,
        //     'sign' => 4
        // ];
        /**开奖号码入库 */
        $open = new Open;

        $open_data = [
            'code' => json_encode($res['code']),
            'multiple' => $res['sp_award'],
            'sign' => $res['sign']
        ];
        $open_res = $open->addOpen($open_data);
        if(!$open_res){
            return json(['err' => 2, 'msg' => '开奖号码入库失败']);
        }

        /**派奖 -- 返回中奖数据*/
        $history = new PluginCarsHis;
        $list = $history->where('status', 0)->select();
        $list->append(['codeList']);
        $list = $list->toArray();

        if(!$list){
            return json(['err' => 3, 'msg' => '没有投注记录或已派奖']);
        }
        /**修改状态 */
        $history->setField('status', 1);
        $award_res = $prize->openAward($res, $list);
        if(empty($award_res)){
            return json(['err' => 5, 'msg' => '没有中奖数据']);
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

    public function award($issue = '')
    {
        /**获取期号 */
        $issue = $issue != '' ? $issue : ((new Issue(self::$config))->timeToIssue()['issue']);
        /**获取奖项相关的的赔率及概率 */
        $cars = new PluginCars;
        $award = $cars->getAwards();
        $award->visible(['sign', 'multiple', 'sp']);
        $award->append(['PlanArr']);
        $award = $award->toArray();
        if(!$award){
            return json(['err' => 1, 'msg' => '该游戏还未添加中奖项']);
        }
        /**开奖号码入库 */
        $open = new Open;
        $info = $open ->where('issue', $issue)->find();
        $prize = new APrize;
        if(!$info){
            $max = $cars->max('sign');
            $res = $prize->createCode($award, $max, 10);//10000概率基数 设置
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
            return json(['err' => 3, 'msg' => '没有投注记录或已派奖']);
        }
        /**修改状态 */
        $history->where('issue', $issue)->setField('status', 1);
        $award_res = $prize->openAward($res, $list);
        if(empty($award_res)){
            return json(['err' => 5, 'msg' => '没有中奖数据']);
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

}