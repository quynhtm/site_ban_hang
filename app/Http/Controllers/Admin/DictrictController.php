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
use App\Library\PHPDev\FuncLib;
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

class DictrictController extends BaseAdminController{

	private $permission_view = 'dictrict_view';
	private $permission_create = 'dictrict_create';
	private $permission_edit = 'dictrict_edit';
	private $permission_delete = 'dictrict_delete';
	private $permission_ajax = 'dictrict_ajax';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $arrProvice = array();
	private $error = '';
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);

		$listProvice = Provice::getAllProvice(array(), 0);
		$this->arrProvice = Provice::arrProvice($listProvice);
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
		
		$search['provice_id'] = addslashes(Request::get('provice_id', -1));
		$search['dictrict_title'] = addslashes(Request::get('dictrict_title', ''));
		$search['dictrict_status'] = (int)Request::get('dictrict_status', -1);
		$search['field_get'] = '';
		
		$dataSearch = Dictrict::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['dictrict_status']);
		$optionProvice = Utility::getOption($this->arrProvice, $search['provice_id']);
		$messages = Utility::messages('messages');

		return view('admin.dictrict.list',[
					'stt'=>$stt,
					'data'=>$dataSearch,
					'total'=>$total,
					'paging'=>$paging,
					'arrStatus'=>$this->arrStatus,
					'optionStatus'=>$optionStatus,
					'arrProvice'=>$this->arrProvice,
					'optionProvice'=>$optionProvice,
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
			$data = Dictrict::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['dictrict_status'])? $data['dictrict_status'] : CGlobal::status_show);
		$optionProvice = Utility::getOption($this->arrProvice, isset($data['provice_id'])? $data['provice_id'] : -1);

        return view('admin.dictrict.add',[
                'id'=>$id,
                'data'=>$data,
                'optionStatus'=>$optionStatus,
                'optionProvice'=>$optionProvice,
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
				'dictrict_title'=>array('value'=>addslashes(Request::get('dictrict_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'dictrict_order_no'=>array('value'=>(int)addslashes(Request::get('dictrict_order_no')),'require'=>0),
				'dictrict_status'=>array('value'=>(int)Request::get('dictrict_status', -1),'require'=>0),
				'provice_id'=>array('value'=>(int)Request::get('provice_id', -1),'require'=>0),
				'dictrict_num'=>array('value'=>Request::get('dictrict_num', -1),'require'=>0),
				'dictrict_num_gold_ship'=>array('value'=>Request::get('dictrict_num_gold_ship', -1),'require'=>0),
				'dictrict_num_vnpost'=>array('value'=>Request::get('dictrict_num_vnpost', -1),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
            Dictrict::saveData($id, $dataSave);
			return Redirect::route('admin.dictrict');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['dictrict_status'])? $data['dictrict_status'] : -1);
		$optionProvice = Utility::getOption($this->arrProvice, isset($data['provice_id'])? $data['provice_id'] : -1);

        return view('admin.dictrict.add',[
                'id'=>$id,
                'data'=>$data,
                'optionStatus'=>$optionStatus,
                'optionProvice'=>$optionProvice,
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
					Trash::addItem($id, 'Dictrict', '', 'dictrict_id', 'dictrict_title', '', '', $this->user['user_id'], $this->user['user_name']);
					Dictrict::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.dictrict');
	}
	public function ajaxGetDictrictByProvice(){
		if(!in_array($this->permission_ajax, $this->permission)){
			Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
			return Redirect::route('admin.dashboard');
		}

		$proviceId = (int)Request::get('proviceId', '-1');
		$dictrictId = (int)Request::get('dictrictId', '-1');
		$data = '';
		if($proviceId > -1){
			$listDictrict = Dictrict::getAllDictrict(array(), 0, $proviceId);
			$arrDictrict = Dictrict::arrDictrict($listDictrict);
			if(!empty($arrDictrict)){
				foreach($arrDictrict as $key=>$val){
					if($dictrictId == $key){
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