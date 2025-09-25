<?php
namespace app\phpqrcode\controller;

use think\Loader;
//二维码生成
class QR {
    private $value;//二维码内容
    private $errorCorrectionLevel; //容错级别分别是 L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）；
    private $matrixPointSize; //生成图片大小,默认为3，
    private $QR;//原生图片

    public function __construct($value,$qrarray=array()){
        Loader::import('phpqrcode', EXTEND_PATH);
        $this->value = isset($value)?$value:'';
        $this->errorCorrectionLevel = isset($qrarray['errorCorrectionLevel']) ? $qrarray['errorCorrectionLevel']:'H';
        $this->matrixPointSize = isset($qrarray['matrixPointSize']) ? $qrarray['matrixPointSize'] : 6;
        $this->QR = isset($qrarray['QR']) ? $qrarray['QR'].'.png' : 'qr.png';
        \QRcode::png($this->value,$this->QR,$this->errorCorrectionLevel,$this->matrixPointSize,2);
    }

    /**
     *生成二维码，并合成logo
     * @param $logo 二维码中心的logo
     * @param $logosize logo的大小，数字越小，logo越大，取值：2 - 8;
     */
    public function Q($logo,$logosize = 3){
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($this->QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            if(!is_int($logosize)) $logosize = 3;
            if($logosize > 8) $logosize = 3;
            if($logosize < 2) $logosize = 3;
            $logo_qr_width = $QR_width / $logosize;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
            imagepng($QR);
            unlink($this->QR);
        }else{
            return false;
        }
    }
}