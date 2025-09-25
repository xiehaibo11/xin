<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Bbs as ABbs;
use app\admin\model\User;
use think\Paginator;


class Bbs extends Base{
	/**
     * 主页信息
     */
	public function index()
	{	
		$data  = request()->get();
		$bbs = new ABbs;
		if(!empty($data)){
			if(!empty($data['starttime'])){
				$bbs->where('create_time', '>=', $data['starttime']);
			}

			if(!empty($data['endtime'])){
				$bbs->where('create_time', '>=', $data['endtime']);
			}

			if(!empty($data['words'])){
				$ids = (new User)->where('nickname|username', 'like', "%{$data['words']}%")->column('id');
				if(!empty($ids)){
					$bbs->where('userid', 'in', $ids)->whereOR('content', 'like', "%{$data['words']}%");
				}else{
					$bbs->where('content', 'like', "%{$data['words']}%");
				}
			}

		}

		$list = $bbs->paginate(14);
		$list->append(['userinfo','mincontent']);

		$this->assign("list",$list);
		$this->assign('data',$data);
		return $this->fetch('index',['title'=>'圈子管理']);
	}
	
	/**
     * 帖子信息
     */
	public function edit($id = ''){
		if(request()->IsPost()){
			$data = request()->post();
			(new ABbs)->allowField(true)->save($data, ['id' => $id]);
		}
		$info = (new ABbs)->get($id);
		$info['statuss'] = $info ->getData('status');
		$info = $info->toArray();
		$info['status'] = strip_tags($info['status']);
		$this->assign("info",$info);
		return $this->fetch('edit',['title'=>'帖子信息']);
	}


	/**
     * 帖子详情
     */
    public function detail($id){
		$info = (new ABbs)->get($id);
		$info->append(['userinfo', 'replynum', 'goodnum']);
		$info = $info->toArray();
		$info['images'] = explode(',', $info['images']);
		$this->assign("info",$info);
	    return $this->fetch('detail',['title'=>'详情']);
    }
	/**
     * 帖子修改
     */
	public function mod(){
	    if(request()->isAjax()){
	        $data=$this->post;
	        unset($this->post['id']);
            if((new ABbs)->mod($this->post,$data['id'])){
                return ['code'=>1];
            }
            return ['code'=>2,'msg'=>'修改失败或数据未变动'];
        }
    }
	/**
     * 主帖删除
     */
    public function del(){
		// $id=$this->id;
		$data = request()->param();
		if(empty($data['id'])){
            $this->error('信息错误');
		}
		$res = (new ABbs)->destroy($data['id']);
        if($res){
			if($image=Db('Bbs')->where('id',$id)->value('images')){
				$image=explode(',',$image);
				foreach($image as $img){
					unlink($_SERVER['DOCUMENT_ROOT'].$img);
				}
			}
			Db('bbs_reply')->where('pid',$id)->delete();
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
	/**
     * 删除回帖
     */
	public function re_delete(){
		if(is_numeric(input('get.id'))){
			$id=input('get.id');
			$pid=Db('bbs_reply')->where('id',$id)->value('pid');	
			if(Db('bbs_reply')->where('id',$id)->value('tid')==0){	
				if(Db('bbs_reply')->where('id',$id)->delete()){
					Db('bbs_reply')->where('tid',$id)->delete();
					Db('bbs')->where('id',$pid)->setDec('takenum');
					return $this->success('删除成功');	
				}else{
					return $this->error('删除失败');
				}
			}else{
				if(Db('bbs_reply')->where('id',$id)->delete()){
					return $this->success('删除成功');
				}else{
					return $this->error('删除失败');
				}
			}
		}
	}
	/**
     * 主贴批量删除
     */
	public function clear(){
		$day=input('get.day');
        if ($day){
            $endtime = date('Y-m-d', strtotime(" -$day day"));
            if(Db('bbs')->where('create_time', '<', $endtime )->delete()){
				Db('bbs_reply')->where('create_time', '<', $endtime)->delete();
                return $this->success('数据已清空',url("admin/Bbs/index"));
            }
        } else {
            if(Db('Bbs')->where('1=1')->delete()){
				Db('bbs_reply')->where("1=1")->delete();
                return $this->success('数据已清空',url("admin/Bbs/index"));
            }
        }
        return $this->error('没有可清空的数据',url("admin/Bbs/index"));
	}
}