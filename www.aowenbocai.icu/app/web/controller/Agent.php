<?php
namespace app\web\controller;
use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use app\common\model\InvitationCode;
use app\common\model\MoneyHistory;
use app\common\model\Statis;
use app\news\model\PluginNewsList;

class Agent extends UserBase
{
    public function index()
    {
      return $this->fetch('');
    }
    //代理说明
    public function explain()
    {
      $navClass = new PluginNewsList;
      $info = $navClass->where('title', '代理说明')->find();
      return $this->fetch('',['info' => $info]);
    }
    //代理报表
    public function report()
    {
      return $this->fetch('');
    }
    //下级报表
    public function subReport($userid = '')
    {
        if ($userid) {
            $model = new \app\web\model\User();
            $check_agent = $model->checkAgents($userid, $this->user['id'], 1);
            if (!$check_agent['code']) return $this->error($check_agent['msg']);
            $user_info = $model->field('nickname')->find($userid);
            $this->assign('user_info', $user_info);
        }
        $this->assign('userid', $userid);
        return $this->fetch('');
    }

    //下级报表
    public function getStatisticsList()
    {
        $post  = request()->param();
        $model = new \app\web\model\User();
        $statis_model = new Statis();
        if (isset($post['userid']) and $post['userid']) {
            $check_agent = $model->checkAgents($post['userid'], $this->user['id'], 1);
            if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            $model->where('agents', $post['userid']);
        } else {
            $model->where('agents',$this->user['id']);
        }
        if (isset($post['name'])) {
            $model->where('username|nickname', 'like', '%' . $post['name'] . '%');
        }
        $res = $model->field('id, nickname, type, username')->paginate(14)->each(function ($item, $key) use($statis_model) {
            $item['statistics'] = $statis_model->getAgentsStatistics($item['id']);
            return $item;
        });
        $res = $res->toArray();
        $res['err'] = 0;
        return $res;
    }

    /** 下级开户 */
    public function invite()
    {
      $rebate = $this->user['rebate'];
      $this->assign('rebate', json_encode($rebate));
      return $this->fetch('');
    }
    /** 邀请码管理 */
    public function inviteCode()
    {
      $rebate = $this->user['rebate'];
      $this->assign('rebate', json_encode($rebate));
      return $this->fetch('');
    }

    /** 添加开户 */
    public function add_invite()
    {
        if(request()->isPost()){
            $data = request()->post();
            if ($this->user['type'] != 2) return ['err' => 1, 'msg' => '当前用户类型不能开户'];
            $res = (new InvitationCode())->add($data, $this->user);
            if (!$res['code']) {
                return ['err' => 1, 'msg' => $res['msg']];
            }
            return ['err' => 0, 'msg' => $res['msg']];
        }
    }

    /** 添加邀请码备注 */
    public function add_invite_remark()
    {
        if(request()->isPost()){
            $data = request()->post();
            $info = (new InvitationCode())->where('userid', $this->user['id'])->find($data['id']);
            if (!$info) return ['err' => 1, 'msg' => '参数错误'];
            $res = $info->save(['remark' => $data['remark']]);
            if (!$res) {
                return ['err' => 1, 'msg' => '修改失败'];
            }
            return ['err' => 0, 'msg' => '修改成功'];
        }
    }

    /** 添加邀请码备注 */
    public function delete_invite()
    {
        if(request()->isPost()){
            $data = request()->post();
            $info = (new InvitationCode())->where('userid', $this->user['id'])->find($data['id']);
            if (!$info) return ['err' => 1, 'msg' => '参数错误'];
            if (!$info->delete()) {
                return ['err' => 1, 'msg' => '删除失败'];
            }
            return ['err' => 0, 'msg' => '删除成功'];
        }
    }

