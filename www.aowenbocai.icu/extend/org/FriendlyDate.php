<?php
namespace org;

class FriendlyDate {

    /**
     * 友好的时间显示
     *
     * @param int    $sTime 待显示的时间
     * @param string $type  类型. normal | mohu | full | ymd | other
     * @return string
     */
    public function __construct() {
    }

    public function getmyDate($sTime,$type = 'normal') {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime      =   time();
        $dTime      =   $cTime - $sTime;
        $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
        $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if($type=='normal'){
            if( $dTime < 60 ){
                if($dTime < 10){
                    return '刚刚';    //by yangjs
                }else{
                    return intval(floor($dTime / 10) * 10)."秒前";
                }
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
                //今天的数据.年份相同.日期相同.
            }elseif( $dYear==0 && $dDay == 0  ){
                return '今天'.date('H:i',$sTime);
            }elseif($dYear==0){
                return date("m-d H:i",$sTime);
            }else{
                return date("Y-m-d H:i",$sTime);
            }
        }elseif($type=='mohu'){

            if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay)."天前";
            }elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . '周前';
            }elseif( $dDay > 30 ){
                return intval($dDay/30) . '个月前';
            }

            //full: Y-m-d , H:i:s
        }elseif($type=='full'){
            return date("Y-m-d , H:i:s",$sTime);
        }elseif($type=='ymd'){
            return date("Y-m-d",$sTime);
        }else{
            if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif($dYear==0){
                return date("Y-m-d H:i:s",$sTime);
            }else{
                return date("Y-m-d H:i:s",$sTime);
            }
        }
    }

}

?>