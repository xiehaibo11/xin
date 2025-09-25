<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Agents as AgentsModel;
use app\index\model\User;
use app\index\service\AgentLevelService;
use app\index\service\AgentBrokerageService;
use app\index\service\AgentQuotaService;

/**
 * 代理控制器
 */
class Agents extends Controller
{
    protected $agentsModel;
    protected $userModel;
    protected $agent;
    protected $levelService;
    protected $brokerageService;
    protected $quotaService;

    public function __construct()
    {
        parent::__construct();
        $this->agentsModel = new AgentsModel();
        $this->userModel = new User();
        $this->levelService = new AgentLevelService();
        $this->brokerageService = new AgentBrokerageService();
        $this->quotaService = new AgentQuotaService();

        // 检查代理登录状态
        if (!$this->agentsModel->checkLogin()) {
            $this->redirect('index/Login/agent');
        }

        // 获取当前代理信息
        $this->agent = $this->agentsModel->getCurrentAgent();
        if (!$this->agent) {
            $this->redirect('index/Login/agent');
        }

        $this->assign('agent', $this->agent);
    }

    /**
     * 代理后台首页
     */
    public function index()
    {
        // 获取代理统计信息（使用新的服务）
        $stats = [
            'total_brokerage' => $this->brokerageService->getTotalBrokerage($this->agent['id']),
            'sub_agents' => $this->userModel->where('agents', $this->agent['id'])->where('type', 2)->count(),
            'total_quota' => $this->quotaService->getTotalQuota($this->agent['id']),
            'spread_register' => $this->userModel->where('spread_uid', $this->agent['id'])->count(),
            'month_performance' => $this->getMonthPerformance($this->agent['id'])
        ];

        // 获取最近的下级用户
        $recentUsers = $this->userModel->where('agents', $this->agent['id'])
            ->field('id,username,nickname,type,money,create_time')
            ->order('create_time desc')
            ->limit(10)
            ->select();

        $this->assign([
            'agent' => $this->agent,
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'title' => '代理后台'
        ]);

        return $this->fetch('index');
    }

    /**
     * 控制台主页面（iframe内容）
     */
    public function main()
    {
        // 获取代理统计信息
        $stats = [
            'sub_users' => $this->userModel->where('agents', $this->agent['id'])->count(),
            'today_brokerage' => $this->brokerageService->getTodayBrokerage($this->agent['id']),
            'total_brokerage' => $this->brokerageService->getTotalBrokerage($this->agent['id']),
            'team_count' => $this->userModel->where('agents', 'like', '%,' . $this->agent['id'] . ',%')->count(),
            'active_users' => $this->userModel->where('agents', $this->agent['id'])->where('status', 1)->count()
        ];

        // 获取最近用户
        $recentUsers = $this->userModel->where('agents', $this->agent['id'])
            ->field('id,username,nickname,create_time,status')
            ->order('create_time desc')
            ->limit(5)
            ->select();

        // 获取最近佣金记录
        $recentBrokerage = [];

        $this->assign([
            'agent' => $this->agent,
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_brokerage' => $recentBrokerage,
            'title' => '控制台概览'
        ]);

        return $this->fetch('main');
    }

    /**
     * 用户列表页面（iframe内容）
     */
    public function userList()
    {
        $this->assign('title', '下级用户管理');
        return $this->fetch('user_list');
    }

    /**
     * 下级用户管理
     */
    public function users()
    {
        $page = input('page', 1);
        $limit = 20;
        
        // 获取下级用户列表
        $users = $this->userModel->where('agents', $this->agent['id'])
            ->field('id,username,nickname,type,money,status,create_time,action_time')
            ->order('create_time desc')
            ->paginate($limit);

        $this->assign([
            'users' => $users,
            'title' => '下级用户管理'
        ]);

        return $this->fetch('users');
    }

