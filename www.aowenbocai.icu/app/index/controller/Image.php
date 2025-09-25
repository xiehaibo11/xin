<?php
namespace app\index\controller;

use app\index\model\User;

class Image
{
    public function load($url, $userid = '')
    {
        header('Access-Control-Allow-Origin: *');
        header("Content-type: image/png");
        if(strstr($url, 'http')){
           $image =  $this->getContent($url);
           $QR = imagecreatefromstring($image);
        }else{
            $url = '.'.ltrim($url,'.');
            $url = explode("?", $url);
            $QR = imagecreatefromstring(file_get_contents($url[0]));
        }
        imagesavealpha($QR,true);
        // $logo = imagecreatefromstring(file_get_contents($logo));
        // $QR_width = imagesx($QR);//二维码图片宽度
        // $QR_height = imagesy($QR);//二维码图片高度
        // $logo_width = imagesx($logo);//logo图片宽度
        // $logo_height = imagesy($logo);//logo图片高度
        // if(!is_int($logosize)) $logosize = 3;
        // if($logosize > 8) $logosize = 3;
        // if($logosize < 2) $logosize = 3;
        // $logo_qr_width = $QR_width / $logosize;
        // $scale = $logo_width/$logo_qr_width;
        // $logo_qr_height = $logo_height/$scale;
        // $from_width = ($QR_width - $logo_qr_width) / 2;
        // //重新组合图片并调整大小
        // imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
        //     $logo_qr_height, $logo_width, $logo_height);
        imagepng($QR);
        // unlink($this->QR);


        die();
    }
    /**curl获取网页信息 */
    public function getContent($url)
    {
        $ch = curl_init();// 创建一个新cURL资源
        curl_setopt($ch , CURLOPT_URL , $url);// 设置URL和相应的选项
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);// 去掉证书认证CURLOPT_SSL_VERIFYHOS
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , 1);// 设置URL和相应的选项
        $data = curl_exec($ch);// 抓取URL并把它传递给浏览器
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $data;
    }
}
