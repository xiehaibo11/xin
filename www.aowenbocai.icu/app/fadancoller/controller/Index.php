<?php
namespace app\fadancoller\controller;

use think\Controller;
use think\Session;
use app\web\model\User as Auser;
use app\fadancoller\model\Lottery_buy as Lottery_buy;
use app\fadancoller\model\Lottery_expect as Lottery_expect;
use app\fadancoller\model\Lottery_join as Lottery_join;
use core\Setting;

class Index extends Controller
{
    // 获取随机数字（0-9）
    public function getrandnum($n)
    {
        $a = [0,1,2,3,4,5,6,7,8,9];
        $b = array_rand($a,$n);
        sort($b);
        return $b;
    }
    
    // 获取11选5随机号码
    public function get115randnum($n)
    {
        $a = ['01','02','03','04','05','06','07','08','09','10','11'];
        $b = array_rand($a,$n);
        sort($b);
        $c = array();
        foreach($b as $key=>$vo) {
            $c[$key] = $a[$vo];
        }
        return $c;
    }
    
    // 测试接口
    public function cs()
    {
        $url = "https://bcapi.cn/token/3f9b629297db11edb63053e8fa42284b/code/hn5fc/rows/1.json";
        $content = json_decode(file_get_contents($url),true);
        print_r($content);
    }

    /**
     * ssc一星复式发单
     */
    public function ssc()
    {
        $url = "http://api.api861861.com/CQShiCai/getBaseCQShiCai.do?issue=&lotCode=10060";
        $content = json_decode(file_get_contents($url),true);
        $result = $content['result']['data'];
        
        if($result){
            $user = new Auser;
            $userlist = $user->gettypeusers(6);
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['status'] = 0;
            $data['is_join'] = 1;
            $expect = substr($result['expect'],0,8).str_pad(intval(substr($result['expect'],9,3)+1),3,'0',STR_PAD_LEFT);
            $data['userid'] = $userlist['id'];
            $rid = (new Lottery_buy)->getLastone($data['userid'])['id'];
            $expectone = (new Lottery_expect)->getone($rid);
            
            if($expectone['expect'] == $expect || $expect == '001') exit;
            
            $data['show'] = 2;
            $data['ext_name'] = 'ssc';
            $data['end_time'] = date('Y-m-d H:i:s',strtotime($result['drawTime'])-60);
            
            $arr['note_num'] = rand(4,6);  //杀四~杀六
            $arr['num'] = implode(',',$this->getrandnum($arr['note_num']));
            $arr['type'] = '6.1';
            
            $money = 2*$arr['note_num']; //每一份的钱
            $multiple = rand(1,10)*100; //倍数为100~1000
            $data['total_money'] = $multiple*$money;
            $data['total_share'] = intval($multiple/50);
            $data['is_stop'] = 0;
            $data['lottery_id'] = "SSC|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
            $data['play_num'] = '['.json_encode($arr).']';
            
            if($id = (new Lottery_buy)->add($data)) {
                $datas['expect'] = $expect;
                $datas['buy_id'] = $id;
                $datas['multiple'] = $multiple;
                $datas['ext_name'] = $data['ext_name'];
                (new Lottery_expect)->add($datas);
                
                // 修改为百分比计算跟单金额（1%-10%）
                $percentage = rand(1, 10) / 100;
                $minAmount = 1; // 最低跟单金额
                $calculatedAmount = ceil($data['total_money'] * $percentage);
                $data2['money'] = max($minAmount, $calculatedAmount);
                
                $data2['userid'] = $data['userid'];
                $data2['buy_id'] = $id;
                $data2['create_time'] = date('Y-m-d H:i:s');
                $data2['join_status'] = 1;
                $data2['ext_name'] = $data['ext_name'];
                (new Lottery_join)->add($data2);
            }
        }
    }

