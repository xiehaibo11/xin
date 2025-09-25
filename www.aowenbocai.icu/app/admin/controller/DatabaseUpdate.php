<?php
namespace app\admin\controller;

use app\admin\model\ExtShowList;
use app\admin\model\UserIdbank;
use app\common\model\MoneyHistory;
use app\lottery\model\common\BaseBuy;
use core\Setting;
use think\Config;
use app\admin\com\Database;
use think\Db;
use think\Validate;

class DatabaseUpdate extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 老系统用户数据导入
     */
    public function userImport()
    {
        $data = $this->getXlsData('user.xls');
        $user_data = [];
        $idbank_data = [];
        foreach ($data as $v) {
            if (!$v['id']) continue;
            $user_data[] = [
                'id' => $v['id'],
                'username' => $v['username'],
                'nickname' => $v['username'],
                'password' => $v['userpass'],
                'tel' => $v['tel'],
                'qq' => $v['qq'],
                'create_time' => date('Y-m-d H:i:s', strtotime($v['addtime'])),
                'money' => $v['Money'],
                'email' => $v['email'],
                'last_ip' => $v['logip'],
                'sid' => getRandChar(16)
            ];
            if ($v['truename'] and $v['identityid']) {
                $idbank_data[] = [
                    'userid' => $v['id'],
                    'idname' => $v['truename'],
                    'idnum' => $v['identityid'],
                ];
            }
        }
        if (!empty($user_data)) (new \app\admin\model\User())->insertAll($user_data);
        if (!empty($idbank_data)) (new UserIdbank())->insertAll($idbank_data);
    }

    /**
     * 老系统用户数据导入
    */
    private function getXlsData($file)
    {
        /**
         * PHPEXCEL生成excel文件
         * @desc 支持任意行列数据生成excel文件，暂未添加单元格样式和对齐
         */
        $objReader = \PHPExcel_IOFactory::createReader ( 'Excel2007' );
        $file = "user.xls";
        if(!$objReader->canRead($file)){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }
        $objReader->setReadDataOnly( true );
        $objPHPExcel = $objReader->load ($file);
        $objWorksheet = $objPHPExcel->getSheet (0);
//取得excel的总行数
        $highestRow = $objWorksheet->getHighestRow ();
//取得excel的总列数
        $highestColumn = $objWorksheet->getHighestColumn ();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString ( $highestColumn );
        $excelData = array ();
        for($row = 2; $row <= $highestRow; $row++) {
            for($col = 0; $col < $highestColumnIndex; $col++) {
                $v = $objWorksheet->getCellByColumnAndRow ( $col, $row )->getValue ();
                if ($objWorksheet->getCellByColumnAndRow ( $col, 1 )->getValue () == 'addtime') {
                    $excelData[$row-2][$objWorksheet->getCellByColumnAndRow ( $col, 1 )->getValue ()] = gmdate('Y-m-d H:i:s',\PHPExcel_Shared_Date::ExcelToPHP($v));
                } else {
                    $excelData[$row-2][$objWorksheet->getCellByColumnAndRow ( $col, 1 )->getValue ()] = $v;
                }
            }
        }
        return $excelData;
    }

    /**
     * 老系统用户数据导入
     */
    public function lottery()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $ext_list = (new ExtShowList())->where('status', 0)->where('type', 1)->column('name');
        $buy_field = Db::name('lottery_buy')->getTableFields();
        $code_field = Db::name('lottery_code')->getTableFields();
        $expect_field = Db::name('lottery_expect')->getTableFields();
        $join_field = Db::name('lottery_join')->getTableFields();
        foreach ($ext_list as $v) {
            $v = trim($v, '/');
            $list = Db::name('plugin_' . $v . '_buy')->select();
            foreach ($list as $row) {
                $my_id = $row['id'];
                unset($row['id']);
                $row['ext_name'] = $v;
                $buy_id = Db::name('lottery_buy')->insertGetId($this->initData2($row, $buy_field));

                $expect_list = Db::name('plugin_' . $v . '_expect')->where('buy_id', $my_id)->select();
                $expect_data = [];
                foreach ($expect_list as $expect_v) {
                    $expect_v['buy_id'] = $buy_id;
                    unset($expect_v['id']);
                    $expect_v['ext_name'] = $v;
                    array_push($expect_data, $expect_v);
                }
                if (!empty($expect_data))  Db::name('lottery_expect')->insertAll($this->initData($expect_data, $expect_field));
                $is_chase = count($expect_data) > 1 ? 1 : 0;
                $join_list = Db::name('plugin_' . $v . '_join')->where('buy_id', $my_id)->select();
                $join_data = [];
                foreach ($join_list as $join_v) {
                    $join_v['buy_id'] = $buy_id;
                    unset($join_v['id']);
                    $join_v['ext_name'] = $v;
                    $join_v['is_chase'] = $is_chase;
                    $join_v['join_status'] = $row['is_join'] ? (($row['userid'] == $join_v['userid']) ? 1 : 2) : 0;
                    array_push($join_data, $join_v);
                }
                if (!empty($join_data))  Db::name('lottery_join')->insertAll($this->initData($join_data, $join_field));
            }
            $code_list = Db::name('plugin_' . $v . '_code')->select();
            foreach ($code_list as $code_v) {
                unset($code_v['id']);
                $code_v['ext_name'] = $v;
                Db::name('lottery_code')->insert($this->initData2($code_v, $code_field));
            }
        }
        return json(['err' => 0, 'msg' => '执行成功']);
    }

    /**
     * 老系统用户数据导入
     */
    public function initData($data, $field)
    {
        $new_data = [];
        foreach ($data as $v) {
            $my_data = [];
            foreach ($field as $row) {
                $my_data[$row] = $v[$row];
            }
            array_push($new_data, $my_data);
        }
        return $new_data;
    }

    /**
     * 老系统用户数据导入
     */
    public function initData2($data, $field)
    {
        $new_data = [];
        foreach ($field as $row) {
            $new_data[$row] = $data[$row];
        }
        return $new_data;
    }

    /**
     * 老系统用户数据导入
     */
    public function backMoney($user_id, $time, $endtime)
    {
        $buy_model = (new BaseBuy());
        $lottery_id_data = $buy_model->where('create_time', '>=', $time)->where('create_time', '<', $endtime)->where('userid', $user_id)->column('lottery_id');
        $money_model = (new MoneyHistory());
        $user_model = (new \app\web\model\User());
        if (!empty($lottery_id_data)) {
            foreach ($lottery_id_data as $v) {
                $user_data = $money_model->where('type', 6)->where('remark', 'like', '%' . $v . '%')->select();
                foreach ($user_data as $row) {
                    $user_model->where('id', $row['userid'])->setDec('money', $row['money']);
                    $money_model->where('id', $row['id'])->delete();
                }
            }
        }
        return json(['msg' => '完成']);
    }
}
