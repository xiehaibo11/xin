<?php
namespace app\lucky\controller;

use app\index\controller\Base;
use think\Controller;
use app\lucky\model\PluginLucky;
use app\lucky\model\PluginLuckyAward;
use app\lucky\model\PluginLuckyHis as His;

class Index extends Base
{
    public function index()
    {
         return $this->fetch('index',['title' => '幸运']);
    }

    public function getSid()
    {
        return json(['sid' => $this->user['sid']]);
    }

    /**获取历史记录 */
    public function getHisList()
    {
        $res = (new His)->hisList($this->user['id']);
        $res->append(['codeRs', 'PlanList']);
        $res = $res->toArray();
        $res['err'] = 0;
        return json($res);
    }

    public function getInfo()
    {
        return json(['money' => $this->user['money']]);
    }

    /**整合整隔流程 */
    public function lucky($money = '', $type = '')
    {
        $money = abs(intval($money));
        if($money == 0 || $type == ''){
            return json(['err' => 1, 'msg' => '操作错误']);
        }
        if($this->user['money'] < $money){
            return json(['err' => 2, 'msg' => '对不起，您的余额不足，请先充值']);
        }
        $info = (new PluginLucky)->get(['sign' => $type, 'status' => 1]);
        if(!$info){
            return json(['err' => 8]);
        }
        $betting_res = (new His)->betting($money, $type, $this->user);
        if(!isset($betting_res['betting_id'])){
            return json($betting_res);
        }
        $this->user['money'] -= $money;
        /**如果上面投注记录成功则进行下列操作（目前暂时省略，待完成下面系列在添加条件,还要修改上诉返回值参数） */
        $open_res = (new Common)->openAward($type);
        /**中奖写入资金明细*/
        $award_res = (new His)->setAward($money, $open_res, $betting_res['betting_id'], $this->user);
        if(!isset($award_res['award_money'])){
            return json($award_res);
        }
        $this->user['money'] = $award_res['after'];
        $res['money'] = $award_res['after'];
        $res['bouns'] = $award_res['award_money'];
        $res['common'] = $open_res['common'];
        $res['lucky'] = $open_res['lucky'];
        $res['sp_award']['common'] = $open_res['sp_award']['common'];
        $res['sp_award']['lucky'] = $open_res['sp_award']['lucky'];
        $res['err'] = 0;
        return json($res);
    }
    
    /**获取中奖规则 */
    public function getRules()
    {
        $res = (new PluginLuckyAward)->getAllAward(1);
        return json(['err' => 0, 'data' => $res]);
    }

}
