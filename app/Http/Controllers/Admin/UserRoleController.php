<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\UserPermission;
use App\Http\Models\UserRole;
use App\Http\Models\UserRolePermission;
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

class UserRoleController extends BaseAdminController{

	private $permission_view = 'userRole_view';
	private $permission_create = 'userRole_create';
	private $permission_edit = 'userRole_edit';
	private $permission_delete = 'userRole_delete';

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
		
		$search['role_title'] = addslashes(Request::get('role_title', ''));
		$search['role_status'] = (int)Request::get('role_status', -1);
		$search['field_get'] = '';
		
		$dataSearch = UserRole::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

		if(sizeof($dataSearch) > 0){
			$aryRoleId = array();
			foreach ($dataSearch as $val) {
				$aryRoleId[] = $val->role_id;
			}
			if (!empty($aryRoleId)) {
				$aryPermission = UserRolePermission::getListPermissionByRoleId($aryRoleId);
				if (!empty($aryPermission)) {
					foreach ($dataSearch as $k => $v) {
						$items = $v;
						foreach ($aryPermission as $val) {
							if ($v->role_id == $val->role_id) {
								$item = isset($v->permissions) ? $v->permissions : array();
								$count = isset($v->countPermission) ? $v->countPermission : 0;
								$item[] = $val;
								$count++;
								$items->permissions = $item;
								$items->countPermission = $count;
							}
						}
						$dataSearch[$k] = $items;
					}
				}
			}

		}

		$optionStatus = Utility::getOption($this->arrStatus, $search['role_status']);
		$messages = Utility::messages('messages');

        return view('admin.userRole.list',[
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
		$data['strPermission'] = array();

		if($id > 0) {
			$data = UserRole::getById($id);
			$dataPermission = UserRolePermission::getListPermissionByRoleId(array($id));
			$aryPermission = array();
			if($dataPermission) {
				foreach($dataPermission as $per) {
					$aryPermission[] = $per->permission_id;
				}
			}
			$data['strPermission'] = $aryPermission;
		}

		$listPermission = UserPermission::getListPermission();
		$arrPermissionByGroup = $this->buildArrayPermissionByGroup($listPermission);

		$optionStatus = Utility::getOption($this->arrStatus, isset($data->role_status) ? $data->role_status : CGlobal::status_show);

        return view('admin.userRole.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'optionStatus'=>$optionStatus,
                    'arrStatus'=>$this->arrStatus,
                    'arrPermissionByGroup'=>$arrPermissionByGroup,
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
				'role_title'=>array('value'=>addslashes(Request::get('role_title')), 'require'=>1, 'messages'=>'Tên không được trống!'),
				'role_order_no'=>array('value'=>(int)Request::get('role_order_no', 0),'require'=>0),
				'role_status'=>array('value'=>(int)Request::get('role_status', -1),'require'=>0),
		);

		$listPermission = UserPermission::getListPermission();
		$arrPermissionByGroup = $this->buildArrayPermissionByGroup($listPermission);
		$arrPermission = Request::get('permission_id', array());
		$data['strPermission'] = $arrPermission;

		$this->error = ValidForm::validInputData($dataSave);
		$checkExists = UserRole::checkRoleExists($dataSave['role_title']['value'], $id);
		if(sizeof($checkExists) > 0){
			$this->error .= 'Nhóm ' . $dataSave['role_title']['value'] . ' đã tồn tại.<br/>';
		}

		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
            UserRole::saveData($id, $dataSave, $arrPermission);
			return Redirect::route('admin.role');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['role_status'])? $data['role_status'] : -1);

        return view('admin.userRole.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'arrPermissionByGroup'=>$arrPermissionByGroup,
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
					//Trash::addItem($id, 'UserRole', '', 'role_id', 'role_title', '', '');
                    //UserRole::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.role');
	}
	private function buildArrayPermissionByGroup($listPermission){
		$arrPermissionByGroup = array();
		if (!empty($listPermission)) {
			foreach ($listPermission as $permission) {
				$arrPermissionByGroup[$permission['permission_group']][] = $permission;
			}
		}
		return $arrPermissionByGroup;
	}
}