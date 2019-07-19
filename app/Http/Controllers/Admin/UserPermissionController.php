<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\UserPermission;
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

class UserPermissionController extends BaseAdminController{

	private $permission_view = 'userPermission_view';
	private $permission_create = 'userPermission_create';
	private $permission_edit = 'userPermission_edit';
	private $permission_delete = 'userPermission_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $error = '';
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadJS('libs/dragsort/jquery.dragsort.js', CGlobal::$postHead);
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
		
		$search['permission_title'] = addslashes(Request::get('permission_title', ''));
		$search['permission_group'] = addslashes(Request::get('permission_group', ''));
		$search['permission_code'] = addslashes(Request::get('permission_code', ''));
		$search['permission_status'] = (int)Request::get('permission_status', -1);
		$search['field_get'] = '';
		
		$dataSearch = UserPermission::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['permission_status']);
		$messages = Utility::messages('messages');

		return view('admin.userPermission.list',[
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
			$data = UserPermission::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['permission_status'])? $data['permission_status'] : CGlobal::status_show);

		return view('admin.userPermission.add',[
					'id'=>$id,
					'data'=>$data,
					'optionStatus'=>$optionStatus,
					'arrStatus'=>$this->arrStatus,
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
				'permission_title'=>array('value'=>addslashes(Request::get('permission_title')), 'require'=>1, 'messages'=>'Tên không được trống!'),
				'permission_group'=>array('value'=>addslashes(Request::get('permission_group')),'require'=>1, 'messages'=>'Nhóm tên không được trống!'),
				'permission_code'=>array('value'=>Request::get('permission_code', array()),'require'=>1, 'messages'=>'Mã không được trống!'),
				'permission_status'=>array('value'=>(int)Request::get('permission_status', -1),'require'=>0),
		);

		$this->error = ValidForm::validInputData($dataSave);

		$checkExists = UserPermission::checkPermissionExists($dataSave['permission_code']['value'], $id);
		if(sizeof($checkExists) > 0){
			$this->error .= 'Quyền' . $dataSave['permission_code']['value'] . ' đã tồn tại.<br/>';
		}

		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			UserPermission::saveData($id, $dataSave);
			return Redirect::route('admin.permission');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['permission_status'])? $data['permission_status'] : -1);

		return view('admin.userPermission.add',[
				'id'=>$id,
				'data'=>$data,
				'optionStatus'=>$optionStatus,
				'arrStatus'=>$this->arrStatus,
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
					//Trash::addItem($id, 'UserPermission', '', 'permission_id', 'permission_title', '', '');
					//UserPermission::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.permission');
	}
}