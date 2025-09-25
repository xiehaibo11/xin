<?php
namespace app\lucky\model;

use think\Model;
use think\Validate;
use app\admin\model\User;
use app\common\model\GameMoneyHistory;
use app\lucky\model\PluginLuckyAward as Award;

class PluginLuckyHis extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['nickname' => $user->nickname,'username' => $user->username];
        }
        return ['nickname' => '用户不存在', 'username' => '...'];
    }

    /**获取器--获取下注信息 */
    public function getCodeRsAttr($value, $data)
    {
        $info = (new PluginLucky)->where('sign', $data['code'])->column('name');
        if(empty($info)){
            return '该标识不在';
        }
        return $info[0];
    }

    /**获取器--获取开奖信息 */
    public function getPlanListAttr($value, $data)
    {
       return $this->getPlanList($data);
    }

    public function getPlanList($data)
    {
        if(!$data['plan']){
            return '';
        }
        $_plan = json_decode($data['plan'], true);
        $plan = $_plan['common'];
        $ids = array_unique($plan);
        $info = (new PluginLucky)->field('sign, name')->where('sign', 'in', $ids)->select();
        if(!$info){
            return '其他,其他,其他';
        }
        $info = $info->toArray();
        $id_name = ['0'=> '其他'];
        foreach ($info as $key => $value) {
            $id_name[$value['sign']] = $value['name'];
        }
        $new = array_count_values($plan);
        $name = '';
        foreach ($new as $key => $value) {
            $name .= $id_name[$key].',';
            if($value > 1){
                $num = $value - 1; 
                for($i = 0; $i < $num; $i++){
                    $name .= $id_name[$key].',';
                }
            }
        }
        $info = (new PluginLucky)->where('sign', $_plan['lucky'])->column('name');
        if(empty($info)){
            $lucky =  '该标识不在';
        }
        $lucky = $info[0];
        return rtrim($name, ',')." |　".$lucky;
    }
    public function getPlayInfoAttr($value, $data)
    {
        $_data['open'] = $this->getPlanList($data);
        $info = (new PluginLucky)->where('sign', $data['code'])->column('name');
        $_data['code'] = empty($info) ? '该标识不在' : $info[0];
        return $_data;
    }

    public function hisList($userid)
    {
        $this->where('userid', $userid);
        $res = $this->order('id', 'desc')->paginate(20);
        return $res;
    }

    public function getList()
    {
        $data = request()->get();
        if(!empty($data['username'])){
            $userid = (new User)->where('username|nickname', 'like', "%".$data['username']."%")->column("id");
            $this->where('userid', 'in', $userid);
        }
        if(!empty($data['starttime'])){
            $this->where('create_time', ">=", $data['starttime']);
        } 

        if(!empty($data['endtime'])){
            $this->where('create_time', "<=", $data['endtime'].' 29:59:59');
        }

        if(isset($data['name']) && $data['name'] != ''){
            $this->where('name', $data['name']);
        }
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC', 'bouns DESC']; 
            $this->order($sort[$data['sort']]);
        }
        return $this->order('id DESC')->paginate(15, false,['query' => $data]);
    }
    public function betting($money, $type, $user)
    {
        $data = [
            'userid' => $user['id'],
            'money' => $money,
            'code' => $type,
        ];
        $rules = [
            'userid' => 'require|integer',
            'money' => 'require|integer',
            'code' => 'require|integer',
        ];
        $validate = new Validate($rules);
        $result = $validate->check($data);
        if(!$result){
            return ['err' => 4, 'msg' => $validate->getError()];
        }
        $res = $this->save($data);
        if(!$res){
            return ['err' => 3, 'msg' => '下注失败，请重试'];
        }
        $after = $user['money'] - $money;
        $history = [
            'money' => -$money,
            'userid' => $user['id'],
            'before' => $user['money'],
            'after' => $after,
            'remark' => '幸运77开始游戏'
        ];
        (new GameMoneyHistory)->write($history);
        return ['err' => 0, 'betting_id' => $this->id];
    }

    /**派奖 */
    public function setAward($money, $open_res, $betting_id, $user)
    {
        $info = $this->get($betting_id);
        if(!$info){
            return ['err'=> 3, 'msg' => '派奖信息错误'];
        }

        $info = $info->toArray();
        if($info['plan']){
            return ['err'=> 4, 'msg' => '该奖已派'];
        }
        $award = (new Award());
        $award_info = $award->find($open_res['id']);
        $bouns = $money * $award_info['multiple'];
        if ($open_res['teshu']) {
            $award_teshu_info = $award->find($open_res['teshu']);
            $bouns *= $award_teshu_info['multiple'];
        }
        if($bouns > 0){
            $his['bouns'] = $bouns;
        }

        $his['plan'] = json_encode(['common' => $open_res['common'], 'lucky' => $open_res['lucky']]);
        $res = $this->save($his, ['id' => $betting_id]);

        $after = $user['money'] + $bouns;
        if($bouns > 0){
            $GameMoneyHistory = [
                'userid' => $user['id'],
                'money' => $bouns,
                'type' => 1,
                'remark' => '幸运77游戏中奖'
            ];
            (new GameMoneyHistory)->write($GameMoneyHistory);
        }
        return ['err' => 0, 'award_money' =>$bouns, 'after' => $after];
    }


    public function setAward_before($user)
    {
        $info = $this->get(session("betting_id"));
        if(!$info){
            return json(['err' =>2, 'msg' => '押宝信息错误']);
        }
        $info = $info->toArray();
        return $this->dealData($info, $user);
    }

    /**派奖数据处理 */
    public function dealData($data, $user)
    {
        $result = session('result');
        $common =  array_count_values($result['common']);
        /**判断是不是其他（即三个不同） */
        $qt = count($common) == 3 ? ['0' => 3] : $common;
        $betting_max =  max($qt);
        $betting_sign = array_keys($qt, $betting_max)[0];
        /**幸运奖是两个都相同 */
        $isDraw = $data['code'] == $result['lucky'] ? true : false;

        $allAward = (new PluginLuckyAward)->getAllAward();
        $comm = $allAward['com'];
        $money = 0;
        foreach ($comm as $key => $value) {
            $max =  max($value['planArr']);
            $sign = array_keys($value['planArr'], $max)[0];
            if($max == $betting_max && $sign == $betting_sign){
                $money = $data['money'] * $value['multiple'];
                break;
            }
        }

        if($isDraw){
            $luck = $allAward['luck'];
            foreach ($luck as $key => $value) {
                $num = $value['planArr'][0];
                if($num == $data['code']){
                    $money = $money * $value['multiple'];
                    break;
                }
            }
        }
        
        $his['bouns'] = $money;
        $his['plan'] = json_encode($result);    
        $res = $this->save($his, ['id' => $data['id']]);
        if(!$res){
           return ['err'=> 3, 'msg' => '该奖已派'];
        }
        $after = $user['money'] + $his['bouns'];
        $GameMoneyHistory = [
            'userid' => $data['userid'],
            'money' => $his['bouns'],
            'type' => 1,
            'before' => $user['money'],
            'after' => $after,
            'remark' => '幸运77游戏中奖'
        ];
        (new GameMoneyHistory)->write($GameMoneyHistory);
        return ['err' => 0, 'money' =>$money];
    }
}
