<?php
    namespace app\admin\controller;

    use app\admin\controller\Base;
    use app\admin\model\User;
    use app\admin\model\ExtShowList;
    use app\admin\model\UserAction as AUserAction;
    // use app\common\model\UserAction as BUserAction;
    use app\common\controller\Extend;

    class UserAction extends Base
    {   
        

        /**
         * @param $word 关键字
         * @param $starttime 起始时间
         * @param $endtime 结束时间
         */
        public function index($words = '', $starttime = '' , $endtime = '')
        {   
            $action = new AUserAction;
            if ($words) {
                $user = (new User)->where('nickname', 'like', "%{$words}%")->whereOr('username', 'like', "%{$words}%")->column('id');
                $ext = (new ExtShowList)->where('title', 'like', "%{$words}%")->column('name');
                $action->where('content', 'like', "%{$words}%")
                        ->whereOr('userid','in', $user)
                        ->whereOr('ext_name','in', $ext);
            }
            if ($starttime) {
                $action->where('create_time', '>=', $starttime);
            }
            if ($endtime) {
                $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
                $action->where('create_time', '<', $endtime);
            }
            $list = $action->order('id','desc')->paginate(14);
            $list->append(['userinfo', 'extinfo']);
            $page = $list ->render();
            $list = $list->toarray();
            $this->assign('list',$list);
            $this->assign('page',$page);
            $this->assign('query',input("get."));
            return $this->fetch('',['title' =>'动态管理']);
        }

        public function deletedata($id = '',$day = 0)
        {
            $action = new AUserAction;
            if($day){
                if($day != 'all') $where['create_time'] = ['<' , date("Y-m-d H:i:s",strtotime(" -{$day} day"))];
                if($day == 'all') $where['id'] = ['neq',0];
                $res = $action->where($where)->delete();
            }
            if($id){
                $res = $action->destroy($id);
            }
            if($res)  $this->success('删除成功');
            $this->error('删除失败');
            
        }

        /**
         * 动态统计列表
         */
        public function statis()
        {
            $data = request()->get();
            $useraction  = new AUserAction;
            $allnum = $useraction->count();
            $ext_name = '';
            if ($data && !empty($data['words'])) {
                $ext_name = (new ExtShowList)->where(['title' => ['like', '%'.$data['words'].'%']])->column('name');
                if(strchr('商城兑换', $data['words'])){
                    $ext_name[] = 'index/Shop';
                }
            }

            $list = $useraction->getListCount(0, $ext_name);
            $list ->append(['extinfo']);
            $page = $list ->render();
            $list = $list->toArray();
            $this->assign("list", $list['data']);
            $this->assign("page", $page);
            $this->assign('query', $data);
            $this->assign('allnum', $allnum);
            return $this->fetch('',['title' => '动态统计列表']);
        }

        /**
         * 图形统计
         */
        public function actionGraph()
        {
            if(request()->IsAjax()){
                $user = new AUserAction;
                $allnum = $user->count();
                
                $list = $user->getListCount(1);
                $list ->append(['extinfo']);
                if($list) {
                    $list = $list->toArray();
                }
                
                $allType = (new ExtShowList)->column("title");
                $allType[] = '商城兑换';

                $extinfo = array_column($list,'extinfo');
                $combine = array_diff($allType,$extinfo);

                return json(['err' =>1, 'data' => ['list' =>$list, 'allnum' =>$allnum, 'combine' =>$combine]]);
            }
            return $this->fetch('graph',['title' => '动态统计柱形图']);
        }

    }
    
?>