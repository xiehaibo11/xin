<?php
namespace app\fadancoller\model;

use core\Setting;
use think\Env;
use think\Model;


class Lottery_expect extends Model
{
   

    public function add($data)
    {
        return $this->insertGetId($data);
    }
    
     public function getone($buyid)
     {
     	return $this->where('buy_id',$buyid)->find();
     	
     }
     
         public function getcount($expect)
     {
     	return $this->where('expect',$expect)->count();
     	
     }
     
     public function getbuyerid($expect,$ext_name)
     {
         $buyidarr=array();
         $allbuy=$this->where(['expect'=>$expect,'ext_name'=>$ext_name])->select();
     
         if($allbuy)
         {
             foreach($allbuy as $vo)
             {
               array_push($buyidarr,$vo['buy_id'])  ;
             }
         }
         return $buyidarr;
     }


}