<?php
namespace app\admin\controller;

use app\admin\model\ExtShowList;
use app\admin\model\Ext;

class PageUi extends Base
{
    public function index($type = '')
    {	
    	$extshowlist = new ExtShowList;
    	$ext = new Ext;
    	$showList = $extshowlist->getList(['type' => $type])->toArray();
		$extList = $ext->getList()->toArray();
		if(count($showList) == 0)  $this->assign('_empty',0);
    	$this->assign('showList',$showList);
    	$this->assign('extList',$extList);
    	$title = '游戏';
    	if ($type == 1) $title = '彩票';
        $this->assign('title',$title);
        return $this->fetch();
    }

    // 获取修改信息
    public function modify($id){
    	$extshowlist = new ExtShowList;
		$info = $extshowlist->getInfo($id);
		$num = count($info);
		if($num == 0) $this->error('没有该游戏信息，请查实！');
		$info = $info->toArray();
		$status = strip_tags($info['status']);
		$info['status_num'] = $status == '禁用' ? 0 : 1;
		$this->assign('info',$info);
		return $this->fetch();
    }

    /**修改信息*/
    public function updateData(){
    	if(request()->isPost()){
    		$extshowlist = new ExtShowList;
    		$res = $extshowlist->updateData(request()->post());
    		$res ? $this->success('修改成功') : $this->error('修改失败');
    	}
    } 
    public function addGame(){
    	if(request()->isPost()){
			$post = request()->post();
			$datas = explode('|',$post['datas']);
    		$data['image'] = $datas[0];
    		$data['name'] = $datas[1];
    		$data['title'] = $datas[2];
    		$data['remark'] = $post['remark'];
    		$data['type'] = $post['type'];
    		$extshowlist = new ExtShowList;
    		$msg = $extshowlist->addList($data);
    		$msg['code'] ? $this->success('添加游戏成功') : $this->error($msg['msg']);
    	}
    }
	public function base64Upload($base64_data, $id)
    {
        $path = base64_upload($base64_data,'uploads/extimg/', 'ext'.$id) . '?t=' . time();
        return ['code' => 1, 'data' => $path];
	}
	

	public function deleteData($id)
	{
		$res = (new ExtShowList)->destroy($id);
		$res ? $this->success('删除成功') : $this->error('删除失败');
	}
    //$up 1是上升 2 是下降，$id 数据库里面的id
    public function setSort(){
    	if(request()->isPost()){
    		$id = input('post.id');
    		$up = input('post.up');
    		$extshowlist = new ExtShowList;
    		$msg = $extshowlist->setIdSort($id,$up);
    		return json($msg);
    	}
	}
	
	/**状态修改 */
	public function setStatus()
	{
		if(request()->isPost()){
			$data = request()->post();

			// 验证管理员权限
			if(!$this->admin || !isset($this->admin['id'])){
				return json(['err' => 1, 'msg' => '权限验证失败']);
			}

			// 验证参数
			if(!isset($data['id']) || !isset($data['name']) || !isset($data['value'])){
				return json(['err' => 1, 'msg' => '参数不完整']);
			}

			// 验证字段名
			$allowedFields = ['reco', 'status', 'pause'];
			if(!in_array($data['name'], $allowedFields)){
				return json(['err' => 1, 'msg' => '不允许修改的字段']);
			}

			try {
				// 先查询记录是否存在
				$record = (new ExtShowList)->where('id', $data['id'])->find();
				if(!$record){
					return json(['err' => 1, 'msg' => '记录不存在']);
				}

				// 使用更可靠的更新方法
				$updateData = [$data['name'] => $data['value']];
				$res = (new ExtShowList)->where('id', $data['id'])->update($updateData);

				// 记录详细的操作日志
				$logData = [
					'time' => date('Y-m-d H:i:s'),
					'admin_id' => $this->admin['id'],
					'admin_username' => $this->admin['username'] ?? '',
					'action' => 'setStatus',
					'lottery_id' => $data['id'],
					'lottery_title' => $record['title'],
					'field' => $data['name'],
					'old_value' => $record->getData($data['name']),
					'new_value' => $data['value'],
					'update_data' => $updateData,
					'result' => $res !== false ? 'success' : 'failed',
					'affected_rows' => $res
				];
				file_put_contents('/tmp/admin_operations.log', json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

				if($res !== false){
					return json(['err' => 0, 'msg' => '修改成功']);
				} else {
					return json(['err' => 1, 'msg' => '数据库更新失败']);
				}
			} catch (\Exception $e) {
				$errorData = [
					'time' => date('Y-m-d H:i:s'),
					'error' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'data' => $data
				];
				file_put_contents('/tmp/admin_errors.log', json_encode($errorData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
				return json(['err' => 1, 'msg' => '操作异常: ' . $e->getMessage()]);
			}
		}
		return json(['err' => 1, 'msg' => '请求方法错误']);
	}

	/**临时调试方法 - 绕过session验证 */
	public function debugSetStatus()
	{
		// 临时绕过Base类的session验证
		if(request()->isPost()){
			$data = request()->post();

			// 验证必要参数
			if(!isset($data['id']) || !isset($data['name']) || !isset($data['value'])){
				return json(['err' => 1, 'msg' => '参数不完整']);
			}

			// 验证字段名
			$allowedFields = ['reco', 'status', 'pause'];
			if(!in_array($data['name'], $allowedFields)){
				return json(['err' => 1, 'msg' => '不允许修改的字段']);
			}

			try {
				$res = (new ExtShowList)->allowField('reco,status,pause')->where('id', $data['id'])->setField($data['name'], $data['value']);

				// 记录操作日志
				$logData = [
					'time' => date('Y-m-d H:i:s'),
					'action' => 'debugSetStatus',
					'id' => $data['id'],
					'field' => $data['name'],
					'value' => $data['value'],
					'result' => $res ? 'success' : 'failed'
				];
				file_put_contents('/tmp/debug_setstatus.log', json_encode($logData) . "\n", FILE_APPEND);

				return $res ? json(['err' => 0, 'msg' => '修改成功']) : json(['err' => 1, 'msg' =>'修改失败']);
			} catch (Exception $e) {
				return json(['err' => 1, 'msg' => '操作异常: ' . $e->getMessage()]);
			}
		}
		return json(['err' => 1, 'msg' => '请求方法错误']);
	}
}
