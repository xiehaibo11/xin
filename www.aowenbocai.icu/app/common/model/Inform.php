<?php
namespace app\common\model;

use think\Model;
use think\Validate;
use app\index\model\User;

class  Inform extends Model
{
    protected $updateTime = false;
    protected $resultSetType = "collection";

    /**
     * 获取器--获取用户信息
    */
    public function getUserInfoAttr($value, $data)
    {
        $info = (new User)->get($data['userid']);
        if($info) $info = $info -> toarray();
        return ['nickname' => $info['nickname'], 'username' => $info['username']];
    }
      /**
     * 获取器--获取用户信息
    */
    public function getStatussAttr($value)
    {
        $status_array = ['未读', '已读'];
        return $status_array[$value];
    }

    /**获取所有符合条件的数据 */
    public function getCount($where = '')
    {
        return $this->where($where)->order('id desc')->count();
    }

     /**获取符合网站后台查询条件的数据 */
     public function getAdminList($where = '')
     {
         return $this->where($where)->order('id desc')->paginate(14);
     }

     /**添加通知 */
     public function addData($data)
     {
        $rules = [
            'content'  => 'require',
            'href'   => 'url',
            'userid' => 'require|alphaDash'
        ];
        $validate = new Validate($rules);
        if (!$validate->check($data)) {
            return ($validate->getError());
        }
        $ids = explode('_', $data['userid']);
        foreach ($ids as $key => $value) {
            $saveData[$key] = ['content' => $data['content'], 'href_url' => $data['href'], 'userid' => $value];
        }
        return $this->saveAll($saveData);
     }

     
    
    /**
     * 删除数据 
     *  @param int data['id'] 根据数据id删除
     *  @param any data['status'] 删除已读状态的数据
     *  @param string data['ids'] id字符串, 删除有id的数据
     *  @param int data['day'] 删除多少天以前的数据 1删除所有
     */
    public function deleteData($data, $userid = '')
    {
        $res = false;
        if($userid != ''){
            $this->where('userid', $userid);
        }
        
        if(isset($data['id'])){
            $res = $this->destroy($data['id']);
        }
        
        if(isset($data['status']) && $data['status'] == 1){
            $res = $this->where(['statuss' => 1])->delete();
        }
        
        if(isset($data['ids'])){
            $res = $this ->where(['id' => ['in', $data['ids']]])->delete();
        }
        
        if(isset($data['day'])){
            $today = strtotime(date("Y-m-d"));
            $start = $data['day'] == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$data['day']." day", $today));
            $res = $this ->where(['create_time' => ['<' , $start]])->delete();
        }
        return $res;
    }
}
