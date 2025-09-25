<?php
namespace app\animal\controller;

use app\common\model\MoneyHistory;
use core\Active;
use app\index\model\User;
use core\model\ActiveLog;
use think\Controller;
use think\Request;

class AnimalApi extends Controller
{
    protected $api_token = 'jfdoismdo32';//接口验证码

    /**
     * 添加资金明细
    */
    public function addMoney($api_token, $sid, $money, $module, $back_type = ''){
        if ($api_token != $this->api_token) return $back_type ? ['err' => 1] : json(['err' => 1]);
        $user = (new User())->where('sid', $sid)->find();
        if (empty($user)) return $back_type ? ['err' => 1] : json(['err' => 1]);
        $type = 0;
        if ($money > 0) {
            $type = 1;
        }
        $res = (new MoneyHistory())->write([
            'userid' => $user['id'],
            'money' => $money,
            'ext_name' => $module,
            'type' => $type
        ]);//添加资金明细

        if ($res['code']) {
            return $back_type ? ['err' => 0, 'money' => $res['afterMoney']] : json(['err' => 0, 'money' => $res['afterMoney']]);
        }
        return $back_type ? ['err' => 1] : json(['err' => 1]);
    }

    /**
     * 击杀资金记录  以及 任务判断  bouns,animalType,children[money,animalType]
     */
    public function animalMoney($api_token, $sid, $module, $animal){
        if ($api_token != $this->api_token) return json(['err' => 1]);
        $user = (new User())->where('sid', $sid)->find();
        if (empty($user)) return json(['err' => 1]);
        $task = $this->getTask($sid);
        $animal = json_decode($animal, true);
        if (empty($animal)) return json(['err' => 1]);
        $money = $animal['bouns'];
        $has_task_animal = 0;
        if (!$task['err'] and $animal['animalType'] == $task['data']['info']) {
            $this->addActiveNum($user, $task['data']['activeid']);
            $has_task_animal = 1;
        }
        if (!empty($animal['children'])) {
            foreach ($animal['children'] as $v) {
                $money += $v['money'];
                if (!$task['err'] and $v['animalType'] == $task['data']['info']) {
                    $this->addActiveNum($user, $task['data']['activeid']);
                    $has_task_animal = 1;
                }
            }
        }
        $res = $this->addMoney($api_token, $sid, $money, $module, 1);
        $new_task = ['err' => 1];
        if ($has_task_animal == 1) {
            $new_task = $this->getTask($sid);
        }
        if (!$res['err']) {
            $back_money = $res['money'];
        } else {
            $back_money = $user['money'];
        }
        return json(['err' => 0, 'money' => $back_money, 'task' => $new_task]);

    }

    /**
     * 获取任务
     */
    public function getTask($sid = '')
    {
        $user = (new User())->where('sid', $sid)->find();
        if (empty($user)) return ['err' => 1];
        $active = new Active($user);
        $task_all = $active->task(['ext' => ['in',['animal', '/animal']]]);//所有任务
        $task_level = 1;
        foreach ($task_all as $v) {
            if ($v['getaward'] == 2) {
                $task_level += 1;
            }
        }
        $res = $active->nowTask(['ext' => ['in',['animal', '/animal']]]);//当前任务
        if (empty($res)) {
            return ['err' => 1];
        } else $res = $res[0];

        $res['task_level'] = $task_level;
        return ['err' => 0, 'data' => $res];

    }

    /**
     * 获取任务
     */
    public function getTaskByid($user, $active_id)
    {
        $active_log = new ActiveLog();
        $active = new Active($user);
        $res = $active_log->getShowData('', $user['id'], $active_id);
        $res = $res->append(['active_info'])->toarray();
        return $res[0];
    }

    /**
     * 增加任务进度
     */
    public function addActiveNum($user, $active_id)
    {
        $active = new Active($user);
        $active->addInterface($active_id,1);
    }

}
