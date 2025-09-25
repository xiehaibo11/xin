<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Active as AActive;
use \app\admin\model\Prop as AProp;
use core\model\ActiveLog;
use app\admin\model\ExtShowList;
use app\admin\model\User;
use app\common\controller\Extend;

class Active extends Base
{
    /**
     * 每日任务列表
     */

    public function index($class = 'tasklist')
    {
        $active = new AActive;
        $list = $active->getList();
        $page = $list->render();
        if(!count($list)) $this->assign('_empty',1);

        $extshow = new ExtShowList;
        $res = $extshow->getList();
        $list->append(['ext_name']);
        foreach ($list as $key => &$value) {
            switch($value['ext'])
            {
                case "/index/User":
                    $value['ext_name'] = '用户中心';
                    break;
                case "User":
                    $value['ext_name'] = '用户中心';
                    break;
                case "brisk":
                    $value['ext_name'] = '活跃任务';
                    break;
                default:
                    $ext = ltrim($value['ext'], '/');
                    $ext = [$ext,'/'.$ext];
                    $exx = $extshow->get(['name' => ['in' , $ext]]);
                    if($exx){
                        $exx = $exx->toarray();
                        $value['ext_name'] = $exx['title'];
                    }else{
                        $value['ext_name'] = "未知";
                    }
                    break;
            }
        }

        $alltask = $active->getAllList();
        if($alltask) $alltask->toarray();
        $this->assign('activeclass',$class);
        $this->assign('extshow',$res);
        $this->assign('alltask',$alltask);
        $this->assign('list',$list);
        $this->assign('page',$page);

        return $this->fetch('',['title' =>'每日任务']);
    }

    /** 添加任务*/
    public function addtask()
    {
        $data = input("post.");
        $active = new AActive;
        $res = $active->addtask($data);
        if(!$res) $this->error('添加失败');
        $this->success('添加成功');
    }

    /**
     * 修改信息
     *@param  int  $id 任务ID
     */
    public function change($id)
    {
        $active = new AActive;
        if(request()->post()){
            $data = input("post.");
            if($data['status'] != 1) $active->save(['pid' => 0],['pid' => $id]);
            $resup = $active->save($data, ['id' =>$id]);
            if(!$resup) $this->error('修改失败');
            $this->success('修改成功');
        }
        $res = $active->get($id);
        if(!$res) $this->error('非法操作');

        $alltask = $active->getAllList();
        if($alltask) $alltask->toarray();
        $extshow = new ExtShowList;
        $extshowres = $extshow->getList();
        $res = $res->toarray();
        switch($res['ext'])
        {
            case "/index/User":
                $exx = '用户中心';
                break;
            case "User":
                $exx = '用户中心';
                break;
            case "brisk":
                $exx = '活跃任务';
                break;
            default:
                $ext = ltrim($res['ext'], '/');
                $ext = [$ext,'/'.$ext];
                $exx = $extshow->get(['name' => ['in', $ext]]);
                if($exx){
                    $exx = $exx->toarray();
                    $exx = $exx['title'];
                }else{
                    $exx = "未知";
                }
                break;
        }

        switch ($res['countext']) {
            case 'game':
                $type = '所有游戏';
                break;
            case 'login':
                $type = '用户登录';
                break;
            case 'sign':
                $type = '用户注册';
                break;
            case 'brisk':
                $type = '活跃任务';
                break;
            default:
                $ext = ltrim($res['ext'], '/');
                $ext = [$ext,'/'.$ext];
                $type = $extshow->get(['name' => ['in', $ext]]);
                if($type){
                    $type = $type->toarray();
                    $type = $type['title'];
                }else{
                    $type = "未知";
                }
                break;
        }
        $spend = $res['ways'] == "times" ? '游戏次数' : '游戏消费';
        $res['statuss'] = $res['status'] ? "开启" : '关闭';
        if($res['pid'] != 0){
            $parent = $active->getAllList(['id'=>$res['pid']])->toarray()[0];
        }
        $par = $res['pid'] != 0 ? $parent['content'] : '优先显示';
        $alltask = $active->getAllList();
        if($alltask) $alltask->toarray();
        $this->assign('ext_name',['exx' =>$exx,'type'=>$type,'spend'=>$spend,'par' => $par]);
        $this->assign('extshow',$extshowres);
        $this->assign('alltask',$alltask);
        $this->assign('info',$res);
        return $this->fetch('',['title' =>"修改任务信息"]);
    }

    public function award($id)
    {
        $active = new AActive;
        if(\request()->post()){
            $data = json_encode(input('post.'));
            $res = $active->save(['award' => $data], ['id' => $id]);
            $res && $this->success('设置成功','index');
        }
        $res = $active->getList(['id' =>$id]);
        if(!$res) $this->error('信息错误，请重试');
        $res = $res->toarray()['data'][0];
        $res['award'] = json_decode($res['award'], true);
        $res['coupon_list'] = (new AProp)->where('type', 3)->select();
        $res['coupon_list'] = $res['coupon_list']->toArray();
        $this->assign('info',$res);
        return $this->fetch('lookset',['title' =>'查看设置任务奖励']);
    }
    /**删除数据 */
    public function deletedata($id)
    {
        $active = new AActive;
        $log = new ActiveLog;
        $res = $active->destroy(['id' =>$id]);
        $log->destroy(['activeid' =>$id]);
        $active->save(['pid' =>0],['pid' => $id]);
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/personal/') . '?t=task' . time();
        return ['code' => 1, 'data' => $path];
    }

    // 任务日志列表
    public function log()
    {
        $log = new ActiveLog;
        $data = request()->get();
        if($data){
            if($data['starttime']){
                $log ->where("create_time", ">", $data['starttime']);
            }
            if($data['endtime']){
                $log ->where("create_time", "<", $data['endtime']);
            }
            if($words = $data['words']){
                $user = (new User)->where('nickname|username', 'like', "%{$words}%")->column('id');
                if( stripos($data['words'],'登录') !== false ||  stripos($data['words'],'注册') !== false){
                    $ext = stripos($data['words'],'登录') !== false ? 'login' : 'sign';
                }else{
                    $ext = (new ExtShowList)->where('title', 'like', "%{$words}%")->column('name');
                }
                $activeinfo = (new AActive)->where('content', 'like', "%$words%")->column('id');
                if($user) $log->where('userid', 'in', $user);
                if($activeinfo) $log->whereOR('activeid', 'in', $activeinfo);
                if($ext) $log->whereOR('ext', 'in', $ext);
            }
        }
        $list = $log->order('id desc')->paginate(14);
        $page = $list->render();
        $list->append(['user_info','ext_info','activeinfo']);
        if($list) $list = $list->toarray();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('query',$data);
        return $this->fetch('',['任务日志管理']);
    }

    public function deletelogdata($id = '',$day = 0)
    {
        $log = new ActiveLog;
        if($id){
            $res = $log::destroy($id);
        }
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day", $today));
            $res = $log ->destroy(['create_time' => ['<' , $start]]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }
}