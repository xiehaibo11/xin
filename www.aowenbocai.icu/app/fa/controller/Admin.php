<?php
namespace app\fa\controller;

use app\admin\controller\Base;
use app\fa\model\Code;
use app\fa\model\Buy;
use app\admin\model\User;
use app\admin\model\ExtShowList;
use think\Db;

class Admin extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new Buy();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();//测试一下
    }

    public function index()
    {
        return $this->fetch('index',['title'=>'后台管理']);
    }

    public function record()
    {
        $this->redirect('betting');
    }

    public function betting($username = '', $expect = '', $starttime = '', $endtime = '')
    {
        $param  = $this->param;
        if ($username) {
            $userid = $this->likeNameToId($username);
            $this->baseModel->where('userid', 'in', $userid);
        }
        if ($expect) {
            $this->baseModel->where('expect', $expect);
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate(13,false,['query'=>$param]);
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('betting',['title' => '投注管理']);
    }

    /**
     * 投注详情页
     */
    public function bettingArticle()
    {
        $join_model= new Join();
        $info = $this->baseModel->get($this->id);
        $expect = $this->baseModel->getExpect($info['id']);
        $join = $join_model->where('buy_id', $info['id'])->find();
        $this->assign('info',$info);
        $this->assign('expect',$expect);
        $this->assign('join',$join);
        $view = !($info['is_join']) ? 'betting_article' : 'join_article';
        return $this->fetch('index/'.$view,['title' => '投注详情']);
    }

    public function likeNameToId($username)
    {
        $User = new User;
        return $User->where([
            'username' => ['like', "%{$username}%"]
        ])->column('id');
    }

    /**
     * 撤单
     * @return mixed
     */
    public function backMoney()
    {
       $id = $this->id;
       $res = (new Code)->backMoney($id);
       if ($res['code']) {
           return $this->success('撤单成功');
       } else {
           return $this->error('撤单失败');
       }
    }

    /**
     * 删除投注内容
     * @return mixed
     */
    public function buyDelete()
    {
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $info = $this->baseModel->get($data['id']);
            $res = $info->delete();
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = $this->baseModel->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

    /**
     * 删除开奖号码
     * @return mixed
     */
    public function codeDelete()
    {
        $id = $this->id;
        $model = new Code();
        $res = $model->where('id', $id)->delete();
        if ($res) {
            return $this->success('删除成功');
        } else {
            return $this->error('撤单失败');
        }
    }

    public function getInfo($id)
    {
        $info = $this->baseModel->find($id);
        return ['code' =>1, 'info' => $info];
    }

    /**
     * 开奖号码管理
     * @return mixed
     */
    public function code( $expect = '', $starttime = '', $endtime = '')
    {
        $model = new Code();
        $param  = $this->param;
        if ($expect) {
            $model->where('expect', $expect);
        }
        if ($starttime) {
            $model->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $model->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $model->order($order)->paginate(13,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('code',['title'=>'开奖号码管理']);
    }

    /**
     * 添加开奖号码
     * @return mixed
     */
    public function addCode()
    {
        if(request()->isPost()){
            $code_model = new Code();
            $has_expect = $code_model->where('expect', $this->post['expect'])->find();
            if (!empty($has_expect)){
                return $this->success('已存在该期开奖');
            } else {
                $data = [
                    'code' => $this->post['code'],
                    'expect' => $this->post['expect'],
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $res = $code_model->add($data);
                if ($res['code']) {
                    return $this->success('添加开奖号码成功', url("code"));
                } else {
                    return $this->success('添加开奖号码失败');
                }
            }

        }
        return $this->fetch('add_code', ['title' => '添加开奖号码']);

    }

    /**
     * 修改开奖号码
     */
    public function editCode()
    {
        $id = $this->id;
        $code_model = new Code();
        $info = $code_model->get($id);
        if(request()->isPost()){
            $has_expect = $code_model->where('expect', $this->post['expect'])->where('id', 'NEQ', $id)->find();
            if (!empty($has_expect)){
                return $this->success('已存在该期开奖');
            } else {
                $data = [
                    'code' => $this->post['code'],
                    'id' => $id,
                    'expect' => $this->post['expect'],
                ];
                $res = $code_model->edit($data);
                if ($res['code']) {
                    return $this->success('编辑开奖号码成功', url("code"));
                } else {
                    return $this->error('编辑开奖号码失败');
                }
            }
        }
        $this->assign('info',$info);
        return $this->fetch('edit_code', ['title' => '添加开奖号码']);

    }

    public function prize()
    {
        if(request()->isPost()){
            $expect = $this->post['expect'];
            $code = (new Code)->where('expect',$expect)->find();
            if (empty($code)) {
                return $this->error('该期不存在');
            } else {
                $res = (new Code)->sendBonus($expect);
                if ($res['code']) {
                    return $this->success('派奖成功', url("code"));
                } else {
                    return $this->error('派奖失败');
                }
            }
        }
        return $this->fetch('prize',['title'=>'中奖匹配']);
    }

}
