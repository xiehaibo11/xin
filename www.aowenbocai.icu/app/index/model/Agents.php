<?php
namespace app\index\model;

use think\Model;
use think\Validate;
use app\common\model\ALog;

/**
 * 代理模型类
 */
class Agents extends Model
{
    protected $name = 'user'; // 使用用户表
    protected $updateTime = false;
    protected $createTime = false;

    /**
     * 代理登录验证
     * @param array $data 登录数据
     * @return array
     */
    public function login($data)
    {
        // 数据验证
        $validate = new Validate([
            'username|用户名' => 'require',
            'password|密码' => 'require'
        ]);
        
        if (!$validate->check($data)) {
            return ["code" => 0, "msg" => $validate->getError()];
        }

        // 查找代理用户
        $agent = $this->where([
            'username' => $data['username'],
            'type' => 2  // 代理用户类型
        ])->find();

        if (!$agent) {
            return ["code" => 0, "msg" => "代理账号不存在"];
        }

        // 验证密码（支持多种密码格式）
        $inputPassword = $data['password'];
        $storedPassword = $agent['password'];

        $passwordMatch = false;

        // 1. 直接MD5比较
        if (md5($inputPassword) === $storedPassword) {
            $passwordMatch = true;
        }
        // 2. 如果是32位MD5，可能是截取的
        elseif (strlen($storedPassword) === 32 && $storedPassword === md5($inputPassword)) {
            $passwordMatch = true;
        }
        // 3. 如果是更长的哈希，可能是其他算法
        elseif (strlen($storedPassword) > 32) {
            // 尝试使用PasswordHash验证
            if (class_exists('\PasswordHash')) {
                try {
                    $passwordMatch = \PasswordHash::verifyLegacy($inputPassword, $storedPassword);
                } catch (\Exception $e) {
                    // 如果PasswordHash验证失败，尝试其他方法
                }
            }
        }

        if (!$passwordMatch) {
            return ["code" => 0, "msg" => "代理密码不正确"];
        }



        $agentData = $agent->toArray();

        // 检查代理状态
        if ($agentData['status'] == 1) {
            return ["code" => 0, "msg" => "代理账号已被冻结，请联系管理员"];
        }

        // 设置代理登录session
        session('agentId', $agentData['id']);
        session('agentname', $agentData['username']);
        session('agent_nickname', $agentData['nickname']);

        // 更新最后登录时间和IP
        $this->where('id', $agentData['id'])->update([
            'action_time' => date('Y-m-d H:i:s'),
            'last_ip' => request()->ip()
        ]);
        session('agent_sid', $agentData['sid']);

        // 更新最后登录信息
        $this->save([
            'last_ip' => request()->ip(),
            'action_time' => date('Y-m-d H:i:s')
        ], ['id' => $agentData['id']]);

        // 记录登录日志
        try {
            if (class_exists('\app\common\model\ALog')) {
                (new \app\common\model\ALog)->createLog(1, "代理登录", $agentData['id']);
            }
        } catch (\Exception $e) {
            // 日志记录失败不影响登录
        }

        return ["code" => 1, "msg" => "登录成功", "data" => $agentData];
    }

    /**
     * 获取代理信息
     * @param int $agentId 代理ID
     * @return array|null
     */
    public function getAgentInfo($agentId)
    {
        return $this->where(['id' => $agentId, 'type' => 2])->find();
    }

    /**
     * 获取代理下级用户
     * @param int $agentId 代理ID
     * @return array
     */
    public function getSubUsers($agentId)
    {
        return $this->where('agents', $agentId)->field('id,username,nickname,type,money,create_time')->select();
    }

    /**
     * 获取代理统计信息
     * @param int $agentId 代理ID
     * @return array
     */
    public function getAgentStats($agentId)
    {
        $stats = [
            'total_users' => $this->where('agents', $agentId)->count(),
            'total_money' => $this->where('agents', $agentId)->sum('money'),
            'active_users' => $this->where('agents', $agentId)->where('action_time', '>', date('Y-m-d', strtotime('-7 days')))->count()
        ];

        return $stats;
    }

    /**
     * 检查代理权限
     * @param int $agentId 代理ID
     * @param int $userId 用户ID
     * @return bool
     */
    public function checkAgentPermission($agentId, $userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        // 检查是否是直接下级
        if ($user['agents'] == $agentId) {
            return true;
        }

        // 检查是否是间接下级（递归检查）
        return $this->checkAgentPermissionRecursive($agentId, $user['agents']);
    }

    /**
     * 递归检查代理权限
     * @param int $targetAgentId 目标代理ID
     * @param int $currentAgentId 当前代理ID
     * @return bool
     */
    private function checkAgentPermissionRecursive($targetAgentId, $currentAgentId)
    {
        if (!$currentAgentId || $currentAgentId == $targetAgentId) {
            return $currentAgentId == $targetAgentId;
        }

        $agent = $this->find($currentAgentId);
        if (!$agent || $agent['type'] != 2) {
            return false;
        }

        return $this->checkAgentPermissionRecursive($targetAgentId, $agent['agents']);
    }

    /**
     * 代理退出登录
     */
    public function logout()
    {
        session('agentId', null);
        session('agentname', null);
        session('agent_nickname', null);
        session('agent_sid', null);
    }

    /**
     * 验证代理登录状态
     * @return bool
     */
    public function checkLogin()
    {
        return session('agentId') && session('agentname');
    }

    /**
     * 获取当前登录代理信息
     * @return array|null
     */
    public function getCurrentAgent()
    {
        $agentId = session('agentId');
        if (!$agentId) {
            return null;
        }

        return $this->getAgentInfo($agentId);
    }
}
?>
