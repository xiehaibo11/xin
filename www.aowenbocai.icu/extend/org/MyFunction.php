<?php
namespace org;

/**
 * 订单生成类
*/
class MyFunction
{
    /** excel导出
     * @param  array $excel_title excel的字段名字
     * @param  $excel_filed  array  数据表中的字段
     * @param  $db_admin  array  导出数据
     * @param  $filename  string  文件名称
     * */
    public static function comm_exportExcel($excel_title, $excel_filed, $excel_width, $db_admin, $filename = '网站数据')
    {
        $filename = $filename . '.xls';
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->createSheet();
        $objSheet = $objPHPExcel->getActiveSheet();//获得当前活动Sheet
        $objSheet->setTitle("one");
        foreach ($excel_title as $key => $value) {
            $key = chr(97 + $key);
            $objSheet->setCellValue($key . '1', $value);
            //$objSheet->getStyle($key . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$objSheet->getStyle($key . '1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
        foreach ($excel_width as $key => $value) {
            $key = chr(97 + $key);
            $objSheet->getColumnDimension($key)->setWidth($value);
        }
        $j = 2;
        foreach ($db_admin as $key => $value) {
            foreach ($excel_filed as $key2 => $row) {
                $key2 = chr(97 + $key2);
                if ($row == 'title') {
                    $my_v = $value->getData($row);
                } else {
                    $my_v = $value[$row];
                }
                $objSheet->setCellValue($key2 . $j, $my_v);
                //$objSheet->getStyle($key2 . $j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //$objSheet->getStyle($key2 . $j)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
            $j++;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type:application/x-excel;name="xxx.xls"');//告诉浏览器将要输出excel03文件
        header('Content-Disposition: attachment;filename="' . $filename . '"');//告诉浏览器将输出文件的名称(文件下载)
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter->save("php://output");

    }
}