    /**
     * 云南时时彩发单
     */
    public function ynssc()
    {
        $url = "http://www.caih8.cfd/ynssc.php";
        $content = json_decode(file_get_contents($url),true);
        $result = $content['result']['data'];

        $user = new Auser;
        $userlist = $user->gettypeusers(6);
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;
        $data['is_join'] = 1;
        $expect = substr($result['preDrawIssue'],0,8).str_pad(intval(substr($result['preDrawIssue'],8,3)+1),3,'0',STR_PAD_LEFT);
        $data['userid'] = $userlist['id'];
        $rid = (new Lottery_buy)->getLastone($data['userid'])['id'];
        $expectone = (new Lottery_expect)->getone($rid);
        
        if($expectone['expect'] == $expect) exit;
        
        $data['show'] = 2;
        $data['ext_name'] = 'ynissc';
        $data['end_time'] = date('Y-m-d H:i:s',strtotime($result['preDrawTime'])+280);
        
        $arr['note_num'] = rand(4,6);  //杀四~杀六
        $arr['num'] = implode(',',$this->getrandnum($arr['note_num']));
        $arr['type'] = '6.1';
        
        $money = 2*$arr['note_num'];//每一份的钱
        $multiple = rand(1,10)*100; //倍数为100~1000
        $data['total_money'] = $multiple*$money;
        $data['total_share'] = intval($multiple/50);
        $data['is_stop'] = 0;
        $data['lottery_id'] = "YNISSC|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
        $data['play_num'] = '['.json_encode($arr).']';
        
        if($id = (new Lottery_buy)->add($data)) {
            $datas['expect'] = $expect;
            $datas['buy_id'] = $id;
            $datas['multiple'] = $multiple;
            $datas['ext_name'] = $data['ext_name'];
            (new Lottery_expect)->add($datas);
            
            // 修改为百分比计算跟单金额（1%-10%）
            $percentage = rand(1, 10) / 100;
            $minAmount = 1;
            $calculatedAmount = ceil($data['total_money'] * $percentage);
            $data2['money'] = max($minAmount, $calculatedAmount);
            
            $data2['userid'] = $data['userid'];
            $data2['buy_id'] = $id;
            $data2['create_time'] = date('Y-m-d H:i:s');
            $data2['join_status'] = 1;
            $data2['ext_name'] = $data['ext_name'];
            (new Lottery_join)->add($data2);
        }
    }