    /** 代理为会员充值转账 */
    public function change_money()
    {
        if(request()->isPost()){
            $data = request()->post();
            $model = new \app\web\model\User();
            $check_agent = $model->checkAgents($data['userid'], $this->user['id']);
            if (!$check_agent['code']) return $this->error($check_agent['msg']);
            $money_model = new MoneyHistory();
            $res = $money_model->write([
                'userid' => $this->user['id'],
                'money' => -$data['money'],
                'type' => 2,
                'remark' => '为下级充值'
            ]);
            if (!$res['code']) {
                return ['err' => 1, 'msg' => $res['msg']];
            }
            $res2 = $money_model->write([
                'userid' => $data['userid'],
                'money' => $data['money'],
                'type' => 2,
                'remark' => '代理充值'
            ]);
            if (!$res2['code']) {
                return ['err' => 1, 'msg' => $res2['msg']];
            }
            return ['err' => 0, 'msg' => '充值成功'];
        }
    }

    /** 获取列表 */
    public function get_invite($type = '')
    {
        $res = (new InvitationCode())->where('userid', $this->user['id'])->where('type', $type)->paginate(14);
        $res = $res->toArray();
        $res['err'] = 0;
        return $res;
    }

    //赔率表
    public function rebateOdds()
    {
        $lottery_list = (new Common())->getNavLottery(true);
        $this->assign('lottery_list', $lottery_list);
        return $this->fetch('',['title' => '返点赔率表']);
    }

    /** 获取赔率 */
    public function get_rebate($name)
    {
        $ext_info = Ext::where('name', $name)->find();
        if (!$ext_info) return ['err' => 1, 'msg' => '彩种不存在'];
        $cp_type = LotteryCommon::getCpType($name);

        // 防护检查：确保rebate字段存在
        $rebate = 0;
        if (isset($this->user['rebate']) && is_array($this->user['rebate']) && isset($this->user['rebate'][$cp_type])) {
            $rebate = $this->user['rebate'][$cp_type];
        }

        $up_user_rebate = 0;//上级返点
        if (isset($this->user['top_agents']) && $this->user['top_agents']) {//判断存在上级时
            $user_top_agents = \app\web\model\User::get($this->user['top_agents']);
            $up_user_rebate_array = $user_top_agents ? $user_top_agents['rebate'] : [];
            if (is_array($up_user_rebate_array) && isset($up_user_rebate_array[$cp_type]) && $up_user_rebate_array[$cp_type]) {
                $up_user_rebate = $up_user_rebate_array[$cp_type] - $rebate;
            }
        }
        $type = LotteryCommon::getSetting($name)->getValue(LotteryCommon::getSettingValue($name, 'bouns'));
        $cp_config = LotteryCommon::getSetting($name)->getValue(LotteryCommon::getSettingValue($name, 'config'));
        $cp_config = json_decode($cp_config, true);
        $base_bonus = 100 - $cp_config['bonus_base'];
        if (!$type) return ['err' => 1, 'msg' => '彩种玩法未配置'];
        $type = json_decode($type, true);
        $gain_array = [];
        $type_array = [];
        if (!$rebate) return ['err' => 1, 'msg' => '没有返点'];
        $k = $rebate/0.1;
        for($i = 0; $i < $rebate; $i+=0.1) {
            $gain_array[$k] = [$i];
            foreach ($type as $key => $v) {
                if ($v['isOpen']) continue;
                if (isset($v['type']) and $v['type']  and in_array($cp_type, ['ssc', 'syxw'])) continue;
                if ($cp_type == 'pk10') {
                    $gain_show = LotteryCommon::getCommon($name)->getGainShow($key, $v);
                    foreach ($gain_show as $row) {
                        if ($cp_config['mode'] == 2) {
                            $gain = '赔率' .  round(($row['gain'] * (100 - ($base_bonus + $i + $up_user_rebate))/100)/2, 2);
                        } else {
                            $gain = '奖金' .  round($row['gain'] * (100 - ($base_bonus + $i + $up_user_rebate))/100, 2);
                        }
                        array_push($gain_array[$k], $gain);
                        if ($i == 0) {
                            array_push($type_array, $row['name']);
                        }
                    }
                    continue;
                }
                $gain = explode(',', $v['gain']);
                if (count($gain) > 1) {
                    $gain_show = LotteryCommon::getCommon($name)->getGainShow($gain, $v);
                    foreach ($gain_show as $row) {
                        if ($cp_config['mode'] == 2) {
                            $gain = '赔率' .  round(($row['gain'] * (100 - ($base_bonus + $i + $up_user_rebate))/100)/2, 2);
                        } else {
                            $gain = '奖金' .  round($row['gain'] * (100 - ($base_bonus + $i + $up_user_rebate))/100, 2);
                        }
                        array_push($gain_array[$k], $gain);
                        if ($i == 0) {
                            array_push($type_array, $row['name']);
                        }
                    }
                } else {
                    $gain = $gain[0];
                    if ($cp_config['mode'] == 2) {
                        $gain = '赔率' .  round(($gain * (100 - ($base_bonus + $i + $up_user_rebate))/100)/2, 2);
                    } else {
                        $gain = '奖金' .  round($gain * (100 - ($base_bonus + $i + $up_user_rebate))/100, 2);
                    }
                    array_push($gain_array[$k], $gain);
                    if ($i == 0) {
                        array_push($type_array, $v['name']);
                    }
                }
            }
            $k--;
        }
        return json(['type' => $type_array, 'gain' => $gain_array]);

    }

