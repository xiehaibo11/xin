<?php
namespace core\model;

use think\Model;
use app\common\model\Active;
use app\common\controller\Extend;
use app\index\model\User;

class ActiveLog extends Model
{
    protected $updateTime = false;
    protected $resultSetType = "collection";

    /**初始化 */
    public function reset($userid)
    {
        $this->userid = $userid;
        $activeId = $this->where(['userid' => $this->userid, 'create_time' => ['>', date("Y-m-d")]])->column('activeid');
        $task = (new Active)->where('id', 'not in', $activeId)->where('status' , 1)->select();
        if (!empty($task)) {
            $this->addTaskLog($task);
        }
    }

    /**
     * 获取器--获取任务信息
     */
    public function getActiveInfoAttr($value, $data)
    {
        $res = (new Active)->get($data['activeid']);
        if($res) $res = $res->toArray();
        $res['err'] = '';
        if($res['pid'] != 0){
            $res['err'] = 1;
        }
        return $res;
    }
    /**
     * 获取器--获取用户信息
     */
    public function getUserInfoAttr($value, $data)
    {
        $res = (new User)->get($data['userid']);
        if($res) $res = $res->toArray();
        return $res;
    }
    /**
     * 获取器--获取模块信息
     */
    public function getExtInfoAttr($value, $data)
    {
        if($data['ext'] == 'login') return ['title' => '登录'];
        if($data['ext'] == 'sign') return ['title' => '签到'];
        if($data['ext'] == 'brisk') return ['title' => '活跃任务'];
        $res = (new Extend)->getInfo($data['ext']);
        return ['title' => $res['title']];
    }
    /**查询当日任务 */
    public function getShowData($where = [],$userid,$id = '')
    {
        $this->where(['userid' => $userid, 'create_time' => ['>=', date("Y-m-d")]]);
        if($where) $this->where($where);
        if($id) $this->where(["activeid" =>$id]);
        return $this->select();
    }

    /**查询所有任务 */
    public function getShowList($where = '')
    {
        return $this->where($where)->paginate(14);
    }
    /**
     * 添加用户任务信息
     */
    public function addTaskLog($task){
        $data = [];
        foreach($task as $value){
            $data[] = [
                'userid' => $this->userid,
                'activeid' =>$value['id'],
                'ext' => $value['countext'],
                'type' => $value['ways']
            ];
        }
        if(!empty($data))$this->saveAll($data);
    }
}
