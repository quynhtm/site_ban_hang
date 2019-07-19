<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\NickSupport;
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

class NickSupportController extends BaseAdminController{

	private $permission_view = 'nickSupport_view';
	private $permission_create = 'nickSupport_create';
	private $permission_edit = 'nickSupport_edit';
	private $permission_delete = 'nickSupport_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $error = '';
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
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
		
		$search['title'] = addslashes(Request::get('title', ''));
		$search['status'] = (int)Request::get('status', -1);
		$search['field_get'] = '';
		
		$dataSearch = NickSupport::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['status']);
		$messages = Utility::messages('messages');

		return view('admin.nicksupport.list',[
					'data'=>$dataSearch,
					'total'=>$total,
					'paging'=>$paging,
					'arrStatus'=>$this->arrStatus,
					'optionStatus'=>$optionStatus,
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
			$data = NickSupport::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['status'])? $data['status'] : CGlobal::status_show);

		return view('admin.nicksupport.add',[
					'id'=>$id,
					'data'=>$data,
					'optionStatus'=>$optionStatus,
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
				'title'=>array('value'=>addslashes(Request::get('title')), 'require'=>1, 'messages'=>'Tên không được trống!'),
				'yahoo'=>array('value'=>addslashes(Request::get('yahoo')),'require'=>0),
				'skyper'=>array('value'=>addslashes(Request::get('skyper')),'require'=>0),
				'phone'=>array('value'=>addslashes(Request::get('phone')),'require'=>0),
				'mobile'=>array('value'=>addslashes(Request::get('mobile', '')),'require'=>0),
				'email'=>array('value'=>addslashes(Request::get('email', '')),'require'=>0),
				'created'=>array('value'=>time(),'require'=>0),
				'order_no'=>array('value'=>(int)Request::get('order_no', 0),'require'=>0),
				'status'=>array('value'=>(int)Request::get('status', -1),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			NickSupport::saveData($id, $dataSave);
			return Redirect::route('admin.nicksupport');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['status'])? $data['status'] : -1);
		return view('admin.nicksupport.add',[
					'id'=>$id,
					'data'=>$data,
					'optionStatus'=>$optionStatus,
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
					Trash::addItem($id, 'NickSupport', '', 'id', 'title', '', '');
					NickSupport::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.nicksupport');
	}
}