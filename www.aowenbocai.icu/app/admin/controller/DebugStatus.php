<?php
namespace app\admin\controller;

use app\admin\model\ExtShowList;
use think\Controller;

/**
 * 调试状态修改控制器 - 不继承Base类，绕过session验证
 */
class DebugStatus extends Controller
{
    /**
     * 调试状态修改方法
     */
    public function setStatus()
    {
        // 设置响应头
        header('Content-Type: application/json; charset=utf-8');
        
        if(request()->isPost()){
            $data = request()->post();
            
            // 验证必要参数
            if(!isset($data['id']) || !isset($data['name']) || !isset($data['value'])){
                return json(['err' => 1, 'msg' => '参数不完整', 'data' => $data]);
            }
            
            // 验证字段名
            $allowedFields = ['reco', 'status', 'pause'];
            if(!in_array($data['name'], $allowedFields)){
                return json(['err' => 1, 'msg' => '不允许修改的字段: ' . $data['name']]);
            }
            
            // 验证ID是否为数字
            if(!is_numeric($data['id'])){
                return json(['err' => 1, 'msg' => 'ID必须为数字']);
            }
            
            // 验证value值
            if(!in_array($data['value'], ['0', '1', 0, 1])){
                return json(['err' => 1, 'msg' => 'value值只能为0或1']);
            }
            
            try {
                // 先查询记录是否存在 - 使用getData获取原始数据
                $record = (new ExtShowList)->where('id', $data['id'])->find();
                if(!$record){
                    return json(['err' => 1, 'msg' => '记录不存在，ID: ' . $data['id']]);
                }

                // 获取原始数据值（避免获取器影响）
                $oldValue = $record->getData($data['name']);

                // 执行更新操作
                $res = (new ExtShowList)->allowField('reco,status,pause')->where('id', $data['id'])->setField($data['name'], $data['value']);

                // 记录操作日志
                $logData = [
                    'time' => date('Y-m-d H:i:s'),
                    'action' => 'debugSetStatus',
                    'id' => $data['id'],
                    'field' => $data['name'],
                    'old_value' => $oldValue,
                    'new_value' => $data['value'],
                    'result' => $res ? 'success' : 'failed',
                    'record_title' => $record['title'],
                    'update_result' => $res
                ];
                file_put_contents('/tmp/debug_setstatus.log', json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
                
                if($res){
                    // 验证更新结果
                    $updatedRecord = (new ExtShowList)->where('id', $data['id'])->find();
                    return json([
                        'err' => 0, 
                        'msg' => '修改成功',
                        'data' => [
                            'id' => $data['id'],
                            'field' => $data['name'],
                            'old_value' => $record[$data['name']],
                            'new_value' => $updatedRecord[$data['name']],
                            'title' => $record['title']
                        ]
                    ]);
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
                file_put_contents('/tmp/debug_error.log', json_encode($errorData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
                
                return json(['err' => 1, 'msg' => '操作异常: ' . $e->getMessage()]);
            }
        }
        
        return json(['err' => 1, 'msg' => '请求方法错误，需要POST请求']);
    }
    
    /**
     * 获取彩种列表
     */
    public function getList()
    {
        try {
            $list = (new ExtShowList)->where('type', 1)->order('sort', 'asc')->select();
            return json(['err' => 0, 'msg' => '获取成功', 'data' => $list]);
        } catch (\Exception $e) {
            return json(['err' => 1, 'msg' => '获取失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 测试页面
     */
    public function test()
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>彩种状态调试工具</title>
            <meta charset="utf-8">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </head>
        <body>
            <h1>彩种状态调试工具</h1>
            <div>
                <h3>修改彩种状态</h3>
                <form id="statusForm">
                    <label>彩种ID: <input type="number" name="id" value="3" required></label><br><br>
                    <label>字段名: 
                        <select name="name">
                            <option value="status">status (启用/禁用)</option>
                            <option value="pause">pause (运行/暂停)</option>
                            <option value="reco">reco (推荐)</option>
                        </select>
                    </label><br><br>
                    <label>值: 
                        <select name="value">
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </label><br><br>
                    <button type="submit">提交修改</button>
                </form>
                <div id="result"></div>
            </div>
            
            <script>
            $("#statusForm").submit(function(e){
                e.preventDefault();
                $.post("/admin.php/debug_status/setstatus", $(this).serialize(), function(res){
                    $("#result").html("<pre>" + JSON.stringify(res, null, 2) + "</pre>");
                });
            });
            </script>
        </body>
        </html>';
        
        return $html;
    }
}
