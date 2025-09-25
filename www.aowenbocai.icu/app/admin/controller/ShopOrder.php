<?php
namespace app\admin\controller;

use app\admin\model\ShopOrder as AshopOrder;
use app\admin\model\Shop as Ashop;
use app\admin\model\User;
use app\admin\model\UserIdbank;
use think\Db;

class ShopOrder extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new AshopOrder();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index($words = '', $starttime = '', $endtime = '')
    {
        $param  = $this->param;
        if ($words) {
            $userid = $this->likeUserNameToId($words);
            $shop_id = $this->likeNameToId($words);
            $this->baseModel->where(function($query) use ($words, $userid, $shop_id){
                $query->whereOr('userid', 'in', $userid)
                    ->whereOr('shop_id', 'in', $shop_id)
                    ->whereOr('remark', 'like', "%{$words}%");
            });
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        $list = $this->baseModel->order('create_time desc')->paginate(15,false,['query'=>$param]);
        // print_r($list->toArray());die;
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('index',['title'=>'商品管理']);
    }

    public function likeUserNameToId($username)
    {
        $User = new User;
        return $User->where([
            'nickname' => ['like', "%{$username}%"]
        ])->whereOr([
            'username' => ['like', "%{$username}%"]
        ])->column('id');
    }

    public function likeNameToId($name)
    {
        $User = new Ashop;
        return $User->where([
            'name' => ['like', "%{$name}%"]
        ])->column('id');
    }

    public function edit()
    {
        if(request()->isPost()){
            $data = $this->post;
            $res =  $this->baseModel->edit($data);
            if ($res['code']) {
                $this->success('编辑处理成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $info = $this->baseModel->allowField(['status', 'remark'])->find($this->id);
        $status_option = $this->baseModel->statusOption($info->getData('status'));
        $this->assign('info', $info);
        $this->assign('status_option', $status_option);
        return $this->fetch('edit', ['title' => '编辑处理']);
    }

    public function info()
    {
        $order_info = $this->baseModel->find($this->id);
        if (!$order_info) return ['code' => 1, 'msg' => '订单不存在'];
        $id_bank_model = (new UserIdbank());
        $idbank = $id_bank_model->where('userid', $order_info['userid'])->find();
        if ($idbank) {
            $idbank = $idbank->toArray();
            $idbank_banks = json_decode($idbank['banks'], true);
            $bank_list = [];
            if (!empty($idbank_banks)) {
                foreach ($idbank_banks as $v) {
                    if ($v['type'] == 1) {
                        array_push($bank_list, [
                            'yh_number' => $v['numbers'],
                            'openname' => $v['openname']
                        ]);
                    } elseif ($v['type'] == 2) {
                        $idbank['zfb_number'] = $v['numbers'];
                    } elseif ($v['type'] == 3) {
                        $idbank['wx_number'] = $v['numbers'];
                    }
                }
                $idbank['bank_list'] = $bank_list;
            }
        }
        $info = Db::name('address')->find($this->id);
        if (empty($info) and !$idbank) {
            return ['code' => 0];
        }
        return ['code' => 1, 'data' => ['info' => $info, 'idbank' => $idbank]];
    } 
    /**删除数据 */
    public function isDelete()
    {
        $data = request()->param();
        if(empty($data['id'])){
            $this->error('信息错误');
        }
		$res = (new AshopOrder)->destroy($data['id']);
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    } 

    /**获取未处理订单 */
    public function getNum($is_json)
    {
        $res['num'] = (new AshopOrder)->where('status', 0)->count();
        $res['err'] = $res['num'] > 0 ? 0 : 1;
        return $is_json ? json($res) : $res;
    }
}
