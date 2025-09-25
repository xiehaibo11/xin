<?php
namespace app\lottery\model\common;

use app\common\controller\LotteryCommon;
use app\lottery\controller\Lottery;
use think\Model;
use app\common\model\MoneyHistory;

class BaseExpect extends Model
{
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';
    protected $ext_name = '';
    protected $name = 'lottery_expect';//表名
    protected $insert = ['ext_name'];
    //status   0  未出票  1  已出票   2  已派奖 3 中奖停止  5 停止追号  6 流产撤单  7 系统撤单 8 用户撤单

    // 定义全局的查询范围
    protected function base($query)
    {
        if ($this->ext_name) {
            $query->where('ext_name', $this->ext_name);
        }
    }

    public function setExtNameAttr($value)
    {
        return $this->ext_name;
    }

    /**获取期号信息 */
    public function get_status_data()
    {
        //$status = ['0' => '未出票', '1' => '已出票', '2' => '已派奖', '3' => '中奖停止', '5' => '停止追号', '6'=>'流产撤单', '7' => '系统撤单', '8' => '用户撤单'];
        $status = ['0' => '进行中', '1' => '进行中', '2' => '已派奖', '3' => '中奖停止', '5' => '停止追号', '6'=>'流产撤单', '7' => '系统撤单', '8' => '用户撤单'];
        return $status;
    }

    /**获取器--获取相应的下单情况 */
    public function getBuyAttr($value, $data)
    {
       $res = ((LotteryCommon::getModel($data['ext_name'], 'buy')))->get($data['buy_id']);
       if($res){
           return $res->toArray();
       }
       return  $res;
    }

    /**
    * 获取购买的所有投注期数
    */
    public function getExpect($buy_id)
    {
        $buy = ((LotteryCommon::getModel($this->ext_name, 'buy')))->find($buy_id);
        $res = $this->where('buy_id', $buy_id)->select();
        $count_multiple = $this->where('buy_id', $buy_id)->sum('multiple');
        foreach ($res as $key => $value) {
            $single_money = $buy['total_money'] / $count_multiple;
            $res[$key]['money'] = $single_money * $value['multiple'];
            $res[$key]['end_time'] = $this->getExpectTime($value['expect'])['data'];
            $code = ((LotteryCommon::getModel($this->ext_name, 'code')))->where('expect', $value['expect'])->find();
            if ($code) {
                $res[$key]['code'] = '<em class="green">' . $code['code'] . '</em>';
                $bonus = $res[$key]['bonus'];
                if ($bonus) {
                    $res[$key]['bonus'] = '<em class="red">中奖 ' . $bonus . ' 元</em>';
                } else {
                    $res[$key]['bonus'] = '<em class="hs">未中奖</em>';
                }
            } else {
                $res[$key]['code'] = '<em class="hs">未开奖</em>';
                $res[$key]['bonus'] = '<em class="red">等待开奖</em>';
            }
            if ($value['status'] == 7) {
                $res[$key]['bonus'] = '<em class="hs">系统撤单</em>';
            }

        }
        return $res;
    }

    /**获取器--获取订单 */
    public function getNewStatusAttr($value, $data)
    {
        if(!$data['status']){
            $res = $this->getExpectTime($data['expect']);
            $data['status'] = strtotime($res['data']) < time() ? 4 : $data['status'];
        }
        return $data['status'];
    }

    /**获取器--获取订单 */
    public function getCodeAttr($value, $data)
    {
        $code = ((LotteryCommon::getModel($data['ext_name'], 'code')))->where('expect', $data['expect'])->column('code');
        return !empty($code) ? explode(',', $code[0]) : $code;
    }

    /**
     * 获取开奖时间 -- 期数获取时间
     * $expect  期数号码
     * 销售时间：9:00-23:00，共84期  每十分钟一期
     */
    public function getExpectTime($expect)
    {
        return ['err' => 0, 'data' =>  date("Y-m-d H:i:s", (new Lottery())->issueToTime($this->ext_name, $expect))];
    }

    /**获取符合条件的未派奖的单子 */
     public function getPrizeList($issue)
     {
        $list = $this->where('expect', $issue)->where('status', '<=', 1)->select()->append(['buy']);
        $list = $list->toarray();
        $join = (LotteryCommon::getModel($this->ext_name, 'join'));
        $setting = (new \core\Setting)->get(['openBd', 'Bdpercent','coin_lottery']);
        $settingCoin = $setting['coin_lottery'] ? $setting['coin_lottery'] : 1;
       
        $prize = [];
        $retrunJoin = [];
        $expect = [];
        $buy = [];
        foreach ($list as $key => $value) {
            if($value['status'] == 1){
                $prize[] = $value;
                continue;
            }
            if($value['buy']['is_join'] == 1){
                $joinlist = $join->where('buy_id', $value['buy_id'])->column(['userid', 'money']); 
                $num = array_sum($joinlist);
                $bdpercent = floatval($setting['Bdpercent']) > 0 && $setting['openBd'] ? floatval($setting['Bdpercent']) : 100;
                $buyPercent = floatval(($num + $value['buy']['assure_money']) / $value['buy']['total_money'] * 1000) / 10;
                if($buyPercent < $bdpercent){
                    $retrunJoin[$value['buy']['lottery_id']] = [ 
                                            'money' => $joinlist,
                                            'bd' => ['userid' => $value['buy']['userid'], 'money' => $value['buy']['assure_money']]
                                        ];
                    
                    $expect[] = ['id' => $value['id'], 'status' => 6];
                    $buy[] = ['id' => $value['buy_id'], 'status' => 6];
                    continue;
                }
            }
            $prize[] = $value;
        }
        if(!empty($retrunJoin)){
            $moneyClass = new MoneyHistory;
            foreach ($retrunJoin as $key => $value) {
                $html = "流产撤单：[$key]返还";
                foreach ($value['money'] as $key2 => $val) {
                    $moneyClass->write(['userid' => $key2, 'money' => $val * $settingCoin, 'remark'=> $html.'购买金额']);
                }
                if($value['bd']['money'] > 0){
                    $moneyClass->write(['userid' => $value['bd']['userid'], 'money' => $value['bd']['money']* $settingCoin, 'remark'=> $html.'保底金额']);
                }
            }
        }
        !empty($expect) && $this->saveAll($expect);
        !empty($buy) && ((LotteryCommon::getModel($this->ext_name, 'buy')))->saveAll($buy);
        return $prize;
     }
}