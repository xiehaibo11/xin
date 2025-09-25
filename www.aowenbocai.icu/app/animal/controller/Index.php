<?php

namespace app\animal\controller;

use app\animal\model\Record;
use core\Active;
use app\common\model\Active as AActive;
use core\model\ActiveLog;
use think\Controller;
use think\Db;

class Index extends Base
{
    public function __construct()
    {
        $this->param = request()->param();
        $this->post = request()->post();
        $this->baseModel = new Record();//当前模型
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch('index', ['title' => '动物园']);
    }
    
    /**
     * 获取击打记录
     */
    public function getRecord($page = 1)
    {
        $pageSize = 6;
        $list = $this->baseModel->where('userid', $this->user['id'])->order(['create_time' => 'desc', 'id' => 'desc'])->paginate($pageSize, false, ['page' => $page]);
        $list->append(['name', 'friend_time']);
        return json($list);
    }

    /**
     * 任务排序
     */
    public function orderTask($new_task, $task, $task_id = '') {
        foreach ($task as $v) {
            if (!$task_id) {
                if ($v['active_pid'] == 0) {
                    array_push($new_task, $v);
                    $new_task = $this->orderTask($new_task, $task, $v['activeid']);
                    return $new_task;
                }
            } else {
                if ($task_id == $v['active_pid']) {
                    array_push($new_task, $v);
                    $new_task = $this->orderTask($new_task, $task, $v['activeid']);
                }
            }
        }
        return $new_task;
    }

    /**
     * 获取任务
     * $type = 1 获取全部任务 加上当前任务
     */
    public function getTask($type = '')
    {
        $user = $this->user;
        if (empty($user)) return json(['err' => 1]);
        $active = new Active($user);
        $task_all = $active->task(['ext' => ['in',['animal', '/animal']]]);//所有任务
        $task_level = 1;
        $task_new_all = [];
        $task_new_all = $this->orderTask($task_new_all, $task_all);
        foreach ($task_all as $v) {//任务排序并判断
            if ($v['getaward'] == 2) {
                $task_level += 1;
            }
        }
        $res = $active->nowTask(['ext' => ['in',['animal', '/animal']]]);//当前任务
        if (empty($res)){ $res = []; $task_level = 0; }
        else $res = $res[0];
        $res['task_level'] = $task_level;
        if ($type) {
            return json(['err' => 0, 'data' => $res, 'all' => $task_new_all]);
        } else {
            return json(['err' => 0, 'data' => $res]);
        }

    }

    /**
     * 获取任务
     */
    public function getTaskByid($active_id)
    {
        $active_log = new ActiveLog();
        $active = new Active($this->user);
        $res = $active_log->getShowData('',$this->user['id'],$active_id);
        $res->append(['active_info']);
        return $res[0];
    }

    /**
     * 获取最近大奖排行
     * 取30个
     * 最近一周的
     */
    public function getRank()
    {
        $pageSize = 30;
        $this->baseModel->where('create_time', '>=',date("Y-m-d", strtotime("-1 week")));
        $list = $this->baseModel->where('animal_type', 'in', [1, 2, 3, 11])->where('bonus', '>', 0)->order(['create_time' => 'desc'])->paginate($pageSize);
        $list->append(['user_info', 'friend_time']);
        return json($list);
    }

    /**
     * 领取奖励
     */
    public function getAward($active_id)
    {
        $active = new Active($this->user);
        $res = $active->getAward($active_id);
        if ($res['err']) return json(['err' => 1]);
        return $this->getTask();
    }


}
