<?php

namespace app\fa\model;

use app\common\model\GameMoneyHistory;
use app\common\model\UserBag;
use think\Db;
use think\Model;
use think\Validate;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;

class Buy extends Model
{
    protected $name = 'plugin_fa_buy';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $code_type;

    protected function initialize()
    {
        parent::initialize();
        $this->code_type = ['1', '2', '6'];//投注方式   1大  2小  6發
    }

    /**
     * 获取器 - 开奖号码
     * @return string
     */
    public function getWinningCodeAttr($value, $data)
    {
        $res = (new Code())->where('expect', $data['expect'])->find();
        return empty($res) ? '' : $res['code'];

    }
    public function getPlayInfoAttr($value, $data)
    {
        $code = json_decode($data['code'],true);
        $_data['code'] = '';
        $txtArr = ['2' => '小', '1' => '大', '6' => '發'];
        foreach ($code as $key => $v) {
            $_data['code'] .= '  '.$txtArr[$key].':'.$v;
        }
       
        $status =  ['0' => '等待派奖', '1' => '已中奖','2' => '未中奖', '7' => '系统撤单'];
        $_data['status'] = $status[$data['status']];
        return $_data;
    }

    /**
     * 获取器 - code
     * @return string
     */
    public function getCodeTxtAttr($value, $data)
    {
        $code = json_decode($data['code'],true);
        $txt='';
        foreach ($code as $key => $v) {
            if ($key == 2) {
                $txt .= '  '.'小:'.$v;
            } elseif ($key == 1) {
                $txt .= '  '.'大:'.$v;
            } elseif ($key == 6) {
                $txt .= '  '.'發:'.$v;
            }
        }
        return $txt;

    }

    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['username'];
    }

    /**
     * 获取器 - status_text
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '进行中', 1 => '已发奖', 2 => '未中奖', 7 => '系统撤单'];
        if ($data['status'] == 7) {
            return $status[7];
        }
        $no = 1;
        $expect = Db::name('plugin_ssc_expect')->where('buy_id', $data['id'])->select();
        foreach ($expect as $v) {
            if ($v['status'] == 0) {
                $no = 0;
            }
        }
        return $status[$no];
    }


    /**
     * 添加投注号码
     */
    public function add($data)
    {
        $validate = new Validate([
            'code|投注号码' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        //判断期数
        $expect = $this->checkExpect();
        if (!$expect['allow_betting']) {
            return ["code" => 0, "msg" => '投注已截止，请等下一期!'];
        }
        $data['expect'] = $expect['expect'];
        //验证投注号码
        if (!count($data['code'])) {
            return ["code" => 0, "msg" => '投注内容为空'];
        }
        foreach ($data['code'] as $v) {
            if (!in_array($v['type'], $this->code_type) and !intval($v['money'])) {
                return ["code" => 0, "msg" => '投注参数错误'];
            }
        }
        //投注号码转换
        $code_array = array();
        foreach ($data['code'] as $key => $item) {
            $code_array[$item['type']][$key] = $item['money'];
        }
        foreach ($code_array as $key => $v) {
            $code_array[$key] = array_sum($code_array[$key]);
        }
        $data['code'] = json_encode($code_array);
        $data['money'] = array_sum($code_array);
        if ($data['is_coupon']) {
            $data['pay_type'] = 2;
        } else {
            $data['pay_type'] = 1;
        }
        unset($data['is_coupon']);
        if ($data['pay_type'] == 1) {//扣除余额
            $money_mx = [
                'userid' => $data['userid'],
                'money' => -$data['money'],
                'ext_name' => Request()->module()
            ];
            $res_mx = (new GameMoneyHistory())->write($money_mx);//添加资金明细
            if (!$res_mx['code']) {
                return ["code" => 0, "msg" => $res_mx['msg']];
            }
        } elseif ($data['pay_type'] == 2) {//扣除免费券
            $setting = \core\Setting::get(['free_coupon_coin']);
            $fa_coupon = (new UserBag())->where('userid', $data['userid'])->where('param_name', 'fa_coupon')->find();
            $coupon_num = $data['money'] / $setting['free_coupon_coin'];
            if ($coupon_num > 0 && $data['money'] % $setting['free_coupon_coin'] != 0) {
                return ["code" => 0, "msg" => '参数错误'];
            }
            if (empty($fa_coupon) || $fa_coupon['num'] < $coupon_num) {
                return ["code" => 0, "msg" => '免费券不足'];
            }
            (new UserBag())->where('userid', $data['userid'])->where('param_name', 'fa_coupon')->setDec('num', $coupon_num);
        }
        $res = $this->save($data);//记录投注号码
        if (!$res) {
            return ['code' => 0, 'msg' => '投注失败'];
        }
        return ['code' => 1, 'expect' => $data['expect']];
    }

    /**
     * 检验期数  35s投注时间  10秒开奖时间  -- 一天 1920期  45秒一期
     */
    public function checkExpect()
    {
        $now_timestamp = time();//当前日期和时间时间戳
        $now_day = strtotime(date('Y-m-d', time()));//当前日期时间戳
        $expect_date = date('Ymd');
        $today_timestamp = $now_timestamp - $now_day;//当前的时间戳
        $expect = sprintf("%04d", floor($today_timestamp / 45));
        $allow_betting = $today_timestamp % 45;
        if ($allow_betting <= 35) {
            $is_allow_betting = 1;//是否允许投注
            $back_time = 35 - $allow_betting;
        } else {
            $is_allow_betting = 0;//是否允许投注
            $back_time = 0;
        }
        return ['expect' => $expect_date . $expect, 'back_time' => $back_time, 'allow_betting' => $is_allow_betting];

    }

    /**
     * 计算当前期数的后$num 期
     * $num  返回的后期数
     */
    public function backExpect($num)
    {
        $res = [];
        $now_timestamp = time();//当前日期和时间时间戳
        $now_day = strtotime(date('Y-m-d', time()));//当前日期时间戳
        $expect_date = date('Ymd');
        $today_timestamp = $now_timestamp - $now_day;//当前的时间戳
        $num_expect = floor($today_timestamp / 45);
        $yesday_num = $num - $num_expect;
        $expect = sprintf("%04d", $num_expect);
        if ($yesday_num <= 0) {
            for ($i = 1; $i <= $num; $i++) {
                array_push($res, $expect_date.$expect - $i);
            }
        } else {
            //今天的期数
            for ($i = 1; $i <= $num_expect; $i++) {
                array_push($res, $expect_date.$expect - $i);
            }
            $expect = 1920;
            //昨天的期数
            for ($i = 0; $i < $yesday_num; $i++) {
                array_push($res, $expect_date.$expect - $i);
            }
        }
        return $res;

    }

    /**
     * 获取当期的投注记录
     * @return string
     */
    public function getExpectBetting($expect, $user_id)
    {
        $buy = $this->where('expect', $expect)->where('userid', $user_id)->select();
        $betting_code = ['1' => 0, '6' => 0, '2' => 0];
        if (!$buy->isEmpty()) {//计算用户的投注号码
            $da = $fa = $xiao = 0;
            foreach ($buy as $v) {
                $code = json_decode($v['code'],true);
                $da += $code[1];
                $fa += $code[6];
                $xiao += $code[2];
            }
            $betting_code = ['1' => $da, '6' => $fa, '2' => $xiao];
        }
        return $betting_code;

    }


}
