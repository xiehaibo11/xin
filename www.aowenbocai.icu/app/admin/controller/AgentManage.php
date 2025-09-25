<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;
use app\admin\model\User as AUser;
use core\Setting;

/**
 * 代理管理控制器
 * 提供完整的代理系统管理功能
 */
class AgentManage extends Base
{
    /**
     * 代理管理首页
     */
    public function index()
    {
        $param = $this->param;
        $words = isset($param['words']) ? $param['words'] : '';
        $starttime = isset($param['starttime']) ? $param['starttime'] : '';
        $endtime = isset($param['endtime']) ? $param['endtime'] : '';
        $level = isset($param['level']) ? $param['level'] : '';
        
        $userModel = new AUser();
        
        // 基础查询条件：只查询代理用户
        $userModel->where('type', 2);
        
        // 搜索条件
        if ($words) {
            $userModel->where('username|nickname|email|tel', 'like', "%{$words}%");
        }
        
        // 时间筛选
        if ($starttime) {
            $userModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $userModel->where('create_time', '<=', $endtime . ' 23:59:59');
        }
        
        // 代理等级筛选
        if ($level !== '') {
            if ($level == 0) {
                // 一级代理（没有上级）
                $userModel->where('agents', 0);
            } else {
                // 有上级的代理
                $userModel->where('agents', '>', 0);
            }
        }
        
        $list = $userModel->order('id desc')->paginate(15, false, ['query' => $param]);
        $list->append(['typename', 'agents_name']);
        
        // 统计数据
        $stats = $this->getAgentStats();
        
        $this->assign("list", $list);
        $this->assign("query", $param);
        $this->assign("stats", $stats);
        
        return $this->fetch('index', ['title' => '代理管理']);
    }
    
    /**
     * 创建代理
     */
    public function create()
    {
        if (request()->isPost()) {
            $data = $this->post;
            
            // 验证必填字段
            if (empty($data['username']) || empty($data['password'])) {
                return json(['err' => 1, 'msg' => '用户名和密码不能为空']);
            }
            
            // 检查用户名是否已存在
            $userModel = new AUser();
            $existUser = $userModel->where('username', $data['username'])->find();
            if ($existUser) {
                return json(['err' => 1, 'msg' => '用户名已存在']);
            }
            
            // 准备数据
            $userData = [
                'username' => $data['username'],
                'password' => md5($data['password']),
                'nickname' => isset($data['nickname']) ? $data['nickname'] : $data['username'],
                'type' => 2, // 代理类型
                'status' => 1,
                'money' => isset($data['money']) ? floatval($data['money']) : 0,
                'game_money' => isset($data['game_money']) ? floatval($data['game_money']) : 0,
                'agents' => isset($data['agents']) ? intval($data['agents']) : 0,
                'create_time' => date('Y-m-d H:i:s'),
                'reg_ip' => request()->ip(),
                'sid' => md5(uniqid() . time())
            ];
            
            // 如果有上级代理，设置top_agents
            if ($userData['agents'] > 0) {
                $parentAgent = $userModel->find($userData['agents']);
                if ($parentAgent && $parentAgent['type'] == 2) {
                    $userData['top_agents'] = $parentAgent['top_agents'] ? $parentAgent['top_agents'] : $parentAgent['id'];
                } else {
                    return json(['err' => 1, 'msg' => '选择的上级代理不存在']);
                }
            }
            
            $result = $userModel->save($userData);
            
            if ($result) {
                return json(['err' => 0, 'msg' => '代理创建成功']);
            } else {
                return json(['err' => 1, 'msg' => '代理创建失败']);
            }
        }
        
        // 获取可选的上级代理列表
        $parentAgents = (new AUser())->where('type', 2)->field('id,username,nickname')->select();
        $this->assign('parentAgents', $parentAgents);
        
        return $this->fetch('create', ['title' => '创建代理']);
    }
    
    /**
     * 代理接管 - 直接登录代理后台
     */
    public function takeover($agentId)
    {
        $userModel = new AUser();
        $agent = $userModel->find($agentId);
        
        if (!$agent) {
            return json(['err' => 1, 'msg' => '代理不存在']);
        }
        
        if ($agent['type'] != 2) {
            return json(['err' => 1, 'msg' => '该用户不是代理']);
        }
        
        // 设置代理登录session
        session('agentname', $agent['username']);
        session('agentId', $agent['id']);
        session('agent_info', [
            'id' => $agent['id'],
            'username' => $agent['username'],
            'nickname' => $agent['nickname'],
            'type' => $agent['type'],
            'money' => $agent['money'],
            'game_money' => $agent['game_money']
        ]);
        
        // 同时设置用户session以保持兼容性
        session('sid', $agent['sid']);
        
        return json(['err' => 0, 'msg' => '接管成功', 'redirect' => '/index/agents/index']);
    }
    
