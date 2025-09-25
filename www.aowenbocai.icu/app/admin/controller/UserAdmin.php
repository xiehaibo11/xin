<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\User as Usermodel;

class UserAdmin extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new Admin();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }
    public function index()
    {
        $list = $this->baseModel->paginate(13,false);
        $this->assign("list",$list);
        return $this->fetch('index',['title' => '管理员管理']);
    }

    /**
     * 添加
     */
    public function add()
    {
        if (request()->isPost()) {
            $res = $this->baseModel->add($this->post);
            if (!$res['code']) {
                $this->error($res['msg'],url("admin/UserAdmin/add"));
            }
            return $this->success('添加成功',url("admin/UserAdmin/index"));
        }
        return $this->fetch('add',['title' => '管理员添加']);
    }

    /**
     * 删除管理员
     */
    public function delete()
    {
        // 记录删除请求日志
        \think\Log::info('管理员删除请求', [
            'request_id' => $this->id,
            'admin_user' => $this->admin['username'] ?? 'unknown',
            'request_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);

        $id = $this->id;
        if (!$id) {
            \think\Log::warning('删除管理员失败: 参数错误', ['id' => $id]);
            return $this->error('参数错误：管理员ID不能为空');
        }

        try {
            // 检查当前管理员权限
            if (!$this->admin || !isset($this->admin['id'])) {
                \think\Log::warning('删除管理员失败: 权限验证失败', ['session' => session('admin_sid')]);
                return $this->error('权限验证失败，请重新登录');
            }

            // 获取管理员信息
            $admin = $this->baseModel->get($id);
            if (!$admin) {
                \think\Log::warning('删除管理员失败: 管理员不存在', ['id' => $id]);
                return $this->error('管理员不存在');
            }

            // 获取关联的用户信息
            $user = $admin->user;
            if (!$user) {
                \think\Log::warning('删除管理员失败: 关联用户不存在', [
                    'admin_id' => $id,
                    'userid' => $admin->userid
                ]);
                return $this->error('关联用户不存在');
            }

            // 防止删除超级管理员
            if (in_array($user->username, ['xie080886', '1019683427'])) {
                \think\Log::warning('删除管理员失败: 尝试删除超级管理员', [
                    'admin_id' => $id,
                    'username' => $user->username,
                    'operator' => $this->admin['username']
                ]);
                return $this->error('不能删除超级管理员');
            }

            // 防止管理员删除自己
            if ($admin->userid == $this->admin['id']) {
                \think\Log::warning('删除管理员失败: 尝试删除自己', [
                    'admin_id' => $id,
                    'operator_id' => $this->admin['id']
                ]);
                return $this->error('不能删除自己的管理员账号');
            }

            // 执行删除操作
            $res = $admin->delete();
            if ($res) {
                \think\Log::info('管理员删除成功', [
                    'deleted_admin_id' => $id,
                    'deleted_username' => $user->username,
                    'operator' => $this->admin['username'],
                    'operator_id' => $this->admin['id']
                ]);

                // 返回JSON格式响应（适配AJAX请求）
                if (request()->isAjax()) {
                    return json(['code' => 1, 'msg' => '删除成功']);
                } else {
                    return $this->success('删除成功', url('admin/UserAdmin/index'));
                }
            } else {
                \think\Log::error('删除管理员失败: 数据库删除操作失败', [
                    'admin_id' => $id,
                    'username' => $user->username
                ]);
                return $this->error('删除失败：数据库操作失败');
            }

        } catch (\Exception $e) {
            // 记录详细错误日志
            \think\Log::error('删除管理员异常', [
                'admin_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'operator' => $this->admin['username'] ?? 'unknown'
            ]);

            return $this->error('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 编辑管理员
     */
    public function edit()
    {
        $id = $this->id;
        if (!$id) {
            return $this->error('参数错误');
        }

        $admin = $this->baseModel->get($id);
        if (!$admin) {
            return $this->error('管理员不存在');
        }

        if (request()->isPost()) {
            $data = $this->post;
            // 这里可以添加编辑逻辑
            return $this->success('编辑成功', url('admin/UserAdmin/index'));
        }

        $this->assign('admin', $admin);
        return $this->fetch('edit', ['title' => '编辑管理员']);
    }

}
