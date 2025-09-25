<?php
namespace core;

use app\common\model\UserBag;
use app\index\model\User;
use app\common\model\Active as AActive;
use core\model\ActiveLog;
use app\common\model\FlowerHistory;
use app\common\model\MoneyHistory;

class Active
{
    public function __construct($userid)
    {
        $this->userid = $userid['id'];
        // $this->activeNum = $userid['activeNum'];
        (new ActiveLog)->reset($this->userid);
    }
    /**
     *每日任务
     *@param array $type
     *@retrun task()
     */
    public function task($type = [])
    {
        $active = new AActive;
        $log = new ActiveLog;
        $res = $log->getShowData($type,$this->userid);
        $res->append(['activeInfo']);
        if(!$res) return ['err' => 1 , 'msg' => '没有关于您的任务信息'];
        $res = $res->toarray();

        /**判断任务是否完成，是否领取*/
        $making = [];
        $finsh = [];
        $finshGive = [];
        foreach ($res as $key => &$value) {
            if (!$value['activeInfo']['status']) continue;
            $award = json_decode($value['activeInfo']['award'], true);
            $award['active'] = ['name' =>'活跃度','value' =>$award['active'] ?? 0];
            $award['exp'] = ['name' =>'经验','value' =>$award['exp'] ?? 0];
            $award['coin'] = ['name' =>'金币','value' =>$award['coin'] ?? 0];
            $list = $this->getInfo($value, $award);

            if(!$value['getaward']) {
                $making[] =  $list;
            }
            if($value['getaward'] == 1)
            {
                $finsh[] = $list;
            }
            if($value['getaward'] == 2)
            {
                $finshGive[] = $list;
            }
        }
        $res = array_merge($finsh,$making,$finshGive);
        return $res;
    }

    /**
     *当前每日任务 -- 一个
     *@param array $type
     *@retrun 当前任务和已经完成的任务
     */
    public function nowTask($type = [])
    {
        $active = new AActive;
        $log = new ActiveLog;
        $res = $log->getShowData($type,$this->userid);
        $res->append(['activeInfo']);
        if(!$res) return ['err' => 1 , 'msg' => '没有关于您的任务信息'];
        $res = $res->toarray();
        /**判断任务是否完成，是否领取*/
        $making = [];
        $finsh = [];
        foreach ($res as $key => &$value) {
            if (!$value['activeInfo']['status']) continue;
            if($value['getaward'] == 2){
                $newid = $active->getid($value['activeInfo']['id']);
                if($newid){
                    $newid = $newid->toarray();
                    $new = $log->getShowData('',$this->userid, $newid['id']);
                    $new->append(['activeInfo']);
                    if($new) {
                        $new = $new->toArray()[0];
                        if($new['getaward'] != 2){
                            $new['activeInfo']['err'] = '';
                            $res[] = $new;
                        }
                    }
                }
            }
            if($value['activeInfo']['err'] == 1 || $value['getaward'] == 2) continue;
            $award = json_decode($value['activeInfo']['award'], true);
            $award['active'] = ['name' =>'活跃度','value' => $award['active'] ?? 0];
            $award['exp'] = ['name' =>'经验','value' => $award['exp'] ?? 0];
            $award['coin'] = ['name' =>'金币','value' => $award['coin'] ?? 0];
            $list = $this->getInfo($value, $award);

            if(!$value['getaward']) {
                $making[] =  $list;
            }
            if($value['getaward'] == 1)
            {
                $finsh[] = $list;
            }
        }
        $res = array_merge($finsh,$making);
        return $res;
    }