    /** 会员管理 */
    public function member($id = '')
    {
        if ($id) {
            $model = new \app\web\model\User();
            $check_agent = $model->checkAgents($id, $this->user['id'], 1);
            if (!$check_agent['code']) return $this->error($check_agent['msg']);
            $user_info = $model->field('nickname')->find($id);
            $this->assign('user_info', $user_info);
        }
        $this->assign('id', $id);
        return $this->fetch('');
    }

    /** 获取列表 */
    public function get_member()
    {
        $post  = request()->param();
        $model = new \app\web\model\User();
        if (isset($post['id']) and $post['id']) {
            $check_agent = $model->checkAgents($post['id'], $this->user['id'], 1);
            if (!$check_agent['code']) return ['err' => 1, 'msg' => $check_agent['msg']];
            $model->where('agents', $post['id']);
        } else {
            $model->where('agents',$this->user['id']);
        }
        if (isset($post['type'])) {
            $model->where('type', $post['type']);
        }
        if (isset($post['name'])) {
            $model->where('username|nickname', 'like', '%' . $post['name'] . '%');
        }
        $res = $model->field('id, nickname, type, money, create_time, action_time, rebate, username')->paginate(14)->each(function ($item, $key) {
            $item['agents_num'] = $item['agents_num'];
            $item['agents_money'] = $item['agents_money'];
            return $item;
        });
        $res = $res->toArray();
        $res['err'] = 0;
        return $res;
    }

    /** 代理报表 */
    public function get_statistics()
    {
        $model = new Statis();
        $res = $model->getAgentsStatistics($this->user['id']);
        return $res;
    }

    //投注明细
    public function betRecord($userid = '')
    {
        if(!$userid){
            $userid = $this->user['id'];
        }
        $model = new \app\web\model\User();
        $user_info = $model->field('nickname')->find($userid);
        $this->assign(['userid'=> $userid, 'userinfo' => $user_info]);
        return $this->fetch('');
    }

    //账户明细
    public function billRecord($userid = '')
    {
        if(!$userid){
            $userid = $this->user['id'];
        }
        $model = new \app\web\model\User();
        $user_info = $model->field('nickname')->find($userid);
        $this->assign(['userid'=> $userid, 'userinfo' => $user_info]);
        return $this->fetch('');
    }

    //充值明细
    public function rechargeRecord($userid = '')
    {
        if(!$userid){
            $userid = $this->user['id'];
        }
        $model = new \app\web\model\User();
        $user_info = $model->field('nickname')->find($userid);
        $this->assign(['userid'=> $userid, 'userinfo' => $user_info]);
        return $this->fetch('');
    }
    //兑换明细
    public function exchangeRecord($userid = '')
    {
        if(!$userid){
            $userid = $this->user['id'];
        }
        $model = new \app\web\model\User();
        $user_info = $model->field('nickname')->find($userid);
        $this->assign(['userid'=> $userid, 'userinfo' => $user_info]);
        return $this->fetch('');
    }
    //转账明细
    public function transformRecord($userid = '')
    {
        if(!$userid){
            $userid = $this->user['id'];
        }
        $model = new \app\web\model\User();
        $user_info = $model->field('nickname')->find($userid);
        $this->assign(['userid'=> $userid, 'userinfo' => $user_info]);
        return $this->fetch('');
    }
}