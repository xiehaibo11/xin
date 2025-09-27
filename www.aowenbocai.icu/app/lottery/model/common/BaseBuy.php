<?php
namespace app\lottery\model\common;

use app\admin\model\Ext;
use app\common\controller\LotteryCommon;
use think\Model;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;
use app\index\model\User;
use app\lottery\model\LotteryCom;
use core\Setting;

class BaseBuy extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $ext_name = '';
    protected $name = 'lottery_buy';//表名
    protected $insert = ['ext_name'];

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

    public function getBettingAttr($value, $data)
    {
        $new = [];
        $play = $this->getPlayNumAttr($data['play_num'], $data);
        if (!is_array($play)) return [];
        foreach ($play as $key => $value) {
            $name = isset($value['name']) ? $value['name'] : $value['type'];
            $new[$key] = '<i>['.$name.']</i><font>'.$value['num'].'</font>';
            $new[$key] .= (new LotteryCom())->planContent($value);
        }
        return $new;
    }

    /**
     * 获取器 - 投注号码
     */
    public function getPlayNumAttr($value, $data)
    {
        $type_array = json_decode((LotteryCommon::getSetting($data['ext_name']))->getValue(LotteryCommon::getSettingValue($data['ext_name'], 'bouns')), true);
        if (!$type_array) {
            return $data['ext_name'] . '配置不存在';
        }
        $sign = array_column($type_array,'sign');
        $cp_type = LotteryCommon::getCpType($data['ext_name']);
        $pk10_array = ['WZ' => '猜位置', 'GJ' => '冠军', 'YJ' => '冠亚','YJ.2' => '冠亚-精准', 'JJ' => '前三', 'JJ.2' => '前三-精准', 'QS' =>'前四', 'QS.2' =>'前四-精准', 'QW' =>'前五', 'QL' =>'前六', 'QQ' =>'前七', 'QB' =>'前八', 'QJ' =>'前九', 'QSHI' =>'前十', 'DW' =>'定位', 'DXDS'
        => '大小单双', 'DXDS.2' => '大小单双', 'LH' => '龙虎'];
        $play_num = json_decode($value, true);
        foreach ($play_num as &$v){
            $signExist = array_search($v['type'], $sign);
            if ($cp_type == 'pk10') {
                $v['name']  = isset($pk10_array[$v['type']]) ? $pk10_array[$v['type']] : '该玩法不存在';
            } else {
                $v['name']  = is_numeric($signExist) ? $type_array[$signExist]['name'] : '该玩法不存在';
            }
        }
        return $play_num;
    }

    /**
     * 获取器 - 剩余金额
     * @return json
     */
    public function getSurplusMoneyAttr($value,$data)
    {
        $join_money = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('buy_id',$data['id'])->sum('money');
        $money = intval($data['total_money']) - intval($join_money);
        return $money;
    }

   /**
     * 获取器 - 进度
     * @return int
     */
    public function getLoadNumAttr($value,$data)
    {
        $join_money = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('buy_id',$data['id'])->sum('money');
        $load_num = intval($join_money) / intval($data['total_money']) * 100;
        $load_num = floor($load_num * 10) / 10;
        return $load_num;
    }

    public function getBuyStatusAttr($value, $data)
    {
        $status = ['0' => '进行中', '1' => '已中奖', '2' => '未中奖', '6'=>'流产撤单', '7' => '系统撤单', '8' => '用户撤单'];
        return $status[$data['status']];
    }

    /**
     * 获取器 - 是否截止
     * @return int
     */
    public function getStatusTxtAttr($value, $data)
    {
        $expect_model = (LotteryCommon::getModel($data['ext_name'], 'expect'));
        $status = $expect_model->get_status_data();
        $expect = $expect_model->field('status, expect, bonus')->where('buy_id', $data['id'])->order('id ASC')->select()->toArray();
        $num = count($expect);
        if (!$num) {
            $status_txt = '订单已损坏';
        } else {
            foreach ($expect as $key => $v) {
                if ($key == 0 and $v['status'] != 2) {
                    $status_txt = $status[$v['status']];
                    break;
                }
                if ($v['status'] == 2 and $v['bonus'] > 0) {
                    $status_txt = '<font style="color:#DE3031;">已中奖</font>';
                    break;
                }
                if ($v['status'] == 2 and $v['bonus'] == 0) {
                    $status_txt = '未中奖';
                }
            }
        }
        return $status_txt;
    }

    /**
     * 获取器 - 是否截止
     * @return int
     */
    public function getIsEndAttr($value, $data)
    {
        if (strtotime($data['end_time']) > time()) {
            return 0;//未截止
        }
        if ($data['is_join']) {
            $load_num = $this->getLoadNumAttr($value, $data);
            if ($load_num >= 100) {
                return 2;//合买满员
            }
        }
        return 1;//已截止
    }

    /**
     * 获取器 - 是否参与购买
     * @return int
     */
    public function getIsBuyAttr($value, $data)
    {
        $admin =  User::get(['sid' => session('sid')]);
        $user = $admin->toArray();
        $res = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('userid', $user['id'])->where('buy_id', $data['id'])->find();
        if (empty($res)) {
            return false;
        }
        return true;
    }

     /**
     * 获取器 -  发起人自购
     * @return json
     */
    public function getUserBuyAttr($value,$data)
    {
        $money = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('userid',$data['userid'])->where('buy_id',$data['id'])->find();
        return $money['money'];
    }

    /**
     * 获取器 -  发起人自购
     * @return json
     */
    public function getTotalShareAttr($value,$data)
    {
        if (!$value) {
            return $data['total_money'];
        }
        return $value;
    }

    /**
     * 获取器 - show_text
     * @return json
     */
    public function getShowTextAttr($value,$data)
    {
        $show_array = ['0' => '完全公开', '1' => '截止后公开', '2' => '完全保密', '3' => '仅跟单人可看'];
        return $show_array[$data['show']];
    }

    /**
     * 获取器 - 彩种名称
     */
    public function getExtNameTxtAttr($value, $data)
    {
        $ext_info = Ext::where('name', $data['ext_name'])->find();
        if (!$ext_info) return '不存在';
        return $ext_info['title'];
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value,$data)
    {
        $user_info = (new User)->field('nickname')->find($data['userid']);
        return $user_info['nickname'];
    }
    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value,$data)
    {
        $user_info = (new User)->field('nickname')->find($data['userid']);
        return $user_info ? $user_info['nickname'] : '该会员不存在';
    }

    /**获取合买里面数据 */
    public function getBuyInfoAttr($value, $data)
    {
        $expect_model = (LotteryCommon::getModel($data['ext_name'], 'expect'));
        $status = $expect_model->get_status_data();
        $expect = $expect_model->field('status, expect, bonus')->where('buy_id', $data['id'])->order('id ASC')->select()->toArray();
        $num = count($expect);
        $isChase = $num > 1 ? 1 : 0;
        $join = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('buy_id', $data['id'])->where('userid', $data['userid'])->column('money');
        if (!$num) {
            $status_txt = '订单已损坏';
        } else {
            foreach ($expect as $key => $v) {
                if ($key == 0 and $v['status'] != 2) {
                    $status_txt = $status[$v['status']];
                    break;
                }
                if ($v['status'] == 2 and $v['bonus'] > 0) {
                    $status_txt = '<font style="color:red;">已中奖</font>';
                    break;
                }
                if ($v['status'] == 2 and $v['bonus'] == 0) {
                    $status_txt = '未中奖';
                }
            }
        }
        $data = [
            'buy' => empty($join) ? 0 : $join[0],
            'isChase' => $isChase,
            'expect' => !$num ? '订单已损坏' : ($isChase ? $expect[0]['expect']."(追".$num."期)" : $expect[0]['expect']),
            'status' => $status_txt,
            'bonus' => !$num ? '订单已损坏' : $expect[0]['bonus'],
            'statusCode' => !$num ? '订单已损坏' : $expect[0]['status'],
            'finsh' => strtotime($data['end_time']) < time() ? 1 : 0
        ];
        return $data;
    }
    
    /**
     * 获取器 - 获取JOin表中关联数据
     */
    public function getJoinDataAttr($value, $data)
    {
        return (LotteryCommon::getModel($data['ext_name'], 'join'))->where('buy_id', $data['id'])->select()->toArray();
    }

    /**获取器--获取合买数据 */
    public function getJoinAttr($value, $data)
    {
        $list = (LotteryCommon::getModel($data['ext_name'], 'join'))->where('buy_id', $data['id'])->column(['userid', 'money']);
        return $list;
    }
    /**
     * 获取器 - Progress
     */
    public function getProgressAttr($value, $data)
    {
        $join = (LotteryCommon::getModel($data['ext_name'], 'join'));
        $count = $join->where('buy_id', $data['id'])->sum('money');
        $data['total_share'] = $this->getTotalShareAttr($data['total_share'], $data);
        return [
            'progress' => intval($count / $data['total_share'] * 10000) / 100,
            'assure_progress' => intval($data['assure_money'] / $data['total_share'] * 10000) / 100,
            'lost_num' => $data['total_share'] - $count,
            'buy_num' => $count
        ];
    }


    /**获取期号信息 */
    public function getExpectAttr($value, $data)
    {
        $list = (LotteryCommon::getModel($data['ext_name'], 'expect'))->field('expect,multiple,bonus,status,id,ext_name')->where('buy_id', $data['id'])->order('expect ASC')->select()->append(['newStatus','code']);
        $list = $list->toArray();
        return $list;
    }

    /**
     *  合买购买
     * @param  array $data 表单提交的值
     * @return array
     * 合买时，若该用户已购买过该条数据，则对数据进行更新不新加投注数据
     */
    public function joinAdd($data, $user)
    {
        $infoRes = $this->field('total_money,end_time,lottery_id,ext_name,total_share')->where('id',$data['buy_id'])->find();
        if(!$infoRes){
            return json(['err' => 2, 'msg' => '合买订单未找到']);
        }
        $infoRes = $infoRes->toArray();
        if(strtotime($infoRes['end_time']) < time()){
            return json(['err' => 3, 'msg' => '合买已结束']);
        }
        $join_class = (LotteryCommon::getModel($this->ext_name, 'join'));
        $joinList = $join_class->field('id,money,userid,ext_name')->where('buy_id', $data['buy_id'])->select()->toArray();
        $setting = (new Setting)->get(['isAuto', 'openBd', 'Bdpercent']);
        $settingCoin = 1;
        $res = (new LotteryCom)->joinCom($data, $infoRes, $joinList, $settingCoin);
        if(!isset($res['data'])){
            return json($res);
        }
        $expect_num = (LotteryCommon::getModel($this->ext_name, 'expect'))->where('buy_id', $data['buy_id'])->count('id');
        /**判断是否达到出票状态 */
        $isAuto = !empty($setting) && isset($setting['isAuto']) ? $setting['isAuto'] : 0;
        if($isAuto){
            $percent = floatval($setting['Bdpercent']) > 0 && $setting['openBd'] == 1 ? floatval($setting['Bdpercent']) : 100;
            $isAuto =  intval($res['buyMoney'] / $infoRes['total_share'] * 1000) /10 < $percent ? 0 : 1;
            if($isAuto){
                (LotteryCommon::getModel($this->ext_name, 'expect'))->save(['status' => 1], ['buy_id' => $data['buy_id']]);
            }
        }
        $moneys = $res['buyMoney'];
        if($res['key']){
            $key = $res['key'] - 1;
            $res =  $join_class->where('id', $joinList[$key]['id'])->where('userid', $joinList[$key]['userid'])->setInc('money', $data['money']);
        }else{
            $res['data']['join_status'] = 2;//参与者
            $res['data']['is_chase'] = $expect_num > 1 ? 1 : 0;//是否为追号
            $res =  $join_class->save($res['data']);
        }
        
        if(!$res){
            return json(["err"=>1,"msg"=>'参与失败<代码：01>']);
        }
        // 添加会员活动
        (new UserAction)->write([
            'userid' => $data['userid'],
            'content' => '参与合买【'.$data['buy_id'].'】',
            'ext_name' => Request()->module()
        ]);
        /**计算进度 */
        $jd_num = floor($moneys/ $infoRes['total_share'] * 1000) / 10;
        return json(["err"=>0,"msg"=>'购买成功','data' => ['jd_num'=>$jd_num]]);

    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * 之前使用事务回滚操作--mysql仅仅在表类型在INNODB的时候才支持回滚
     * 投注扣除保底金额，派奖在根据需要进行返还
     * @return array
     */
    public function add($post, $user)
    {

        $playType = json_decode((LotteryCommon::getSetting($this->ext_name))->getValue(LotteryCommon::getSettingValue($this->ext_name, 'bouns')),true);
        $data = (new LotteryCom())->buyCom($post, $user, $playType, $this->ext_name);
        if($data['err']){
            return $data;
        }
        $data = $data['data'];
        $total_money = $data['buy']['total_money'];
        $nowExpect = $data['nowIssue'];
        // 添加投注记录
        $data['buy']['ext_name'] = $this->ext_name;
        $res = $this->save($data['buy']);
        if(!$res){
            return ["err"=>1,"msg"=>'投注失败<代码：01>'];
        }
        $is_join = $data['buy']['is_join'];
        $data = $data['data'];
        $expect_array = (new LotteryCom)->expectData($data, $total_money,$this->id, $nowExpect, $this->ext_name);

        $expectModel = LotteryCommon::getModel($this->ext_name, 'expect');

        // 使用 insertAll 方法而不是 saveAll，避免更新条件缺失问题
        try {
            $res = $expectModel->insertAll($expect_array);//期号添加
            if(!$res){
                return ["err"=>2,"msg"=>'投注失败<代码：02> - 期号数据插入失败'];
            }
        } catch (\Exception $e) {
            \think\Log::error('期号插入异常: ' . $e->getMessage());
            return ["err"=>2,"msg"=>'投注失败<代码：02> - ' . $e->getMessage()];
        }
        //添加参与会员信息
        $res = (LotteryCommon::getModel($this->ext_name, 'join'))->save([
            'userid' => $data['userid'],
            'money' => $data['money'],
            'buy_id' => $this->id,
            'is_chase' => count($expect_array) > 1 ? 1 : 0,
            'join_status' => $is_join,
            'ext_name' => $this->ext_name  // 添加ext_name字段，修复投注失败<代码：02>问题
        ]);
        if(!$res){
            return ["err"=>3, "msg"=>'投注失败<代码：02>'];
        }

        if ($data['is_join']) {
            (new UserAction)->write([
                'userid' => $data['userid'],
                'content' => '我在第 [<b>' . $expect_array[0]['expect'] . '</b>] 期发起了一个的合买，快快来<a href="'.url($this->ext_name . '/Index/joinArticle').'?id=' . $this->id.'">参与</a>吧 O(∩_∩)O~~',
                'ext_name' => Request()->module()
            ]);//添加会员活动
        }
        return ["err"=>0,"msg"=>'投注成功'];
    }
   
    /**用户前端停止追号 */
    public function stopLottery($data, $expectId)
    {
        $expectModel = (LotteryCommon::getModel($this->ext_name, 'expect'));
        $expect = $expectModel->where('buy_id', $data['id'])->column(['id', 'expect', 'multiple','status']);
        if($expect[$expectId]['status']){
            return ['err' => 5, 'msg' => '该期该状态不能停止追号'];
        }
        $endTime = $expectModel->getExpectTime($expect[$expectId]['expect'])['data'];
        if(strtotime($endTime) <= time()){
            return ['err' => 5, 'msg' => '该期已结束不能停止追号'];
        }
        $multiple = array_column($expect, 'multiple');
        $perMoney = $data['total_money'] / array_sum($multiple);
        $setting = (new Setting)->get(['coin_lottery']);
        $settingCoin = $setting['coin_lottery'] ? $setting['coin_lottery'] : 1;
        $returnMoney = $expect[$expectId]['multiple'] * $perMoney * $settingCoin;

        /**合买追号停止*/
        if($data['is_join']){
            $moneyHis = (new LotteryCom())->joinChaseStop($this->ext_name, $data, $returnMoney,$expect[$expectId]['expect']);
            if (empty($moneyHis)) {
                return ['err' => 5, 'msg' => '合买未截止不能停止追号'];
            }
        } else {
            $moneyHis[] = ['userid' => $data['userid'], 'money' => $returnMoney, 'remark' => '停止追号['.$data['lottery_id'].']中第'.$expect[$expectId]['expect'].'期'];
        }
        $moneyClass = new MoneyHistory;
        foreach ($moneyHis as $value) {
            $moneyClass->write($value);
        }
        $expectModel->where('id',$expectId )->setField('status',5);
        return ['err' => 0, 'msg' => '该期停止追号成功'];
    }

    public function joinChaseStop($data, $returnMoney, $expectNum)
    {
        $list = (LotteryCommon::getModel($this->ext_name, 'join'))->where('buy_id', $data['id'])->where('userid', "<>", 1)->column(['userid', 'money']);
        $buyAll = array_sum($list);
        $moneyHis = [];
        foreach ($list as $key => $value) {
            $selfMoney = intval($value / $buyAll * 100 * $returnMoney) / 100;
            $moneyHis[] = ['userid' => $key, 'money' => $selfMoney, 'remark' => '停止追号['.$data['lottery_id'].']中第'.$expectNum.'期'];
        }
        return $moneyHis;
    }
}