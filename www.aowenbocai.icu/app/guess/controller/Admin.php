<?php
namespace app\guess\controller;

use app\guess\model\GuessBuy;
use app\admin\controller\Base;
use app\admin\model\ExtShowList;
use app\guess\model\Guess as AGuess;

class Admin extends Base
{
    protected $baseModel;
    protected $param;
    protected $post;
    protected $id;
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new AGuess();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }


    /**
     * 竞彩列表
     */
    public function index($title = '', $start_time = '', $end_time = '', $open_time = '')
    {
        $param  = $this->param;
        if ($title) {
            $this->baseModel->where('title', $title);
        }
        if ($start_time) {
            $this->baseModel->where('start_time', $start_time);
        }
        if ($end_time) {
            $this->baseModel->where('end_time', $end_time);
        }
        if ($open_time) {
            $this->baseModel->where('open_time', $open_time);
        }
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate(13,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('index',['title' => '竞猜列表']);
    }

    public function addGuess()
    {
        if(request()->isPost()){
            return $this->addCommon();
        }
        $type_option = $this->baseModel->typeOption();
        return $this->fetch('add_guess', ['title' => '添加竞猜', 'type_option' => $type_option]);
    }

    public function editGuess()
    {
        if(request()->isPost()){
            return $this->addCommon();
        }
        $info = $this->baseModel->find($this->id);
        $status_option = $this->baseModel->statusOption($info->getData('status'));
        $type_option = $this->baseModel->typeOption($info->getData('type'));
        $this->assign('info', $info);
        $this->assign('type_option', $type_option);
        $this->assign('status_option', $status_option);
        return $this->fetch('edit_guess', ['title' => '编辑竞猜']);
    }

    private function addCommon() {
        $data = $this->post;
        $option = $data['options'];
        foreach($option as $key => $value){
            $new[] = [$value => $data['odds'][$key]];
        }
        $data['options'] = json_encode($new);
        unset($data['odds']);
        $res = $this->baseModel->add($data);
        if ($res['code']) {
            return $this->success($res['msg'], url("index"));
        } else {
            return $this->error($res['code']);
        }
    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/image/') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    /**
     * 记录列表
     */
    public function record($username='', $title = '', $starttime = '', $endtime = '')
    {
        $Model = new GuessBuy();
        $param  = $this->param;
        if ($username) {
            $userid = $this->likeNameToId($username);
            $Model->where('userid', 'in', $userid);
        }
        if ($title) {
            $guess_id = $this->likeTitleToId($title);
            $Model->where('guess_id', 'in', $guess_id);
        }
        if ($starttime) {
            $Model->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $Model->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $Model->order($order)->paginate(13,false,['query'=>$param]);
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('betting_list',['title' => '竞猜记录']);
    }

    public function likeTitleToId($title)
    {
        return $this->baseModel->where([
            'title' => ['like', "%{$title}%"]
        ])->column('id');
    }

    public function likeNameToId($username)
    {
        $User = new \app\admin\model\User();
        return $User->where([
            'username' => ['like', "%{$username}%"]
        ])->column('id');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $res = (new AGuess())->deleteData(['id' => $this->id]);
        if($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }

    public function buyDelete($id = '',$day = 0)
    {

        $Model = new GuessBuy();
        if($id){
            $res = $Model::destroy($id);
        }
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day", $today));
            $res = $Model->destroy(['create_time' => ['<' , $start], 'status' => 1]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

    /**
     * 投注撤单
     * @return mixed
     */
    public function backMoney()
    {
        $Model = new GuessBuy();
        $id = $this->id;
        $res = $Model->backMoney($id);
        if ($res['code']) {
            return $this->success('撤单成功', url('bettingList'));
        } else {
            return $this->error('撤单失败');
        }
    }

    /**
     * 开奖页面
     */
    public function awards()
    {
        $info = $this->baseModel->find($this->id);
        if(request()->isPost()){
            $res = $this->baseModel->awards($this->id, $this->post);
            if ($res['code']) {
                return $this->success($res['msg'], url('index'));
            } else {
                return $this->error($res['msg']);
            }
        }
        $this->assign('info', $info);
        return $this->fetch('awards', ['title' => '开奖管理']);
    }

}