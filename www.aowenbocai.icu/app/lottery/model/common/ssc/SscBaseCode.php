<?php
namespace app\lottery\model\common\ssc;

use app\lottery\model\common\BaseCode;

class SscBaseCode extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取器 - 十位判断单双
     */
    public function getShiAttr($value, $data)
    {
        $code_array = explode(',', $data['code']);
        return $this->checkDdCode($code_array[3]);
    }

    /**
     * 获取器 - 个位判断单双
     */
    public function getGeAttr($value, $data)
    {
        $code_array = explode(',', $data['code']);
        return $this->checkDdCode($code_array[4]);
    }

    /**
     * 判断大小单双
     */
    public function checkDdCode($num)
    {
        $res = $num < 5 ? '小' : '大';
        $res = $num % 2 == 0 ? $res . '双' : $res . '单';
        return $res;
    }

    /**
     * 获取器 - 后三判断组三/组六/豹子
     */
    public function getHousanAttr($value, $data)
    {
        $code_array = explode(',', $data['code']);
        $hs = array_slice($code_array, 2, 3);
        $num = count(array_unique($hs));
        switch ($num) {
            case '1':
                return '豹子';
                break;
            case '2':
                return '组三';
                break;
            case '3':
                return '组六';
                break;
        }
    }
    

}