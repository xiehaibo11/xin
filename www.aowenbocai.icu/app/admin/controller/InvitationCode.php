<?php

namespace app\admin\controller; 

class InvitationCode extends Base {

    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new \app\common\model\InvitationCode();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        $list = $this->getList();
        return $this->fetch('index',['title'=>'推广链接管理', 'list' => $list]);
    }

    /**
     * 分类列表
     */
    public function getList()
    {
        $post  = $this->param;
        if ($post['words']) {
            $this->baseModel->where('code', 'like', "%{$post['words']}%");
        }
        if ($post['user_name']) {
            $userid = $this->likeUserNameToId($post['user_name']);
            $this->baseModel->whereIn('userid', $userid);
        }
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        $list = $this->baseModel->paginate($pageSize)->each(function ($item, $key) {
            $item['nick_name'] = $item['nick_name'];
            $item['type_txt'] = $item['type_txt'];
            $item['rebate_txt'] = '';
            foreach ($item['rebate'] as $key => $v) {
                if ($key == 'ssc') {
                    $txt = '时时彩';
                } elseif ($key == 'ks') {
                    $txt = '快三';
                } elseif ($key == 'syxw') {
                    $txt = '11选5';
                } else {
                    $txt = $key;
                }
                $item['rebate_txt'] .= '[' . $txt . ':' . '<font style="color:red;">' . $v . '</font>] ';
            }
        });
        return $list;
    }

    public function likeUserNameToId($username)
    {
        $User = new \app\admin\model\User();
        return $User->where([
            'nickname' => ['like', "%{$username}%"]
        ])->whereOr([
            'username' => ['like', "%{$username}%"]
        ])->column('id');
    }

    public function edit()
    {
        $info = $this->baseModel->find($this->id);
        $user_info = (new \app\admin\model\User())->find($info['userid']);
        if(request()->isPost()){
            $data = $this->post;
            $data['rebate'] = [
                'ssc' => $data['ssc'],
                'ks' => $data['ks'],
                'syxw' => $data['syxw'],
                'pk10' => $data['pk10'],
                'pc28' => $data['pc28'],
            ];
            $res =  $this->baseModel->add($data, $user_info, $this->id);
            if ($res['code']) {
                $this->success('更新成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $this->assign('info', $info);
        $this->assign('user_rebate', $user_info['rebate']);
        return $this->fetch('edit', ['title' => '修改邀请码']);

    }

}