    public function getInfo($value, $award)
    {
        $value['activeInfo']['ext'] = ltrim($value['activeInfo']['ext'], '/');
        $list = [
            'activeid' => $value['activeid'],
            'active_pid' => $value['activeInfo']['pid'],
            'info' => $value['activeInfo']['info'],
            'content' => $value['activeInfo']['content'],
            'logo' => $value['activeInfo']['logo'],
            'countValue' => $value['activeInfo']['countvalue'],
            'ext' =>$value['activeInfo']['ext'],
            'getaward' =>$value['getaward'],
            'over' =>$value['active'],
            'award' => $award,
        ];
        return $list;
    }
    // 首页获取有关活跃度的
    public function getActive()
    {
        $log = new ActiveLog;
        $list = $log->getShowData(['ext' =>'brisk'],$this->userid);
        $list->append(['activeInfo']);
        if(!$list) return ['err' => 1, 'msg' =>'没有活跃任务'];;
        $list = $list->toarray();
        foreach ($list as $key => &$value) {
            $award = json_decode($value['activeInfo']['award'], true);
            $award['active'] = ['name' =>'活跃度','value' =>$award['active'] ?? 0];
            $award['exp'] = ['name' =>'经验','value' =>$award['exp'] ?? 0];
            $award['coin'] = ['name' =>'金币','value' =>$award['coin'] ?? 0];
            $value['award'] = $award;
            $listData[$key] = $value['activeInfo']['countvalue'];
            $_list[$key] = $this->getInfo($value, $award);

        }
        if(is_array($listData)){
            array_multisort($listData, SORT_ASC, $_list);
        }
        return ['value' => $listData, 'data' =>$_list];
    }
    /**
     * 领取奖励 -- 更新active_log 表中用户名（userid）以及任务ID(active)对应的getaward字段
     * @param object $res
     */
    public function getAward($id)
    {
        $log  = new ActiveLog;
        $logres = $log->getShowData('',$this->userid,$id);

        if(!$logres) return ['err' => 0, 'msg' => '信息错误'];
        $logres->append(['activeInfo']);
        $logres = $logres->toarray()[0];
        if($logres['getaward'] < 1) return ['err' => 1, 'msg' => '该奖励未达到领奖状态'];
        if($logres['getaward'] == 2) return ['err' => 1, 'msg' => '该奖励已领取'];
        $res = $log->save(['getaward'=>2],['id' =>$logres['id']]);
        if(!$res) {
            return ['err' =>1 , 'msg' =>'领取失败，请重试'];      
        } 

        if(!$logres['activeInfo']['award']){
             return ['err' => 0, 'msg' =>'领取成功'];
        }
        $award= json_decode($logres['activeInfo']['award'], true);
        /**处理奖励*/
        foreach($award as $key => $value){
            if($key == 'ext' && $value > 0){
                $this->setExp($value);
            }
            if($key == 'coin' && $value > 0){
                (new MoneyHistory)->write(['userid' =>$this->userid, 'money' => $value, 'type' => 4, 'ext_name'=>'active','remark' =>$logres['activeInfo']['content']]);
            }
            if($key == 'prop_param' && $value != ''){
                (new UserBag())->add($this->userid, $value);
            }
            if($key == 'active' && $value > 0){
                /**用户活跃任务的完成*/
                $this->getActiveList($value);
            }
        }


        return ['err' => 0, 'msg' =>'领取成功'];
    }

    /**
     * 用户活跃任务的调用完成
     * @param int $active 活跃值
     */
    public function getActiveList($active)
    {
        $log = new ActiveLog;
        $list = $log->getShowData(['ext' =>'brisk'],$this->userid);
        $list->append(['activeInfo']);
        $list = $list->toarray();
        if(!$list) return false;
        /**该任务若领取则调用下个任务进行活跃调用*/
        foreach ($list as $key => $value) {
            if($value['getaward'] > 0) continue;
            $newactive = $value['active'] + $active;
            $getaward = $newactive >= $value['activeInfo']['countvalue'] ? 1 : 0;
            $data[] = [
                'id' => $value['id'],
                'getaward' => $getaward,
                'active' => $newactive,
                'type' => 4
            ];
        }
        $log->saveAll($data);
    }
    /**
     * 其他模块调用生成任务数据
     * @param array,int $activeid 任务ID
     * @param int $active 活跃值(就是任务完成量)
     */
    public function addInterface($activeid, $activenum)
    {
        if(is_array($activeid)){
            $ids = [];
            foreach ($activeid as $value) {
                $ids[] = $this->setInterface($value, $activenum);
            }
        }else{
            $this->setInterface($activeid, $activenum);
        }
    }

    public function setInterface($activeid, $activenum)
    {
        $log = new ActiveLog;
        $logres = $log->getShowData('',$this->userid,$activeid);
        /**判断任务是否存在 */
        if($logres->isEmpty()){
            return false;
        }
        $logres->append(['activeInfo']);
        $logres = $logres->toarray()[0];

        /**判断任务领奖状态是否是已完成 */
        if($logres['getaward'] > 0){
            return false;
        }
        /**判断该任务的上级任务是否完成，未领奖则不进行该任务的完成*/
        if($logres['pid']){
            $pidres = $this->upCompare($logres['activeInfo']['pid']);
            if(!$pidres['code']) return false;
        }

        $newactive = $logres['active'] + $activenum;
        $getaward = $newactive >= $logres['activeInfo']['countvalue'] ? 1 : 0;
        $log->save(['getaward' => $getaward, 'active' =>$newactive], ['id' =>$logres['id']]);
        return true;
    }
    /**
     * 修改用户经验值
     */
    public function setExp($award){
        (new User)->updateInc('exp', $award, ['id' =>$this->userid]);
    }
    /**
     * 更新用户活跃值
     */
    public function setActive($award){
        (new User)->updateInc('activeNum', $award, ['id' =>$this->userid]);
    }
    /**判断该任务的上级任务是否完成，未领奖则不进行该任务的完成*/
    public function upCompare($pid)
    {
        $log = new ActiveLog;
        $pinfo = $log->getShowData('',$this->userid,$pid);
        $pinfo->append(['activeInfo']);

        if($pinfo){
            $pinfo = $pinfo->toarray()[0];
            if($pinfo['getaward'] < 2) return ['code' => 0];

        }
        return ['code' => 1];
    }
}