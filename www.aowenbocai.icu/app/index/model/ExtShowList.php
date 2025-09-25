<?php 
	namespace app\index\model;
	use app\admin\model\Ext;
    use think\Model;

	class ExtShowList extends model
	{
    	protected $resultSetType = 'collection';

        /**
         * 获取器
         * @return string
         */
        public function getExpectTypeAttr ($value,$data)
        {
            $info = (new Ext())->where('name', str_replace('/','',$data['name']))->find();
            if (!$info) return 0;
            return $info['expect_type'];
        }

        /**
         * 获取器
         * @return string
         */
        public function getIsSystemCodeAttr ($value,$data)
        {
            $info = (new Ext())->where('name', str_replace('/','',$data['name']))->find();
            if (!$info) return 0;
            return $info['is_system_code'];
        }

    	/*
		*游戏大厅游戏列表
		 */
		public function getList($num = 0, $where = []){
		    if (empty($where)) {
			    $this->where(['status' => 0])->order('sort ASC');
            } else {
                $this->where($where)->where(['status' => 0])->order('sort ASC');
            }
			if (!empty($num)) {
				$this->limit($num);
			}
			$res = $this->select();
			return $res;
		}

		/*
		*游戏大厅游戏推荐列表
		 */
		public function getRecList(){
			return $this->where(['reco' => 1,'status' => 0])->order('sort ASC')->limit(0,4)->select();
		}

		/*
		*用户中心获取游戏
		 */
		public function getUserExtList($extname){
			return $this->where(['name'=>['IN',$extname]])->select();
		}

		public function getGamesList($type = 0)
		{
			return $this->where('type', $type)->where('status', 0)->column(['id','title','name']);
		}
	}
	
 ?>