<?php
namespace app\fadancoller\model;

use core\Setting;
use think\Env;
use think\Model;


class Lottery_join extends Model
{
   

    public function add($data)
    {
        return $this->insertGetId($data);
    }
    
      public function getlist($where,$limit)
    {
    	$list=$this->where($where)->limit($limit)->select();
        return $list;
    }
    
    
    public function addmoney($id,$money)
    {
    	
    return	$this->where(array('id'=>$id))->setInc('money',$money);
    	
    }
    
    public function getcountmoney($buyid)
    {
    	
    	return $this->where(array('buy_id'=>$buyid))->sum('money');
    }


}