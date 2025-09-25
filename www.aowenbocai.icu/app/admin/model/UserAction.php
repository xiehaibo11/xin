<?php
    namespace app\admin\model;

    use think\Model;
    use think\Validate;

    class UserAction extends BaseModel
    {
		protected $updateTime = false;
		protected $createTime = false;
        protected $resultSetType = 'collection';
        
        public function getExtInfoAttr($value, $data)
        {
            if($data['ext_name'] == 'index/Shop'){
                return '商城兑换';
            }

            $ext_name = ['/'.$data['ext_name'], $data['ext_name']];
            $info = (new ExtShowList)->get(['name' => ['in' , $ext_name]]);
            if($info){
                $info = $info->toArray();
                return $info['title'];
            }else{
                return '系统';
            }
        }

        public function getList($where = [],$limit = 14){
            return $this->where($where)->order("id desc")->paginate($limit);
        }

        /**
         * 获取条数
         * @param int $type 用户判断获取的数据是分页数据还是所有数据
         * @param array $ext_name 模块名
         */
        public function getListCount($type = 0,$ext_name = [])
        {
            $data = request()->get();
            if ($starttime = $data['starttime']) {
                $this->where('create_time', '>=', $starttime);
            }
            if ($endtime= $data['endtime']) {
                $this->where('create_time', '<=', $endtime." 23:59:59");
            }
            if($ext_name){
                $this->where('ext_name', 'in', $ext_name);
            }
            $this->field('count(id) as num, ext_name')->group('ext_name');
            if($type == 1){
                return $this->select();
            }else{  
                return $this->paginate(14);
            }
        }
    }
    
?>