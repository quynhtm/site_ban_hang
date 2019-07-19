<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Provice;
use App\Http\Models\Dictrict;
use App\Http\Models\Ward;
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

class WardController extends BaseAdminController{

	private $permission_view = 'ward_view';
	private $permission_create = 'ward_create';
	private $permission_edit = 'ward_edit';
	private $permission_delete = 'ward_delete';
	private $permission_ajax = 'ward_ajax';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $arrProvice = array(-1 => '--Chọn--');
	private $arrDictrict = array(-1 => '--Chọn--');
	private $error = '';
	
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);

		$listProvice = Provice::getAllProvice(array(), 0);
		$this->arrProvice = Provice::arrProvice($listProvice);
		
		$listDictrict = Dictrict::getAllDictrict(array(), 0);
		$this->arrDictrict = Dictrict::arrDictrict($listDictrict);
		
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
		$offset = $stt = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['provice_id'] = (int)Request::get('provice_id', -1);
		$search['dictrict_id'] = (int)Request::get('dictrict_id', -1);
		$search['ward_title'] = addslashes(Request::get('ward_title', ''));
		$search['ward_status'] = (int)Request::get('ward_status', -1);
		$search['field_get'] = '';
		
		$dataSearch = Ward::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['ward_status']);
		$optionProvice = Utility::getOption($this->arrProvice, $search['provice_id']);
		$optionDictrict = Utility::getOption($this->arrDictrict, $search['dictrict_id']);
		$messages = Utility::messages('messages');

		return view('admin.ward.list',[
					'stt'=>$stt,
					'data'=>$dataSearch,
					'total'=>$total,
					'paging'=>$paging,
					'arrStatus'=>$this->arrStatus,
					'optionStatus'=>$optionStatus,
					'arrProvice'=>$this->arrProvice,
					'optionProvice'=>$optionProvice,
					'arrDictrict'=>$this->arrDictrict,
					'optionDictrict'=>$optionDictrict,
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
			$data = Ward::getById($id);
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['ward_status'])? $data['ward_status'] : CGlobal::status_show);
		$optionProvice = Utility::getOption($this->arrProvice, isset($data['provice_id'])? $data['provice_id'] : -1);
		$optionDictrict = Utility::getOption($this->arrDictrict, isset($data['dictrict_id'])? $data['dictrict_id'] : -1);

        return view('admin.ward.add',[
                'id'=>$id,
                'data'=>$data,
                'optionStatus'=>$optionStatus,
                'optionProvice'=>$optionProvice,
                'optionDictrict'=>$optionDictrict,
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
				'ward_title'=>array('value'=>addslashes(Request::get('ward_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'ward_order_no'=>array('value'=>(int)addslashes(Request::get('ward_order_no')),'require'=>0),
				'ward_status'=>array('value'=>(int)Request::get('ward_status', -1),'require'=>0),
				
				'provice_id'=>array('value'=>(int)addslashes(Request::get('provice_id')),'require'=>0),
				'dictrict_id'=>array('value'=>(int)addslashes(Request::get('dictrict_id')),'require'=>0),
				
				'ward_num'=>array('value'=>addslashes(Request::get('ward_num')),'require'=>0),
				'ward_num_gold_ship'=>array('value'=>addslashes(Request::get('ward_num_gold_ship')),'require'=>0),
				'ward_num_vnpost'=>array('value'=>addslashes(Request::get('ward_num_vnpost')),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
            Ward::saveData($id, $dataSave);
			return Redirect::route('admin.ward');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['ward_status'])? $data['ward_status'] : -1);
        $optionProvice = Utility::getOption($this->arrProvice, isset($data['provice_id'])? $data['provice_id'] : -1);
        $optionDictrict = Utility::getOption($this->arrDictrict, isset($data['dictrict_id'])? $data['dictrict_id'] : -1);

        return view('admin.ward.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'optionStatus'=>$optionStatus,
                    'optionProvice'=>$optionProvice,
                    'optionDictrict'=>$optionDictrict,
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
					Trash::addItem($id, 'Ward', '', 'ward_id', 'ward_title', '', '', $this->user['user_id'], $this->user['user_name']);
					Ward::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.ward');
	}
	function ajaxGetWardByDictrict(){

		if(!in_array($this->permission_ajax, $this->permission)){
			Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
			return Redirect::route('admin.dashboard');
		}

		$dictrictId = (int)Request::get('dictrictId', '-1');
		$wardId = (int)Request::get('wardId', '-1');
		$data = '';
		if($dictrictId > -1){
			$listDictrict = Ward::getAllward(array(), 0, $dictrictId);
			$arrDictrict = Ward::arrward($listDictrict);
			if(!empty($arrDictrict)){
				foreach($arrDictrict as $key=>$val){
					if($wardId == $key){
						$selected = 'selected="selected"';
					}else{
						$selected = '';
					}
					$data .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
				}
			}
		}
		$html = json_encode($data);
		echo $html;die;
	}
}