    /**
     * 代理层级结构
     */
    public function hierarchy($parentId = 0)
    {
        $userModel = new AUser();
        
        if ($parentId == 0) {
            // 显示一级代理
            $agents = $userModel->where('type', 2)->where('agents', 0)->select();
        } else {
            // 显示指定代理的下级
            $agents = $userModel->where('agents', $parentId)->select();
        }
        
        $this->assign('agents', $agents);
        $this->assign('parentId', $parentId);
        
        return $this->fetch('hierarchy', ['title' => '代理层级']);
    }
    
    /**
     * 获取代理统计数据
     */
    private function getAgentStats()
    {
        $userModel = new AUser();
        
        return [
            'total_agents' => $userModel->where('type', 2)->count(),
            'level1_agents' => $userModel->where('type', 2)->where('agents', 0)->count(),
            'active_agents' => $userModel->where('type', 2)->where('action_time', '>', date('Y-m-d H:i:s', strtotime('-7 days')))->count(),
            'today_new' => $userModel->where('type', 2)->where('create_time', '>=', date('Y-m-d'))->count()
        ];
    }
    
    /**
     * 代理数据统计
     */
    public function statistics()
    {
        // 代理业绩统计等功能
        return $this->fetch('statistics', ['title' => '代理统计']);
    }
    
    /**
     * 批量操作
     */
    public function batchAction()
    {
        if (request()->isPost()) {
            $data = $this->post;
            $action = $data['action'];
            $ids = explode(',', $data['ids']);

            $userModel = new AUser();

            switch ($action) {
                case 'freeze':
                    $result = $userModel->whereIn('id', $ids)->update(['status' => 0]);
                    break;
                case 'unfreeze':
                    $result = $userModel->whereIn('id', $ids)->update(['status' => 1]);
                    break;
                default:
                    return json(['err' => 1, 'msg' => '无效操作']);
            }

            if ($result) {
                return json(['err' => 0, 'msg' => '操作成功']);
            } else {
                return json(['err' => 1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 获取下级代理数量
     */
    public function getChildrenCount()
    {
        $parentId = input('parentId', 0);
        $userModel = new AUser();

        $count = $userModel->where('agents', $parentId)->where('type', 2)->count();

        return json(['err' => 0, 'count' => $count]);
    }

    /**
     * 获取下级代理列表
     */
    public function getChildren()
    {
        $parentId = input('parentId', 0);
        $userModel = new AUser();

        $children = $userModel->where('agents', $parentId)
                             ->where('type', 2)
                             ->field('id,username,nickname,money,game_money,create_time,photo')
                             ->select();

        return json(['err' => 0, 'data' => $children]);
    }

    /**
     * 获取代理详情
     */
    public function getAgentDetail()
    {
        $agentId = input('agentId', 0);
        $userModel = new AUser();

        $agent = $userModel->find($agentId);
        if (!$agent) {
            return json(['err' => 1, 'msg' => '代理不存在']);
        }

        // 获取下级统计
        $childrenCount = $userModel->where('agents', $agentId)->count();
        $directChildren = $userModel->where('agents', $agentId)->where('type', 2)->count();

        // 构建详情HTML
        $html = '
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="' . ($agent['photo'] ?: '/static/admin/images/default_avatar.png') . '"
                     class="img-circle" width="100" height="100">
                <h4>' . $agent['username'] . '</h4>
                <p class="text-muted">' . ($agent['nickname'] ?: '暂无昵称') . '</p>
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><td>用户ID</td><td>' . $agent['id'] . '</td></tr>
                    <tr><td>用户名</td><td>' . $agent['username'] . '</td></tr>
                    <tr><td>昵称</td><td>' . ($agent['nickname'] ?: '暂无') . '</td></tr>
                    <tr><td>余额</td><td>¥' . $agent['money'] . '</td></tr>
                    <tr><td>游戏币</td><td>' . $agent['game_money'] . '</td></tr>
                    <tr><td>注册时间</td><td>' . $agent['create_time'] . '</td></tr>
                    <tr><td>最后活动</td><td>' . $agent['action_time'] . '</td></tr>
                    <tr><td>直接下级</td><td>' . $directChildren . ' 个代理</td></tr>
                    <tr><td>总下级</td><td>' . $childrenCount . ' 个用户</td></tr>
                    <tr><td>状态</td><td>' . ($agent['status'] ? '<span class="label label-success">正常</span>' : '<span class="label label-danger">冻结</span>') . '</td></tr>
                </table>
            </div>
        </div>';

        return json(['err' => 0, 'html' => $html]);
    }
}