    /**
     * 用户详情
     */
    public function userDetail()
    {
        $userId = input('id', 0);
        
        // 验证权限
        if (!$this->agentsModel->checkAgentPermission($this->agent['id'], $userId)) {
            return $this->error('无权限查看此用户信息');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $this->assign([
            'user' => $user,
            'title' => '用户详情'
        ]);

        return $this->fetch('user_detail');
    }

    /**
     * 代理报表
     */
    public function report()
    {
        $startDate = input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = input('end_date', date('Y-m-d'));

        // 获取报表数据
        $reportData = $this->getReportData($startDate, $endDate);

        $this->assign([
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'title' => '代理报表'
        ]);

        return $this->fetch('report');
    }

    /**
     * 获取报表数据
     */
    private function getReportData($startDate, $endDate)
    {
        // 这里可以根据实际需求实现报表逻辑
        return [
            'total_bet' => 0,
            'total_win' => 0,
            'total_commission' => 0,
            'user_count' => $this->userModel->where('agents', $this->agent['id'])->count()
        ];
    }

    /**
     * 代理设置
     */
    public function settings()
    {
        if (request()->isPost()) {
            $data = input('post.');
            
            // 更新代理信息
            $updateData = [];
            if (isset($data['nickname']) && $data['nickname']) {
                $updateData['nickname'] = $data['nickname'];
            }
            
            if (isset($data['old_password']) && $data['new_password']) {
                // 验证旧密码
                if (md5($data['old_password']) !== $this->agent['password']) {
                    return $this->error('原密码不正确');
                }
                $updateData['password'] = md5($data['new_password']);
            }

            if (!empty($updateData)) {
                $this->userModel->save($updateData, ['id' => $this->agent['id']]);
                return $this->success('设置更新成功');
            }
        }

        $this->assign('title', '代理设置');
        return $this->fetch('settings');
    }

    /**
     * 代理退出登录
     */
    public function logout()
    {
        $this->agentsModel->logout();
        $this->redirect('index/Login/agent');
    }

    /**
     * AJAX获取用户列表
     */
    public function getUserList()
    {
        $page = input('page', 1);
        $limit = input('limit', 20);
        $search = input('search', '');

        $query = $this->userModel->where('agents', $this->agent['id']);
        
        if ($search) {
            $query->where('username|nickname', 'like', '%' . $search . '%');
        }

        $users = $query->field('id,username,nickname,type,money,status,create_time')
            ->order('create_time desc')
            ->paginate($limit);

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $users->items(),
            'count' => $users->total()
        ]);
    }

    /**
     * AJAX获取统计数据
     */
    public function getStats()
    {
        $stats = $this->agentsModel->getAgentStats($this->agent['id']);

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * 开通代理功能
     */
    public function createAgent()
    {
        if (request()->isPost()) {
            $data = input('post.');

            // 验证数据
            $validate = validate([
                'username|用户名' => 'require|length:4,20|alphaNum',
                'password|密码' => 'require|length:6,20',
                'nickname|昵称' => 'require|length:2,20',
                'phone|手机号' => 'require|mobile',
                'agent_level|代理等级' => 'require|number'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            // 检查用户名是否存在
            $existUser = $this->userModel->where('username', $data['username'])->find();
            if ($existUser) {
                return json(['code' => 0, 'msg' => '用户名已存在']);
            }

            // 检查手机号是否存在
            $existPhone = $this->userModel->where('phone', $data['phone'])->find();
            if ($existPhone) {
                return json(['code' => 0, 'msg' => '手机号已存在']);
            }

            // 创建代理账号
            $agentData = [
                'username' => $data['username'],
                'password' => md5($data['password']),
                'nickname' => $data['nickname'],
                'phone' => $data['phone'],
                'type' => 2, // 代理类型
                'agents' => $this->agent['id'], // 上级代理
                'agent_level' => $data['agent_level'],
                'is_agent' => 1,
                'spread_uid' => $this->agent['id'],
                'spread_time' => time(),
                'create_time' => date('Y-m-d H:i:s'),
                'status' => 0 // 正常状态
            ];

            $result = $this->userModel->save($agentData);

            if ($result) {
                // 生成推广链接
                $this->generateSpreadLink($this->userModel->id);

                return json(['code' => 1, 'msg' => '代理开通成功']);
            } else {
                return json(['code' => 0, 'msg' => '代理开通失败']);
            }
        }

        // 获取代理等级列表
        $levels = $this->levelService->getLevelList(['status' => 1]);

        $this->assign([
            'levels' => $levels,
            'title' => '开通代理'
        ]);

        return $this->fetch('create_agent');
    }

    /**
     * 检查用户名是否存在
     */
    public function checkUsername()
    {
        if (request()->isPost()) {
            $username = input('post.username', '');

            if (empty($username)) {
                return json(['code' => 0, 'msg' => '用户名不能为空']);
            }

            // 检查用户名是否存在
            $existUser = $this->userModel->where('username', $username)->find();
            if ($existUser) {
                return json(['code' => 0, 'msg' => '用户名已存在']);
            }

            return json(['code' => 1, 'msg' => '用户名可用']);
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 代理等级管理
     */
    public function agentLevels()
    {
        $levels = $this->levelService->getLevelList();

        $this->assign([
            'levels' => $levels,
            'title' => '代理等级管理'
        ]);

        return $this->fetch('agent_levels');
    }

    /**
     * 佣金管理
     */
    public function brokerage()
    {
        $page = input('page', 1);
        $limit = input('limit', 20);
        $where = [
            'type' => input('type', ''),
            'status' => input('status', ''),
            'start_time' => input('start_time', ''),
            'end_time' => input('end_time', '')
        ];

        $brokerageData = $this->brokerageService->getBrokerageList($this->agent['id'], $where, $page, $limit);
        $brokerageStats = $this->brokerageService->getBrokerageStats($this->agent['id']);

        $this->assign([
            'brokerageData' => $brokerageData,
            'brokerageStats' => $brokerageStats,
            'title' => '佣金管理'
        ]);

        return $this->fetch('brokerage');
    }

    /**
     * 下级代理额度管理
     */
    public function quotaManage()
    {
        $page = input('page', 1);
        $limit = input('limit', 20);
        $where = [
            'keyword' => input('keyword', ''),
            'status' => input('status', '')
        ];

        $quotaData = $this->quotaService->getSubAgentQuotaList($this->agent['id'], $where, $page, $limit);
        $quotaStats = $this->quotaService->getQuotaStats($this->agent['id']);

        $this->assign([
            'quotaData' => $quotaData,
            'quotaStats' => $quotaStats,
            'title' => '额度管理'
        ]);

        return $this->fetch('quota_manage');
    }

    /**
     * 设置下级代理额度
     */
    public function setQuota()
    {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate([
                'sub_agent_id|下级代理' => 'require|number',
                'quota_amount|额度金额' => 'require|float|egt:0',
                'remark|备注' => 'max:200'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            $result = $this->quotaService->setAgentQuota(
                $this->agent['id'],
                $data['sub_agent_id'],
                $data['quota_amount'],
                $data['remark'] ?? ''
            );

            if ($result) {
                return json(['code' => 1, 'msg' => '额度设置成功']);
            } else {
                return json(['code' => 0, 'msg' => '额度设置失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 调整下级代理额度
     */
    public function adjustQuota()
    {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate([
                'sub_agent_id|下级代理' => 'require|number',
                'amount|调整金额' => 'require|float',
                'remark|备注' => 'max:200'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            $result = $this->quotaService->adjustAgentQuota(
                $this->agent['id'],
                $data['sub_agent_id'],
                $data['amount'],
                $data['remark'] ?? ''
            );

            if ($result) {
                return json(['code' => 1, 'msg' => '额度调整成功']);
            } else {
                return json(['code' => 0, 'msg' => '额度调整失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 冻结/解冻代理额度
     */
    public function changeQuotaStatus()
    {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate([
                'sub_agent_id|下级代理' => 'require|number',
                'status|状态' => 'require|in:0,1',
                'remark|备注' => 'max:200'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            $result = $this->quotaService->changeQuotaStatus(
                $this->agent['id'],
                $data['sub_agent_id'],
                $data['status'],
                $data['remark'] ?? ''
            );

            if ($result) {
                $statusText = $data['status'] ? '解冻' : '冻结';
                return json(['code' => 1, 'msg' => "额度{$statusText}成功"]);
            } else {
                return json(['code' => 0, 'msg' => '操作失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 获取额度变更日志
     */
    public function getQuotaLogs()
    {
        $page = input('page', 1);
        $limit = input('limit', 20);
        $subAgentId = input('sub_agent_id', 0);

        $logs = $this->quotaService->getQuotaLogs($this->agent['id'], $subAgentId, $page, $limit);

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $logs['list'],
            'count' => $logs['total']
        ]);
    }

    /**
     * 团队管理
     */
    public function teamManage()
    {
        // 获取团队结构数据
        $teamData = $this->getTeamStructure($this->agent['id']);

        $this->assign([
            'teamData' => $teamData,
            'title' => '团队管理'
        ]);

        return $this->fetch('team_manage');
    }

    /**
     * 获取团队结构
     * @param int $agentId 代理ID
     * @param int $level 层级
     * @return array
     */
    private function getTeamStructure($agentId, $level = 1)
    {
        if ($level > 3) return []; // 最多显示3级

        $subUsers = $this->userModel->where('agents', $agentId)
            ->field('id,username,nickname,type,money,create_time,action_time')
            ->select();

        $result = [];
        foreach ($subUsers as $user) {
            $userData = $user->toArray();
            $userData['level'] = $level;
            $userData['is_agent'] = $user['type'] == 2;

            if ($userData['is_agent']) {
                $userData['sub_users'] = $this->getTeamStructure($user['id'], $level + 1);
            }

            $result[] = $userData;
        }

        return $result;
    }

    /**
     * 生成推广链接
     * @param int $agentId 代理ID
     * @return string
     */
    private function generateSpreadLink($agentId)
    {
        $linkCode = md5($agentId . time() . rand(1000, 9999));
        $linkCode = substr($linkCode, 0, 8);

        $linkData = [
            'agent_id' => $agentId,
            'link_code' => $linkCode,
            'link_url' => request()->domain() . '/register?spread=' . $linkCode,
            'add_time' => time(),
            'update_time' => time()
        ];

        \think\Db::name('agent_spread_link')->insert($linkData);

        return $linkData['link_url'];
    }

    /**
     * 推广链接管理
     */
    public function spreadLinks()
    {
        $links = \think\Db::name('agent_spread_link')
            ->where('agent_id', $this->agent['id'])
            ->order('add_time desc')
            ->select();

        foreach ($links as &$link) {
            $link['add_time_text'] = date('Y-m-d H:i:s', $link['add_time']);
            $link['status_text'] = $link['status'] ? '启用' : '禁用';
        }

        $this->assign([
            'links' => $links,
            'title' => '推广链接管理'
        ]);

        return $this->fetch('spread_links');
    }

    /**
     * 生成新的推广链接（AJAX接口）
     */
    public function createSpreadLink()
    {
        if (request()->isPost()) {
            $linkUrl = $this->generateSpreadLink($this->agent['id']);

            if ($linkUrl) {
                return json(['code' => 1, 'msg' => '推广链接生成成功', 'data' => $linkUrl]);
            } else {
                return json(['code' => 0, 'msg' => '推广链接生成失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 切换推广链接状态
     */
    public function toggleLinkStatus()
    {
        if (request()->isPost()) {
            $linkId = input('link_id', 0);
            $status = input('status', 1);

            $result = \think\Db::name('agent_spread_link')
                ->where('id', $linkId)
                ->where('agent_id', $this->agent['id'])
                ->update([
                    'status' => $status,
                    'update_time' => time()
                ]);

            if ($result) {
                $statusText = $status ? '启用' : '禁用';
                return json(['code' => 1, 'msg' => "链接{$statusText}成功"]);
            } else {
                return json(['code' => 0, 'msg' => '操作失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 删除推广链接
     */
    public function deleteLink()
    {
        if (request()->isPost()) {
            $linkId = input('link_id', 0);

            $result = \think\Db::name('agent_spread_link')
                ->where('id', $linkId)
                ->where('agent_id', $this->agent['id'])
                ->delete();

            if ($result) {
                return json(['code' => 1, 'msg' => '链接删除成功']);
            } else {
                return json(['code' => 0, 'msg' => '删除失败']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 获取下级代理列表（AJAX）
     */
    public function getSubAgents()
    {
        $subAgents = $this->userModel->where('agents', $this->agent['id'])
            ->where('type', 2)
            ->field('id,username,nickname')
            ->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $subAgents
        ]);
    }

    /**
     * 获取代理统计数据（AJAX）
     */
    public function getAgentStats()
    {
        $stats = [
            'total_brokerage' => $this->brokerageService->getTotalBrokerage($this->agent['id']),
            'sub_agents' => $this->userModel->where('agents', $this->agent['id'])->where('type', 2)->count(),
            'total_quota' => $this->quotaService->getTotalQuota($this->agent['id']),
            'spread_register' => $this->userModel->where('spread_uid', $this->agent['id'])->count(),
            'month_performance' => $this->getMonthPerformance($this->agent['id'])
        ];

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * 获取月度业绩
     * @param int $agentId 代理ID
     * @return float
     */
    private function getMonthPerformance($agentId)
    {
        $startTime = date('Y-m-01 00:00:00');
        $endTime = date('Y-m-t 23:59:59');

        // 这里需要根据实际业务逻辑计算月度业绩
        // 可能包括下级用户的充值、投注等数据
        return 0.00;
    }

    /**
     * 编辑用户信息
     */
    public function editUser()
    {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate([
                'user_id|用户ID' => 'require|number',
                'nickname|昵称' => 'require|length:2,20',
                'phone|手机号' => 'mobile',
                'agent_level|代理等级' => 'number|in:1,2,3',
                'remark|备注' => 'max:200'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            // 检查用户是否存在且属于当前代理
            $user = $this->userModel->where('id', $data['user_id'])
                ->where('agents', $this->agent['id'])
                ->find();

            if (!$user) {
                return json(['code' => 0, 'msg' => '用户不存在或无权限操作']);
            }

            // 更新用户信息
            $updateData = [
                'nickname' => $data['nickname'],
                'update_time' => time()
            ];

            if (!empty($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }

            if (!empty($data['agent_level'])) {
                $updateData['agent_level'] = $data['agent_level'];
            }

            if (!empty($data['remark'])) {
                $updateData['remark'] = $data['remark'];
            }

            $result = $this->userModel->where('id', $data['user_id'])->update($updateData);

            if ($result !== false) {
                // 记录操作日志
                $this->logOperation('edit_user', $data['user_id'], '编辑用户信息', $data);
                return json(['code' => 1, 'msg' => '用户信息修改成功']);
            } else {
                return json(['code' => 0, 'msg' => '修改失败，请重试']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 修改用户状态（冻结/解冻）
     */
    public function changeUserStatus()
    {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate([
                'user_id|用户ID' => 'require|number',
                'status|状态' => 'require|in:0,1',
                'remark|备注' => 'max:200'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }

            // 检查用户是否存在且属于当前代理
            $user = $this->userModel->where('id', $data['user_id'])
                ->where('agents', $this->agent['id'])
                ->find();

            if (!$user) {
                return json(['code' => 0, 'msg' => '用户不存在或无权限操作']);
            }

            // 更新用户状态
            $result = $this->userModel->where('id', $data['user_id'])->update([
                'status' => $data['status'],
                'update_time' => time()
            ]);

            if ($result !== false) {
                $statusText = $data['status'] ? '解冻' : '冻结';
                // 记录操作日志
                $this->logOperation('change_status', $data['user_id'], $statusText . '用户', $data);
                return json(['code' => 1, 'msg' => "用户{$statusText}成功"]);
            } else {
                return json(['code' => 0, 'msg' => '操作失败，请重试']);
            }
        }

        return json(['code' => 0, 'msg' => '请求方式错误']);
    }

    /**
     * 记录操作日志
     */
    private function logOperation($action, $targetId, $description, $data = [])
    {
        try {
            // 这里可以记录到专门的操作日志表
            // 暂时使用简单的日志记录
            $logData = [
                'agent_id' => $this->agent['id'],
                'action' => $action,
                'target_id' => $targetId,
                'description' => $description,
                'data' => json_encode($data),
                'ip' => request()->ip(),
                'create_time' => time()
            ];

            // 如果有操作日志表，可以在这里插入记录
            // Db::name('agent_operation_log')->insert($logData);

        } catch (\Exception $e) {
            // 日志记录失败不影响主要功能
        }
    }

}
?>
