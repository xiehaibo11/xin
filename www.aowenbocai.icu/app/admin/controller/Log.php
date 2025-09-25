<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Log as ALog;
use app\admin\model\User;

class Log extends Base
{
    /**
     * [index description]
     * @param  string $words     关键字
     * @param  string $starttime 开始时间
     * @param  string $endtime   结束时间
     * @return [type]            [description]
     */
    public function index($words = '', $starttime = '', $endtime = '')
    {

        $paginate = [];
        $ALog = new ALog;

        if ($words) {
            $userid = $this->likeNameToId($words);

            $ALog->where(function($query) use ($words, $userid){
                $query->whereOr('userid', 'in', $userid)
                ->whereOr('create_ip|remark', 'like', "%{$words}%");
            });
            $paginate['query'] = ['words' => $words];
        }
        if ($starttime) {
            $ALog->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $ALog->where('create_time', '<', $endtime);
        }


        $list = $ALog->where(['type' => 1])->order("id desc")->paginate(14, false, $paginate);
        $list->append(['username']);
        return $this->fetch('index',['title' => '系统日志', 'list' => $list, 'query' => ['words' => $words]]);
    }

    public function likeNameToId($username)
    {
        $User = new User;
        return $User->where([
            'username|nickname' => ['like', "%{$username}%"]
        ])->column('id');
    }

    /**
     * 清除日志
     * @param  string $day     清除天数
     * @return string
     */
    public function delete($day="")
    {
        $ALog = new ALog;
        if ($day){
            $endtime = date('Y-m-d', strtotime(" -$day day"));
            if($ALog->where('create_time', '<', $endtime )->delete()){
                return $this->success('数据已清空',url("admin/Log/index"));
            }
        } else {
            if($ALog->where('1=1')->delete()){
                return $this->success('数据已清空',url("admin/Log/index"));
            }
        }
        return $this->error('没有可清空的数据',url("admin/Log/index"));
    }

}
