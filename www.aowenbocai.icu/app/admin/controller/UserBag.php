<?php
namespace app\admin\controller;

use think\Validate;

class UserBag extends Base
{

    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new \app\common\model\UserBag();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index($words = '')
    {
        $pageSize = 10;
        $where = [];
        if ($words) {
            $user_array = $this->getUserId($words);
            $where['userid'] = ['in', $user_array];
        }
        $list = $this->baseModel->getList('', $where, function($item, $key){

        });
        return $this->fetch('index',['title' => '玩家道具', 'list' => $list, 'query' => ['words' => $words]]);
    }

    public function getUserId($words) {
        $res = (new User())->where('nickname|username', 'like', "%{$words}%")->column('id');
        return $res;
    }

    public function add()
    {
        $prop = (new \app\admin\model\Prop())->select();
        if (request()->isPost()) {

            return $this->success('修改成功', 'index');
        }
        $info['prop'] = $prop;
        return $this->fetch('add', ['title' => '添加', 'info' => $info]);
    }

    public function edit($id = '')
    {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $Fish = new \app\common\model\UserBag();
        $info = $Fish->find($id);
        $info = $info->append(['user_info', 'name']);
        if (request()->isPost()) {
            $info->num = request()->post()['num'];
            $info->save();
            return $this->success('修改成功', 'index');
        }

        return $this->fetch('edit', ['title' => '修改', 'info' => $info]);
    }

    public function remove($id) {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $info = \app\common\model\UserBag::get($id);
        if (!$info) {
            return $this->error('错误，操作非法');
        }
        $info->delete();

        return $this->success('删除成功', 'index');
    }


}
