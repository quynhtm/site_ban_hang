<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Banner;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Trash;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

class BannerController extends BaseAdminController{

	private $permission_view = 'banner_view';
	private $permission_create = 'banner_create';
	private $permission_edit = 'banner_edit';
	private $permission_delete = 'banner_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $arrRunTime = array(-1 => 'Chọn thời gian chạy', CGlobal::status_hide => 'Chạy mãi mãi', CGlobal::status_show => 'Chạy theo thời gian');
	private $arrTarget = array(CGlobal::status_hide => 'Blank', CGlobal::status_show => 'Parent');
	private $arrRel = array(CGlobal::status_hide => 'Nofollow', CGlobal::status_show => 'Follow');
	private $arrType = array(
			-1 => 'Chọn vị trí',
			0 => 'Header',
			1 => 'Slider',
			2 => 'Left',
			3 => 'Right',
			4 => 'DuoiSliderIndex',
			5 => 'Trans',
			6 => 'Hàng mới về',
		);
	
	private $error = '';
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/datetimepicker/datetimepicker.css', CGlobal::$postHead);
		Loader::loadJS('libs/datetimepicker/jquery.datetimepicker.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/upload/cssUpload.css', CGlobal::$postHead);
		Loader::loadJS('libs/upload/jquery.uploadfile.js', CGlobal::$postEnd);
		Loader::loadJS('backend/js/upload-admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);
	}
	public function listView(){

        if(!in_array($this->permission_view, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

        //Config Page
		$pageNo = (int) Request::get('page', 1);
		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::num_record_per_page;
		$offset = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['banner_title'] = addslashes(Request::get('banner_title', ''));
		$search['banner_status'] = (int)Request::get('banner_status', -1);
		$search['banner_type'] = (int)Request::get('banner_type', -1);
		$search['banner_is_target'] = (int)Request::get('banner_is_target', 0);
		$search['banner_is_rel'] = (int)Request::get('banner_is_rel', 0);
		
		$search['field_get'] = '';
		
		$dataSearch = Banner::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['banner_status']);
		$messages = Utility::messages('messages');

		return view('admin.banner.list',[
					'data'=>$dataSearch,
					'total'=>$total,
					'paging'=>$paging,
					'arrStatus'=>$this->arrStatus,
					'optionStatus'=>$optionStatus,
					'arrTarget'=>$this->arrTarget,
					'arrRel'=>$this->arrRel,
					'arrType'=>$this->arrType,
					'search'=>$search,
					'messages'=>$messages,
				]);

	}
	public function getItem($id=0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$data = array();
		if($id > 0) {
			$data = Banner::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['banner_status'])? $data['banner_status'] : CGlobal::status_show);
		$optionType = Utility::getOption($this->arrType, isset($data['banner_type'])? $data['banner_type'] : -1);
		$optionTarget = Utility::getOption($this->arrTarget, isset($data['banner_is_target'])? $data['banner_is_target'] : 0);
		$optionRel = Utility::getOption($this->arrRel, isset($data['banner_is_rel'])? $data['banner_is_rel'] : 0);
		$optionRunTime = Utility::getOption($this->arrRunTime, isset($data['banner_is_run_time'])? $data['banner_is_run_time'] : 0);


		return view('admin.banner.add',[
				'id'=>$id,
				'data'=>$data,
				'optionStatus'=>$optionStatus,
				'optionType'=>$optionType,
				'optionTarget'=>$optionTarget,
				'optionRel'=>$optionRel,
				'optionRunTime'=>$optionRunTime,
				'error'=>$this->error,
			]);

	}
	public function postItem($id=0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$id_hiden = (int)Request::get('id_hiden', 0);
		$data = array();
		
		$dataSave = array(
				'banner_title'=>array('value'=>addslashes(Request::get('banner_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'banner_title_show'=>array('value'=>addslashes(Request::get('banner_title_show')), 'require'=>1, 'messages'=>'Tiêu đề hiển thị không được trống!'),
				'banner_intro'=>array('value'=>addslashes(Request::get('banner_intro')),'require'=>0),
				'banner_link'=>array('value'=>addslashes(Request::get('banner_link')),'require'=>0),
				'banner_order_no'=>array('value'=>(int)addslashes(Request::get('banner_order_no')),'require'=>0),
				'banner_status'=>array('value'=>(int)Request::get('banner_status', -1),'require'=>0),
				'banner_is_target'=>array('value'=>(int)(Request::get('banner_is_target')),'require'=>0),
				'banner_is_rel'=>array('value'=>(int)(Request::get('banner_is_rel')),'require'=>0),
				'banner_type'=>array('value'=>(int)(Request::get('banner_type')),'require'=>0),
				'banner_is_run_time'=>array('value'=>(int)Request::get('banner_is_run_time'),'require'=>0),
				'banner_start_time'=>array('value'=>trim(Request::get('banner_start_time')),'require'=>0),
				'banner_end_time'=>array('value'=>trim(Request::get('banner_end_time')),'require'=>0),
				'banner_create_time'=>array('value'=>time(),'require'=>0),
		);
		
		if($id > 0){
			unset($dataSave['banner_create_time']);
		}
		
		//Add Thoi Gian Cho Banner
		$banner_is_run_time = $dataSave['banner_is_run_time']['value'];
		$banner_start_time = $dataSave['banner_start_time']['value'];
		$banner_end_time = $dataSave['banner_end_time']['value'];
		
		$banner_start_time = ($banner_start_time != '') ? strtotime($banner_start_time . ' 00:00:00'): 0;
		$banner_end_time = ($banner_end_time != '') ? strtotime($banner_end_time . ' 23:59:59') : 0;
		
		$dataSave['banner_start_time']['value'] = ($banner_is_run_time == CGlobal::status_show) ? $banner_start_time : 0;
		$dataSave['banner_end_time']['value'] = ($banner_is_run_time == CGlobal::status_show) ? $banner_end_time : 0;
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			//So Sanh Anh Cu Va Moi, Neu Khac Nhau thi Xoa Anh Cu Di
			if($id > 0){
				$banner_image = trim(addslashes(Request::get('img')));
				$banner_image_old = trim(addslashes(Request::get('img_old')));
				if($banner_image_old !== '' && $banner_image !== '' && strcmp ( $banner_image_old , $banner_image ) != 0){
					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_BANNER.'/'.$id;
					if(is_file($path.'/'.$banner_image_old)){
						@unlink($path.'/'.$banner_image_old);
					}
				}
			}
			
			Banner::saveData($id, $dataSave);
			return Redirect::route('admin.banner');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['banner_status'])? $data['banner_status'] : -1);
		$optionType = Utility::getOption($this->arrType, isset($data['banner_type'])? $data['banner_type'] : -1);
		$optionTarget = Utility::getOption($this->arrTarget, isset($data['banner_is_target'])? $data['banner_is_target'] : 0);
		$optionRel = Utility::getOption($this->arrRel, isset($data['banner_is_rel'])? $data['banner_is_rel'] : 0);
		$optionRunTime = Utility::getOption($this->arrRunTime, isset($data['banner_is_run_time'])? $data['banner_is_run_time'] : 0);

		return view('admin.banner.add',[
				'id'=>$id,
				'data'=>$data,
				'optionStatus'=>$optionStatus,
				'optionType'=>$optionType,
				'optionTarget'=>$optionTarget,
				'optionRel'=>$optionRel,
				'optionRunTime'=>$optionRunTime,
				'error'=>$this->error,
			]);
	}
	public function delete(){

        if(!in_array($this->permission_delete, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::addItem($id, 'Banner', CGlobal::FOLDER_BANNER, 'banner_id', 'banner_title', 'banner_image', '');
					Banner::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.banner');
	}
}