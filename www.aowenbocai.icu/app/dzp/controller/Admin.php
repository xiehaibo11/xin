<?php
namespace app\dzp\controller;

use app\common\model\GameDzpSetting;
use think\Controller;
use app\common\model\GameDzpHistory;
use app\admin\model\User;
use app\admin\model\ExtShowList;


class Admin extends Controller
{
    /**
     * 奖品列表
     */
    public function index()
    {
        $setting = \core\Setting::get(['dzp_count']);
        $model = new GameDzpSetting();
        $list = $model->select();
        $list = $list->append(['name'])->toArray();
        $this->assign('list', $list);
        $this->assign('dzp_count', $setting['dzp_count']);
        return $this->fetch('',['title' => '幸运转盘设置']);
    }

    /**
     * 添加奖品
     */
    public function add_setting()
    {
        $model = new GameDzpSetting();
        if(request()->isPost()){
            $data = input('post.');
            $res =  $model->add($data);
            if ($res['code']) {
                $this->success('添加成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $type_option = $model->typeOption();
        $prop_list = (new \app\admin\model\Prop())->select();
        $prop_list = $prop_list->append(['ext_info'])->toArray();
        $this->assign('type_option', $type_option);
        $this->assign('prop_list', $prop_list);
        return $this->fetch('',['title' => '添加奖品']);
    }

    /**
     * 添加奖品
     */
    public function edit_setting($id)
    {
        $model = new GameDzpSetting();
        $info = $model->find($id);
        if(request()->isPost()){
            $data = input('post.');
            $res =  $model->add($data);
            if ($res['code']) {
                $this->success('修改成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $type_option = $model->typeOption($info['type']);
        $prop_list = (new \app\admin\model\Prop())->select();
        $prop_list = $prop_list->append(['ext_info'])->toArray();
        $this->assign('type_option', $type_option);
        $this->assign('prop_list', $prop_list);
        $this->assign('info', $info);
        return $this->fetch('',['title' => '编辑奖品']);
    }

    public function setStatus($id = '')
    {
        if (empty($id)) {
            return $this->error('错误');
        }
        $info = GameDzpSetting::get($id);
        $info->status = $info->status == 0 ? 1 : 0;
        $info->save();
        return $this->success('修改成功');
    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/image/') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    public function set()
    {
        $config = request()->post();
        $res = false;
        $setting = \core\Setting::set($config);
        $this->success('修改成功',url('index'));
    }

    public function record()
    {
        $history = new GameDzpHistory();
        $res = $history->getList(14);
        $page = $res -> render();
        $res->append(['nickname']);
        $res = $res->toArray();
        if(!$res['data']) {
            $this->assign('_empty' , 1);
        }
        // $statis = $history->getList(14,'', 1);
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign('list', $res['data']);
        $this->assign('page', $page);
        return $this->fetch('',['title' => '幸运转盘管理' , 'query' => request()->param()]);
    }

    public function isDelete($id = '',$day = 0)
    {

        $history = new GameDzpHistory();
        if($id){
            $res = $history::destroy($id);
        }
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day", $today));
            $res = $history ->destroy(['create_time' => ['<' , $start]]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

}
