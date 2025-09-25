<?php
namespace app\fadancoller\model;

use core\Setting;
use think\Env;
use think\Model;


class Lottery_buy extends Model
{
   

    public function add($data)
    {
        return $this->insertGetId($data);
    }
    
    public function getlist($where,$limit)
    {
    	$list=$this->where($where)->limit($limit)->order('id desc')->select();
    	return $list;
    }
    
   
public function getLastone($userid)
{
	
	$r=$this->where('userid',$userid)->order('id desc')->find();
    	return $r;
}


public function getuserid($id)
{
	
	$r=$this->where('id',$id)->find();
    	return $r['userid'];
}

}