    /**
     * 海南时时彩发单
     */
    public function hnssc()
    {
        $url = "https://www.caih8.cfd/hnssc.php";
        $content = json_decode(file_get_contents($url),true);
        $result = $content['result']['data'];
        
        if($result["preDrawTime"] == NULL){
         header('Refresh: 2; URL=https://www.caih8.cfd/fadancoller/index/hnssc');
         exit;
        }

        $user = new Auser;
        $userlist = $user->gettypeusers(6);
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;
        $data['is_join'] = 1;
        $expect = substr($result['preDrawIssue'],0,8).str_pad(intval(substr($result['preDrawIssue'],8,3)+1),3,'0',STR_PAD_LEFT);
        $data['userid'] = $userlist['id'];
        $rid = (new Lottery_buy)->getLastone($data['userid'])['id'];
        $expectone = (new Lottery_expect)->getone($rid);
        
        if($expectone['expect'] == $expect) exit;
        
        $data['show'] = 2;
        $data['ext_name'] = 'hnssc';
        $data['end_time'] = date('Y-m-d H:i:s',strtotime($result['preDrawTime'])+280);
        
        $wanfatype = array(
            array('type'=>'4.6', 'money'=>70, 'notes'=>35, 'number'=>implode(',',$this->getrandnum(7))),
            array('type'=>'3.6', 'money'=>70, 'notes'=>35, 'number'=>implode(',',$this->getrandnum(7))),
            array('type'=>'2.6', 'money'=>70, 'notes'=>35, 'number'=>implode(',',$this->getrandnum(7))),
            array('type'=>'2.3', 'money'=>144, 'notes'=>72, 'number'=>implode(',',$this->getrandnum(9))),
            array('type'=>'3.3', 'money'=>144, 'notes'=>72, 'number'=>implode(',',$this->getrandnum(9))),
            array('type'=>'4.3', 'money'=>144, 'notes'=>72, 'number'=>implode(',',$this->getrandnum(9))),
            array('type'=>'4.1', 'money'=>432, 'notes'=>216, 'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6))),
            array('type'=>'3.1', 'money'=>432, 'notes'=>216, 'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6))),
            array('type'=>'2.1', 'money'=>432, 'notes'=>216, 'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)))
        );
        
        $wanfaarr = $wanfatype[array_rand($wanfatype,1)];
        $arr['note_num'] = $wanfaarr["notes"];
        $arr['num'] = $wanfaarr["number"];
        $arr['type'] = $wanfaarr["type"];
        $money = $wanfaarr["money"];
        $multiple = rand(1,20);
        $data['total_money'] = $multiple*$money;
        $data['total_share'] = $data['total_money'];
        $data['is_stop'] = 0;
        $data['lottery_id'] = "HNSSC|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
        $data['play_num'] = '['.json_encode($arr).']';
        
        if($id = (new Lottery_buy)->add($data)) {
            $datas['expect'] = $expect;
            $datas['buy_id'] = $id;
            $datas['multiple'] = $multiple;
            $datas['ext_name'] = $data['ext_name'];
            (new Lottery_expect)->add($datas);
            
            // 修改为百分比计算跟单金额（1%-10%）
            $percentage = rand(1, 10) / 100;
            $minAmount = 1;
            $calculatedAmount = ceil($data['total_money'] * $percentage);
            $data2['money'] = max($minAmount, $calculatedAmount);
            
            $data2['userid'] = $data['userid'];
            $data2['buy_id'] = $id;
            $data2['create_time'] = date('Y-m-d H:i:s');
            $data2['join_status'] = 1;
            $data2['ext_name'] = $data['ext_name'];
            (new Lottery_join)->add($data2);
        }
    }

    /**
     * 腾讯时时彩发单
     */
    public function txssc()
    {
        $url = "https://www.caih8.cfd/hnssc.php";
        $content = json_decode(file_get_contents($url),true);
        
        if(!$content || !isset($content['result']['data'])) {
            return;
        }
        
        $result = $content['result']['data'];
        $lastuserid = [];
        $user = new Auser;
        $userlist = $user->gettypeusers(6);
        $lastuserarr = (new Lottery_expect)->getbuyerid($result['preDrawIssue'],'txssc');
        
        if($lastuserarr) {
            foreach($lastuserarr as $k) {
                $lastuserid[] = (new Lottery_buy)->getuserid($k);
            }
        }
        
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;
        $data['is_join'] = 1;
        $expect = substr($result['preDrawIssue'],0,8).str_pad(intval(substr($result['preDrawIssue'],8,3)+1),3,'0',STR_PAD_LEFT);
        
        if(substr($expect,-3) === '001') {
            return;
        }
        
        $data['userid'] = $userlist['id'];
        if(in_array($data['userid'],$lastuserid)) {
            return;
        }
        
        $data['show'] = 2;
        $data['ext_name'] = 'txssc';
        $data['end_time'] = date('Y-m-d H:i:s',strtotime($result['preDrawTime'])+280);
        
        $wanfatype = [
            ['type'=>'4.6','money'=>70,'notes'=>35,'number'=>implode(',',$this->getrandnum(7))],
            ['type'=>'3.6','money'=>70,'notes'=>35,'number'=>implode(',',$this->getrandnum(7))],
            ['type'=>'2.6','money'=>70,'notes'=>35,'number'=>implode(',',$this->getrandnum(7))],
            ['type'=>'2.3','money'=>144,'notes'=>72,'number'=>implode(',',$this->getrandnum(9))],
            ['type'=>'3.3','money'=>144,'notes'=>72,'number'=>implode(',',$this->getrandnum(9))],
            ['type'=>'4.3','money'=>144,'notes'=>72,'number'=>implode(',',$this->getrandnum(9))],
            ['type'=>'4.1','money'=>432,'notes'=>216,'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6))],
            ['type'=>'3.1','money'=>432,'notes'=>216,'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6))],
            ['type'=>'2.1','money'=>432,'notes'=>216,'number'=>implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6)).'|'.implode(',',$this->getrandnum(6))]
        ];
        
        $wanfaarr = $wanfatype[array_rand($wanfatype,1)];
        $arr['note_num'] = $wanfaarr["notes"];
        $arr['num'] = $wanfaarr["number"];
        $arr['type'] = $wanfaarr["type"];
        $money = $wanfaarr["money"];
        $multiple = rand(1,20);
        $data['total_money'] = $multiple*$money;
        $data['total_share'] = $data['total_money'];
        $data['is_stop'] = 0;
        $data['lottery_id'] = "txssc|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
        $data['play_num'] = '['.json_encode($arr).']';
        
        if($id = (new Lottery_buy)->add($data)) {
            $datas['expect'] = $expect;
            $datas['buy_id'] = $id;
            $datas['multiple'] = $multiple;
            $datas['ext_name'] = $data['ext_name'];
            (new Lottery_expect)->add($datas);
            
            // 修改为百分比计算跟单金额（1%-10%）
            $percentage = rand(1, 10) / 100;
            $minAmount = 1;
            $calculatedAmount = ceil($data['total_money'] * $percentage);
            $data2['money'] = max($minAmount, $calculatedAmount);
            
            $data2['userid'] = $data['userid'];
            $data2['buy_id'] = $id;
            $data2['create_time'] = date('Y-m-d H:i:s');
            $data2['join_status'] = 1;
            $data2['ext_name'] = $data['ext_name'];
            (new Lottery_join)->add($data2);
        }
    }

    /**
     * 印尼时时彩发单
     */
    public function ynissc()
    {
    $url = "https://www.caih8.cfd/hnssc.php";
    $content = json_decode(file_get_contents($url), true);
    
    if (!$content || !isset($content['result']['data'])) {
        // 如果无法获取有效数据，可以根据实际情况处理，这里简单返回
        return;
    }
    
    $result = $content['result']['data'];
    $lastuserid = [];  // 上一期的发单用户
    
    $user = new Auser;
    $userlist = $user->gettypeusers(6);
    
    // 获取上一期的购买用户ID列表
    $lastuserarr = (new Lottery_expect)->getbuyerid($result['preDrawIssue'], 'ynissc');
    
    if ($lastuserarr) {
        foreach ($lastuserarr as $k) {
            $lastuserid[] = (new Lottery_buy)->getuserid($k);
        }
    }
    
    $data['create_time'] = date('Y-m-d H:i:s');
    $data['status'] = 0;
    $data['is_join'] = 1;
    
    // 生成下一期的期号
    $expect = substr($result['preDrawIssue'], 0, 8) . str_pad(intval(substr($result['preDrawIssue'], 8, 3)) + 1, 3, '0', STR_PAD_LEFT);
    
    // 添加判断条件，若期号为001则不发布
    if (substr($expect, -3) === '001') {
        return;
    }
    
    $data['userid'] = $userlist['id'];
    
    // 如果当前用户已经在上一期的购买用户列表中，则退出
    if (in_array($data['userid'], $lastuserid)) {
        return;
    }
    
    $data['show'] = 2;
    $data['ext_name'] = 'ynissc';
    $data['end_time'] = date('Y-m-d H:i:s', strtotime($result['preDrawTime']) + 280);
    
    $wanfatype = [
        ['type' => '4.6', 'money' => 70, 'notes' => 35, 'number' => implode(',', $this->getrandnum(7))],
        ['type' => '3.6', 'money' => 70, 'notes' => 35, 'number' => implode(',', $this->getrandnum(7))],
        ['type' => '2.6', 'money' => 70, 'notes' => 35, 'number' => implode(',', $this->getrandnum(7))],
        ['type' => '2.3', 'money' => 144, 'notes' => 72, 'number' => implode(',', $this->getrandnum(9))],
        ['type' => '3.3', 'money' => 144, 'notes' => 72, 'number' => implode(',', $this->getrandnum(9))],
        ['type' => '4.3', 'money' => 144, 'notes' => 72, 'number' => implode(',', $this->getrandnum(9))],
        ['type' => '4.1', 'money' => 432, 'notes' => 216, 'number' => implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6))],
        ['type' => '3.1', 'money' => 432, 'notes' => 216, 'number' => implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6))],
        ['type' => '2.1', 'money' => 432, 'notes' => 216, 'number' => implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6)) . '|' . implode(',', $this->getrandnum(6))],
    ];
    
    // 随机选择一种玩法
    $wanfaarr = $wanfatype[array_rand($wanfatype, 1)];
    
    // 调试输出
    echo "Selected money: " . $wanfaarr["money"] . "\n";
    
    $arr['note_num'] = $wanfaarr["notes"];
    $arr['num'] = $wanfaarr["number"];
    $arr['type'] = $wanfaarr["type"];
    
    $money = $wanfaarr["money"];
    $multiple = rand(1, 20);
    
    // 调试输出
    echo "Random multiple: " . $multiple . "\n";
    
    $data['total_money'] = $multiple * $money;
    $data['total_share'] = $data['total_money'];
    
    // 调试输出
    echo "Total money to be added: " . $data['total_money'] . "\n";
    
    $data['is_stop'] = 0;
    $data['lottery_id'] = "ynissc|" . $data['userid'] . str_replace('.', '', sprintf("%.4f", microtime(true)));
    $data['play_num'] = '[' . json_encode($arr) . ']';
    
    // 添加订单信息
    if ($id = (new Lottery_buy)->add($data)) {
        $datas['expect'] = $expect;
        $datas['buy_id'] = $id;
        $datas['multiple'] = $multiple;
        $datas['ext_name'] = $data['ext_name'];
        
        (new Lottery_expect)->add($datas);
        
            // 修改为百分比计算跟单金额（1%-10%）
            $percentage = rand(1, 10) / 100;
            $minAmount = 1;
            $calculatedAmount = ceil($data['total_money'] * $percentage);
            $data2['money'] = max($minAmount, $calculatedAmount);
            
            $data2['userid'] = $data['userid'];
            $data2['buy_id'] = $id;
            $data2['create_time'] = date('Y-m-d H:i:s');
            $data2['join_status'] = 1;
            $data2['ext_name'] = $data['ext_name'];
            (new Lottery_join)->add($data2);
        }
    }

    /**
     * 广东11选5发单
     */
    public function gd1155()
    {
        $url = "http://api.api861861.com/ElevenFive/getElevenFiveInfo.do?lotCode=10006";
        $content = json_decode(file_get_contents($url),true);
        $result = $content['result']['data'];

        $user = new Auser;
        $userlist = $user->gettypeusers(6);
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;
        $data['is_join'] = 1;
        $expect = $result['drawIssue'];
        $data['userid'] = $userlist['id'];
        $rid = (new Lottery_buy)->getLastone($data['userid'])['id'];
        $expectone = (new Lottery_expect)->getone($rid);
        
        if($expectone['expect'] == $expect) exit;
        
        $data['show'] = 3;
        $data['ext_name'] = 'gd11';
        $data['end_time'] = date('Y-m-d H:i:s',strtotime($result['drawTime'])-60);
        
        $arr['note_num'] = rand(4,6);  //杀四~杀六
        $arr['num'] = implode(',',$this->get115randnum($arr['note_num']));
        $arr['type'] = '8.1';
        
        $money = 2*$arr['note_num'];//每一份的钱
        $multiple = rand(1,10)*100; //倍数为100~1000
        $data['total_money'] = $multiple*$money;
        $data['total_share'] = intval($multiple/50);
        $data['is_stop'] = 0;
        $data['lottery_id'] = "GD11|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
        $data['play_num'] = '['.json_encode($arr).']';
        
        if($id = (new Lottery_buy)->add($data)) {
            $datas['expect'] = $expect;
            $datas['buy_id'] = $id;
            $datas['multiple'] = $multiple;
            $datas['ext_name'] = $data['ext_name'];
            (new Lottery_expect)->add($datas);
            
            // 修改为百分比计算跟单金额（1%-10%）
            $percentage = rand(1, 10) / 100;
            $minAmount = 1;
            $calculatedAmount = ceil($data['total_money'] * $percentage);
            $data2['money'] = max($minAmount, $calculatedAmount);
            
            $data2['userid'] = $data['userid'];
            $data2['buy_id'] = $id;
            $data2['create_time'] = date('Y-m-d H:i:s');
            $data2['join_status'] = 1;
            $data2['ext_name'] = $data['ext_name'];
            (new Lottery_join)->add($data2);
        }
    }

    /**
     * 自动跟单
     */
    public function joinbuy()
    {
        $where = ['status' => 0];
        $limit = 10;

        $lotteryBuy = new Lottery_buy();
        $fadanlist = $lotteryBuy->getlist($where, $limit);

        if ($fadanlist) {
            $lotteryJoin = new Lottery_join();
            $auser = new Auser();

            foreach ($fadanlist as $order) {
                $totalMoneyJoined = $lotteryJoin->getcountmoney($order['id']);
                
                if ($totalMoneyJoined < $order['total_share']) {
                    $userList = $auser->gettypeusers(7);
                    if ($userList) {
                        $userId = $userList['id'];
                        $buyId = $order['id'];
                        
                        // 修改为动态百分比计算（1%-10%）
                        $percentage = rand(1, 10) / 100;
                        $joinAmount = ceil($order['total_share'] * $percentage);
                        
                        // 确保不超过剩余金额
                        $remainingAmount = $order['total_share'] - $totalMoneyJoined;
                        if ($joinAmount > $remainingAmount) {
                            $joinAmount = $remainingAmount;
                        }
                        
                        // 确保最小金额为1
                        $joinAmount = max(1, $joinAmount);

                        $joinData = [
                            'userid' => $userId,
                            'ext_name' => $order['ext_name'],
                            'buy_id' => $buyId,
                            'join_status' => 2,
                            'create_time' => date('Y-m-d H:i:s'),
                            'money' => $joinAmount
                        ];

                        $userJoinWhere = ['userid' => $userId, 'buy_id' => $buyId];
                        $existingJoin = $lotteryJoin->getlist($userJoinWhere, 1);

                        if (!$existingJoin) {
                            $lotteryJoin->add($joinData);
                        } else {
                            $currentJoinAmount = $existingJoin[0]['money'];
                            $newJoinAmount = $currentJoinAmount + $joinAmount;
                            
                            if ($newJoinAmount > $order['total_share']) {
                                $newJoinAmount = $order['total_share'];
                            }
                            
                            $lotteryJoin->addmoney($existingJoin[0]['id'], $newJoinAmount - $currentJoinAmount);
                        }
                    }
                }
            }
        }
    }
}