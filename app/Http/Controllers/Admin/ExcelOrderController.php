<?php
/*
 * @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\User;
use App\Library\PHPDev\CDate;
use App\Library\PHPExcel\PHPExcel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use App\Http\Models\Order;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;

class ExcelOrderController extends BaseAdminController{

	private $arrUser = array();
	private $error = '';
	public function __construct(){
		parent::__construct();

		$listUser = User::getAllUser(array(), 0);
		$this->arrUser = User::arrUser($listUser);
	}


	public function createFileExcelOrder(){

		$order_from = Request::get('order_from', '');
		$order_to = Request::get('order_to', '');
        $order_status = Request::get('order_status', CGlobal::cho_gui);

		$dataSearch['order_lendon'] = 0;
        $dataSearch['order_status'] = $order_status;

        if($order_from == '' && $order_to == ''){
            //Thang hien tai
            $month_year_current = date('m-Y');
            $count_day_in_month = cal_days_in_month(CAL_GREGORIAN, date('m', time()), date('Y', time()));
            $dataSearch['order_from'] ='01-'.$month_year_current;
            $dataSearch['order_to'] =  $count_day_in_month.'-'.$month_year_current;
        }else{
            $_order_from = CDate::convertDate($order_from.' 00:00:00');
            $_order_to = CDate::convertDate($order_to. ' 23:59:59');
            if($_order_to < $_order_from && $_order_to > 0){
                echo 'Chọn ngày chưa hợp lý';die;
            }else{
                $dataSearch['order_from'] = $order_from;
                $dataSearch['order_to'] =  $order_to;
            }
        }
        $dataSearch['order_sort'] = 'asc';
        $data = Order::searchByConditionDashBoard($dataSearch);
        if(sizeof($data) > 0){
            //Error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Ho_Chi_Minh');

            if (PHP_SAPI == 'cli'){
                die('This app should only be run from a Web Browser');
            }

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("quynhtm")
                ->setLastModifiedBy("quynhtm")
                ->setTitle("Office 2007 XLSX Document")
                ->setSubject("Office 2007 XLSX Document")
                ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("View Result file");
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
           // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);

            $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(20);
            $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
            $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'STT')
                ->setCellValue('B1', 'Tên khách hàng')
                ->setCellValue('C1', 'Địa chỉ')
                ->setCellValue('D1', 'SĐT')
                ->setCellValue('E1', 'COD')
                //->setCellValue('F1', 'Tên SP')
                //->setCellValue('G1', 'SL')
                ->setCellValue('F1', 'Ghi chú');

            $i=1;
            $j=1;
            $r=1;//stt
            $row_merg = 0;
            $name_file = 'xuat_don_hang';

            foreach($data as $item) {
                $order_list_code = (isset($item->order_list_code) && $item->order_list_code != '') ? unserialize($item->order_list_code) : array();
                if(is_array($order_list_code) && sizeof($order_list_code) > 0){
                    $i++;
                    //$row = count($order_list_code);
                    //$row_merg = $i + $row - 1;

                    /*
                    if ($row_merg > $i) {
                        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $i . ':A' . $row_merg);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $i . ':B' . $row_merg);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $i . ':C' . $row_merg);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $i . ':D' . $row_merg);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $i . ':E' . $row_merg);
                        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $i . ':H' . $row_merg);
                    }
                    */
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $r)
                        ->setCellValue('B'.$i, $item->order_title)
                        ->setCellValue('C'.$i, stripcslashes($item->order_address))
                        ->setCellValue('D'.$i, $item->order_phone)
                        ->setCellValue('E'.$i, (int)$item->order_total_lst)
                        ->setCellValue('F'.$i, stripcslashes($item->order_note));

                    /*
                    foreach($order_list_code as $_item){
                       $j++;
                       $objPHPExcel->setActiveSheetIndex(0)
                           ->setCellValue('F'.$j, $_item['pcode'])
                           ->setCellValue('G'.$j, $_item['pnum']);
                    }
                    $i = $row_merg;
                    */
                    $r++;
                    $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($i)->setRowHeight(15);
                }
            }

            $objPHPExcel->getActiveSheet()->setTitle($name_file.'.xls');
            $objPHPExcel->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name_file.'.xls"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            die;
        }else{
            echo 'Không tồn tại đơn hàng. <a href="'.Config::get('config.BASE_URL').'admin/order">Quay lại</a>';die;
        }
        die;
	}
}