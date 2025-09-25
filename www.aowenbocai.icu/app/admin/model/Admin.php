<?php
namespace app\admin\model;

use think\Validate;
use think\db\Query;

class Admin extends BaseModel
{
    protected $autoWriteTimestamp = false;//自动更新时间

    public function user()
    {
        return $this->hasOne('User','id',"userid");
    }

    /**
     * 添加权限
     * @param  array
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }

        $user = User::where($data)->find();
        if (!$user) {
            return ["code"=>0,"msg"=>"用户名不存在"];
        }
        $admin = $this->where(['userid'=>$user['id']])->find();
        if ($admin) {
            return ["code"=>0,"msg"=>"已添加此管理员"];
        }
        $this->save(['userid'=>$user['id']]);

        return ["code"=>1,"id"=>$user['id']];
    }

    /**获取数据库 */
    public function getDatabases()
    {
        $database = config('database.database');
        $key = "Tables_in_".$database;
        $query = new Query;
        $bases = $query->query('show tables from '.$database);
        $return_data[] = ['table' => 'kr_money_history', 'name' => 'moneyHistory'];
        foreach($bases as $value){
            $table = $value['Tables_in_app'];
            $tableArr = explode('_', $table);
            if($tableArr[1] != 'plugin' && $tableArr[1] != 'game'){
                continue;
            }
            $new = array_slice($tableArr, 2);
            $name = implode('',$new);
            if(in_array('buy', $tableArr)){
                array_push($return_data, ['table' => $table, 'name' => $name]);
            }else{
                $result = $query->query('select COLUMN_TYPE from information_schema.columns where table_name = "'.$table.'" and (column_name = "bouns" or column_name = "bonus")');
                if(empty($result)){
                    continue;
                }
                array_push($return_data, ['table' => $table, 'name' => $name]);
            }
        }
        return $return_data;
    }

    /**
     * 删除管理员数据
     * @param int $id 管理员ID
     * @return array
     */
    public function deleteData($id)
    {
        try {
            // 获取管理员信息
            $admin = $this->get($id);
            if (!$admin) {
                return ["code" => 0, "msg" => "管理员不存在"];
            }

            // 获取关联的用户信息
            $user = $admin->user;
            if (!$user) {
                return ["code" => 0, "msg" => "关联用户不存在"];
            }

            // 防止删除超级管理员
            if ($user->username == 'xie080886') {
                return ["code" => 0, "msg" => "不能删除超级管理员"];
            }

            // 执行删除
            $res = $admin->delete();
            if ($res) {
                return ["code" => 1, "msg" => "删除成功"];
            } else {
                return ["code" => 0, "msg" => "删除失败"];
            }

        } catch (\Exception $e) {
            // 记录错误日志
            \think\Log::error('删除管理员失败: ' . $e->getMessage());
            return ["code" => 0, "msg" => "删除失败: " . $e->getMessage()];
        }
    }
}
