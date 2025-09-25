<?php
namespace app\admin\controller;

use app\admin\model\MsgArticle;
use app\admin\model\Log;
use app\admin\model\User as AUser;
use app\admin\model\ExtShowList;
use app\common\model\GameMoneyHistory;
use app\common\model\FlowerHistory;
use app\common\model\MoneyHistory;
use app\admin\model\UserIdbank;
use app\pay\controller\Pay;
use org\MyFunction;
use core\Setting;
use think\Db;

class User extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new AUser();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index($words = '', $starttime = '', $endtime = '', $type = '', $sort = '')
    {
        $param  = $this->param;
        if ($words) {
            $this->baseModel->where('username|nickname|email|tel', 'like', "%{$words}%");
        }
        if ($type != '') {
            $this->baseModel->where('type', $type);
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        $sortList = ['action_time ASC', 'action_time DESC', 'money ASC', 'money DESC'];
        if($sort != '') {
            $this->baseModel->order($sortList[$sort]);
        }
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate(15,false,['query'=>$param]);
        $list->append(['typename', 'agents_name']);
        foreach($list as &$item) {
            $item['reg_city'] = getIPCity($item['reg_ip']);
            $item['last_city'] = getIPCity($item['last_ip']);            
        }
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('index',['title' => '管理员管理']);
    }

    public function agent_list($userid = '', $words = '', $starttime = '', $endtime = '', $sort = '')
    {
        $param  = $this->param;
        if ($words) {
            $this->baseModel->where('username|nickname|email|tel', 'like', "%{$words}%");
        }
        if ($userid) {
            $this->baseModel->where('agents', $userid);
        } else {
            $this->baseModel->where('type', 2)->where('agents', 0);
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        $sortList = ['action_time ASC', 'action_time DESC', 'money ASC', 'money DESC'];
        if($sort != '') {
            $this->baseModel->order($sortList[$sort]);
        }
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate(15,false,['query'=>$param]);
        $list->append(['typename', 'agents_name']);
        foreach($list as &$item) {
            $item['reg_city'] = getIPCity($item['reg_ip']);
            $item['last_city'] = getIPCity($item['last_ip']);
        }
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('agent_index',['title' => '管理员管理']);
    }

    /**
    * 获取 IP  地理位置
    * 淘宝IP接口
    * @Return: array
    */
    public function getCity($ip = '')
    {
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);  
        if(empty($res)){ return false; }  
        $jsonMatches = array();  
        preg_match('#\{.+?\}#', $res, $jsonMatches);  
        if(!isset($jsonMatches[0])){ return false; }  
        $json = json_decode($jsonMatches[0], true);  
        if(isset($json['ret']) && $json['ret'] == 1){  
            $json['ip'] = $ip;  
            unset($json['ret']);  
        }else{  
            return false;  
        }
        return $json['province'] . ' ' . $json['city'];
    }


    /**
     * 添加
     */
    public function add()
    {
        if (request()->isPost()) {
            $res = $this->baseModel->add($this->post);
            if (!$res['code']) {
                $this->error($res['msg'],url("admin/User/add"));
            }
            return $this->success('添加成功',url("admin/User/index"));
        }
        $setting = Setting::get(['rebate_max']);
        $this->assign('setting', $setting);
        return $this->fetch('add',['title' => '管理员添加']);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = $this->id;
        $info = $this->baseModel->get($id);
        if (request()->isPost()) {
            $data = $this->post;
            $res = $this->baseModel->edit($data);
            if (!$res['code']) {
                $this->error($res['msg']);
            }
            return $this->success('修改成功',url("admin/User/index"));
        }
        
        $info->append(['typename', 'agents_name']);
        $id_bank_model = (new UserIdbank);
        $idbank = $id_bank_model->where('userid', $id)->find();
        $bank_openname = '';
        $bank_list = [];
        if ($idbank) {
            $idbank_banks = json_decode($idbank['banks'], true);
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
            }
        }
        $bank_option = $id_bank_model->bankOption();
        $status_option = $this->baseModel->statusOption($info->getData('status'));
        $this->assign('info', $info);
        $this->assign('idbank', $idbank);
        $this->assign('bank_option', $bank_option);
        $this->assign('status_option', $status_option);
        $this->assign('bank_list', $bank_list);
        return $this->fetch('edit', ['title' => '管理员修改']);
    }

    public function base64Upload($base64_data, $userid)
    {
        $path = base64_upload($base64_data,'uploads/personal/', $userid) . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    /**
     * 查看
     */
    public function info($userid = '')
    {
        $user = (new AUser)->find($userid);
        if (!$user) {
            $this->error('用户不存在！');
        }
        $user->append(['userBank']);
        return $this->fetch('info', ['title' => '资料查看', 'info' => $user]);
    }

    /**
     * 删除会员
     */
    public function delete()
    {
        try {
            $id = $this->id;
            if (!$id) {
                return $this->error('参数错误');
            }

            // 权限验证
            if (!$this->admin || !isset($this->admin['id'])) {
                \think\Log::warning('删除会员失败: 权限验证失败', ['session' => session('admin_sid')]);
                return $this->error('权限验证失败，请重新登录');
            }

            $info = $this->baseModel->get($id);
            if (!$info) {
                return $this->error('用户不存在');
            }

            $userinfo = $info->toArray();

            // 防止删除超级管理员
            if (in_array($userinfo['username'], ['xie080886', '1019683427'])) {
                \think\Log::warning('删除会员失败: 尝试删除超级管理员', [
                    'user_id' => $id,
                    'username' => $userinfo['username'],
                    'operator' => $this->admin['username'] ?? 'unknown'
                ]);
                return $this->error('不能删除超级管理员');
            }

            // 开启事务
            Db::startTrans();

            try {
                // 处理代理关系 - 将下级用户转移给上级代理
                $son_user_list = $this->baseModel->where('agents', $id)->column('id');
                if (!empty($son_user_list)) {
                    if ($userinfo['agents']) {
                        // 存在上一级，将下级转移给上级
                        $this->baseModel->whereIn('id', $son_user_list)->update(['agents' => $userinfo['agents']]);
                        \think\Log::info('代理关系转移', [
                            'deleted_agent' => $id,
                            'transferred_to' => $userinfo['agents'],
                            'affected_users' => $son_user_list
                        ]);
                    } else {
                        // 没有上级，设为无代理
                        $this->baseModel->whereIn('id', $son_user_list)->update(['agents' => 0, 'top_agents' => 0]);
                        \think\Log::info('代理关系清除', [
                            'deleted_agent' => $id,
                            'affected_users' => $son_user_list
                        ]);
                    }
                }

                // 清除所有关联数据
                $user_rel_table = $this->baseModel->getRelUserTable();
                foreach ($user_rel_table as $v) {
                    $exist = Db::query('show tables like "kr_' . $v . '"');
                    if (!$exist) continue;

                    $deleted_count = Db::name($v)->where('userid', $id)->delete();
                    if ($deleted_count > 0) {
                        \think\Log::info("清理关联数据: {$v}, 删除记录数: {$deleted_count}");
                    }
                }

                // 删除用户主记录
                $res = $info->delete();
                if (!$res) {
                    throw new \Exception('删除用户主记录失败');
                }

                // 记录操作日志
                \think\Log::info('会员删除成功', [
                    'deleted_user_id' => $id,
                    'deleted_username' => $userinfo['username'],
                    'deleted_nickname' => $userinfo['nickname'] ?? '',
                    'operator' => $this->admin['username'] ?? 'unknown',
                    'operator_id' => $this->admin['id'] ?? 0
                ]);

                // 提交事务
                Db::commit();

                // 返回JSON格式响应（适配AJAX请求）
                if (request()->isAjax()) {
                    return json(['code' => 1, 'msg' => '删除成功']);
                } else {
                    return $this->success('删除成功', url('admin/User/index'));
                }

            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            \think\Log::error('删除会员异常', [
                'user_id' => $id ?? 0,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'operator' => $this->admin['username'] ?? 'unknown'
            ]);

            return $this->error('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 用户充值
     */
    public function recharge($userid, $coin = '', $remark = '', $cz_type = '')
    {
        $user = AUser::get($userid);
        if (!$user) {
            $this->error('用户不存在！');
        }

        if (request()->isPost()) {
            if ($cz_type === '') {
                return $this->error('充值类型不能为空');
            }
            if ($coin == '') {
                $this->error('充值额度输入错误！');
            }

            if($coin != '' and $coin != 0 and $cz_type == 11){
                (new MoneyHistory)->write([
                    'userid' => $userid,
                    'money' => $coin,
                    'type' => $coin < 0 ? 3 : 2,
                    'remark' => $remark ? $remark : ($coin < 0 ? '系统扣除' : '系统充值')
                ]);
            }
            if($coin > 0 and $cz_type != 11){
                $recharge_model = (new \app\common\model\Recharge());
                $out_trade_no = $userid.'Re'.time();
                $typeList = ['微信充值', '支付宝充值','微信扫码充值','支付宝扫码充值', '其他三方充值', '银行卡转账充值'];
                $subject = $typeList[$cz_type].'充值';
                $data = [
                    'userid' => $userid,
                    'order' => $out_trade_no,
                    'money' => $coin,
                    'name' => $subject,
                    'type' => 2,
                    'statuss' => 1,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $res = $recharge_model->insert($data);
                if ($res) {
                    $recharge_model->diamonds($coin, $userid, 2);
                }
            }
            Log::write('给会员ID:' . $userid . ' 充值 ' . $coin);
            return $this->success('操作成功');
        }
        return $this->fetch('recharge', ['title' => '会员充值', 'user' => $user]);
    }
 
    /**
     * 切换会员身份
     */
    public function goto($userid = '')
    {
        $user = AUser::get($userid);
        if (!$user) {
            $this->error('用户不存在！');
        }

        // 设置用户session
        session('sid', $user['sid']);

        // 如果是代理用户，直接跳转到代理后台
        if ($user['type'] == 2) {
            // 设置代理登录session
            session('agentname', $user['username']);
            session('agentId', $user['id']);
            session('agent_info', [
                'id' => $user['id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'],
                'type' => $user['type'],
                'money' => $user['money'],
                'game_money' => $user['game_money']
            ]);

            // 跳转到代理后台
            return $this->redirect('/index/agents/index');
        }

        // 普通用户跳转到前台
        return $this->redirect('/index/index');
    }

    /**
     * 发送消息
     */
    public function send_message($userid = '')
    {
        $id = $userid;
        $info = $this->baseModel->get($id);
        if (request()->isPost()) {
            $data=$this->post;
            $data['send_userid']=$this->admin['userid'];
            $res = (new MsgArticle)->add($this->post);
            if (!$res['code']) {
                $this->error($res['msg'],url("admin/User/edit?id=$id"));
            }
            return $this->success('发送成功',url("admin/User/index"));
        }
        $this->assign('info',$info);
        return $this->fetch('send_message',['title' => '发送消息']);
    }

    public function moneyHistory($words = '', $starttime = '', $endtime = '', $ext = '', $type = '')
    {
        $MoneyHistory = new MoneyHistory;
        $extShowList =new ExtShowList;
        $order = getOrder($this->param);
        $where = [];
        if($type){
            $where['type'] = $type;
        }
        $list = $MoneyHistory->getList($words, $starttime, $endtime, 14, $where, $order);
        $list->append(['extname']);
        $extList = $extShowList->getUseList()->toArray();
        if($ext != ''){
            $extname = $extShowList->where(['name' => ['in', ['/'.$ext, $ext]]])->column('title');
            $this->assign('extname', $extname);
        }
        $type_list = $MoneyHistory->getTypeList();
        if($type){
            $type_txt = $MoneyHistory->getTypeNameAttr('', ['type' => $type]);
        }else{
            $type_txt = '选择资金类型';
        }
        return $this->fetch('money_history',['title' => '资金明细', 'list' => $list, 'query' => request()->get(), 'ext' => $extList, 'type_txt' => $type_txt, 'type_list' => $type_list]);
    }

    /**
     * 资金明细excel导出
     */
    public function moneyExportExcel($words = '', $starttime = '', $endtime = '', $ext = '', $type = '')
    {
        $MoneyHistory = new MoneyHistory;
        $where = [];
        if($type){
            $where['type'] = $type;
        }
        $list = $MoneyHistory->getList($words, $starttime, $endtime, 0, $where);
        $list = $list->append(['extname', 'type_name']);

        $excel_title = ["ID", "用户昵称", "用户名", "来源", "类型", "操作前", "操作后", "金额", "时间", "备注"];//标题
        $excel_filed = ["id", "nickname", "username", "extname", "type_name", "before", "after", "money", "create_time", "remark"];//数据字段
        $excel_width = ["10", "15", "15", "15", "12", "10", "10", "10", "18", "60"];
        MyFunction::comm_exportExcel($excel_title, $excel_filed, $excel_width, $list, "资金明细数据" . date('Ymd', time()));
    }

    public function moneyDelete($id = '', $day = 0)
    {
        $model = new MoneyHistory;
        if($id){
            $res = $model->destroy($id);
        }
        if($day){
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day"));
            $res = $model->where(['create_time' => ['<' , $start]])->delete();
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

    /**查询用户，用于通知消息 */
    public function serachUser()
    {
        if(request()->IsPost()){
            $data = request()->post();
            $user = new AUser;
            if($data['words']){
                $user->where('username', 'like', "%{$data['words']}%");
            }
            $res = $user->field('id,username,nickname')->select()->toarray();
            if($res){
                return json(['err' => 0, 'data' =>$res]);
            }
            return json(['err' => 1, 'msg' => '暂时没有数据']);
        }
    }

    /**修改实名认证身份证和身份证用户名 */
    public function changeIdcard()
    {
        if(request()->isPost()){
            $data = request()->post();
            $data['idnum'] = trim($data['idnum']);
            $idRes = $this->checkIdCard($data['idnum']);
            if(!$idRes){
              return json(['err' => 1, 'msg' => '身份证号码不正确']);
            }
            $res = (new UserIdbank)->changeData($data);
            return $res;
        }
    }   

     /**身份证号码验证 */
   public function checkIdCard($idcard)
   {
       // 只能是18位
       if(mb_strlen($idcard)!=18){
           return false;
       }

       // 取出本体码
       $idcard_base = substr($idcard, 0, 17);

       // 取出校验码
       $verify_code = substr($idcard, 17, 1);

       // 加权因子
       $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

       // 校验码对应值
       $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

       // 根据前17位计算校验码
       $total = 0;
       for($i=0; $i<17; $i++){
           $total += substr($idcard_base, $i, 1)*$factor[$i];
       }

       // 取模
       $mod = $total % 11;

       // 比较校验码
       if($verify_code == $verify_code_list[$mod]){
           return true;
       }else{
           return false;
       }
   }

    /**
     * 批量删除所有会员（保留超级管理员1019683427）
     */
    public function batchDeleteAll()
    {
        // 仅允许超级管理员执行此操作
        if (!$this->admin || !in_array($this->admin['username'], ['1019683427'])) {
            return $this->error('权限不足，仅超级管理员可执行此操作');
        }

        if (request()->isPost()) {
            try {
                $confirmation = $this->post['confirmation'] ?? '';
                if ($confirmation !== 'CONFIRM_DELETE_ALL') {
                    return $this->error('确认码错误，操作已取消');
                }

                // 设置执行时间限制
                set_time_limit(300); // 5分钟

                // 获取要删除的用户列表（排除1019683427）
                $usersToDelete = $this->baseModel->where('username', 'neq', '1019683427')->field('id,username,nickname')->select();
                $deleteCount = count($usersToDelete);

                if ($deleteCount === 0) {
                    return $this->success('没有用户需要删除');
                }

                $successCount = 0;
                $errorCount = 0;

                // 分批删除，每批10个
                $batches = array_chunk($usersToDelete->toArray(), 10);

                foreach ($batches as $batch) {
                    foreach ($batch as $user) {
                        try {
                            // 开启事务
                            Db::startTrans();

                            $userId = $user['id'];
                            $username = $user['username'];

                            // 处理代理关系
                            $sonUsers = $this->baseModel->where('agents', $userId)->column('id');
                            if (!empty($sonUsers)) {
                                $this->baseModel->whereIn('id', $sonUsers)->update(['agents' => 0, 'top_agents' => 0]);
                            }

                            // 清除关联数据
                            $relTables = $this->baseModel->getRelUserTable();
                            foreach ($relTables as $table) {
                                $exist = Db::query('show tables like "kr_' . $table . '"');
                                if (!$exist) continue;

                                try {
                                    Db::name($table)->where('userid', $userId)->delete();
                                } catch (\Exception $e) {
                                    // 忽略不存在userid字段的表
                                }
                            }

                            // 删除用户主记录
                            $userRecord = $this->baseModel->get($userId);
                            if ($userRecord) {
                                $userRecord->delete();
                            }

                            // 提交事务
                            Db::commit();
                            $successCount++;

                            // 记录日志
                            \think\Log::info('批量删除用户', [
                                'deleted_user_id' => $userId,
                                'deleted_username' => $username,
                                'operator' => $this->admin['username']
                            ]);

                        } catch (\Exception $e) {
                            Db::rollback();
                            $errorCount++;

                            \think\Log::error('批量删除用户失败', [
                                'user_id' => $userId ?? 0,
                                'username' => $username ?? 'unknown',
                                'error' => $e->getMessage(),
                                'operator' => $this->admin['username']
                            ]);
                        }
                    }
                }

                // 记录总体操作日志
                \think\Log::info('批量删除所有用户操作完成', [
                    'total_users' => $deleteCount,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'operator' => $this->admin['username'],
                    'operation_time' => date('Y-m-d H:i:s')
                ]);

                return $this->success("批量删除完成！成功删除 {$successCount} 个用户，失败 {$errorCount} 个", url('admin/User/index'));

            } catch (\Exception $e) {
                \think\Log::error('批量删除用户严重错误', [
                    'error' => $e->getMessage(),
                    'operator' => $this->admin['username']
                ]);

                return $this->error('批量删除失败：' . $e->getMessage());
            }
        }

        // 获取统计信息
        $stats = [
            'total_users' => $this->baseModel->count(),
            'total_money' => $this->baseModel->sum('money'),
            'users_with_money' => $this->baseModel->where('money', '>', 0)->count(),
            'type_stats' => [
                '测试会员' => $this->baseModel->where('type', 0)->count(),
                '普通会员' => $this->baseModel->where('type', 1)->count(),
                '代理会员' => $this->baseModel->where('type', 2)->count(),
                '订单会员' => $this->baseModel->where('type', 6)->count(),
                '跟单会员' => $this->baseModel->where('type', 7)->count(),
            ]
        ];

        $this->assign('stats', $stats);
        return $this->fetch('batch_delete_all');
    }

    /**
     * 代理查询
     */
    public function agents_serach()
    {
        $data = $this->post;
        if($data['words'] != ''){
            $this->baseModel->field('id, username')->where('username|nickname', 'like', '%'.$data['words'].'%');
        }
        $all = $this->baseModel->select()->toArray();
        return json(['err' => 0, 'data' =>$all]);
    }

    /**
     * 修改代理
     */
    public function agents_edit()
    {
        if(request()->IsPost()){
            $data = $this->post;
            $user = new AUser;
            if(!$data['agents'] || !$data['userid']) return json(['err' => 1, 'msg' => '参数错误']);
            if ($data['agents'] == $data['userid']) return json(['err' => 1, 'msg' => '代理不能选择自己']);
            $agents_info = $user->where('type', 2)->find($data['agents']);
            if (!$agents_info) return json(['err' => 1, 'msg' => '选择的代理不存在']);
            $res = $user->where('id', $data['userid'])->update(['agents' => $data['agents'], 'top_agents' => $agents_info['top_agents'] ? $agents_info['top_agents'] : $data['agents']]);
            if($res){
                return json(['err' => 0, 'msg' => '保存成功']);
            }
            return json(['err' => 1, 'msg' => '保存失败']);
        }
    }
}
