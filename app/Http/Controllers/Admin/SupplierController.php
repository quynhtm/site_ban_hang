<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Supplier;
use App\Http\Models\Trash;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

class SupplierController extends BaseAdminController{

	private $permission_view = 'supplier_view';
	private $permission_create = 'supplier_create';
	private $permission_edit = 'supplier_edit';
	private $permission_delete = 'supplier_delete';

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
		
		$search['supplier_title'] = addslashes(Request::get('supplier_title', ''));
		$search['supplier_status'] = (int)Request::get('supplier_status', -1);
		$search['field_get'] = 'supplier_id,supplier_title,supplier_mobile,supplier_email,supplier_address,supplier_created,supplier_order_no,supplier_status';
		
		$dataSearch = Supplier::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['supplier_status']);
		$messages = Utility::messages('messages');

		return view('admin.supplier.list',[
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
			$data = Supplier::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['supplier_status'])? $data['supplier_status'] : CGlobal::status_show);

		return view('admin.supplier.add',[
					'id'=>$id,
					'data'=>$data,
					'arrStatus'=>$this->arrStatus,
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
				'supplier_title'=>array('value'=>addslashes(Request::get('supplier_title')), 'require'=>1, 'messages'=>'Tên không được trống!'),
				'supplier_mobile'=>array('value'=>addslashes(Request::get('supplier_mobile', '')),'require'=>0),
				'supplier_email'=>array('value'=>addslashes(Request::get('supplier_email', '')),'require'=>0),
				'supplier_address'=>array('value'=>addslashes(Request::get('supplier_address', '')),'require'=>0),
				'supplier_intro'=>array('value'=>addslashes(Request::get('supplier_intro', '')),'require'=>0),
				'supplier_created'=>array('value'=>time(),'require'=>0),
				'supplier_order_no'=>array('value'=>(int)Request::get('supplier_order_no', 0),'require'=>0),
				'supplier_status'=>array('value'=>(int)Request::get('supplier_status', -1),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			Supplier::saveData($id, $dataSave);
			return Redirect::route('admin.supplier');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}

		$optionStatus = Utility::getOption($this->arrStatus, isset($data['supplier_status'])? $data['supplier_status'] : -1);
		return view('admin.supplier.add',[
					'id'=>$id,
					'data'=>$data,
					'arrStatus'=>$this->arrStatus,
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
					Trash::addItem($id, 'Supplier', '', 'supplier_id', 'supplier_title', '', '');
					Supplier::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.supplier');
	}
}