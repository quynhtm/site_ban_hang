<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\User;
use App\Http\Models\UserRole;
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

class UserController extends BaseAdminController{

	private $permission_view = 'user_view';
	private $permission_create = 'user_create';
	private $permission_edit = 'user_edit';
	private $permission_delete = 'user_delete';

	private $arrRole = array(-1 => '--Chọn chức vụ--');
	private $arrStatus = array(-1 => '--Chọn trạng thái--', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $error = '';
	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);

		$dataSearch['field_get'] = 'role_id,role_title';
		$arrRole = UserRole::getAllRole($dataSearch, $limit=1000);
		if(!empty($arrRole)){
			foreach($arrRole as $role){
				$this->arrRole[$role->role_id] =  $role->role_title;
			}
		}
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
		
		$search['user_rid'] = (int)Request::get('user_rid', -1);
		$search['user_name'] = addslashes(Request::get('user_name', ''));
		$search['user_status'] = (int)Request::get('user_status', -1);
		$search['field_get'] = '';
		//User View Only
		if(isset($this->user)){
			$session_user = $this->user;
			if($session_user['user_rid'] != CGlobal::rid_admin && $session_user['user_rid'] != CGlobal::rid_manager){
				$search['user_id'] = isset($session_user['user_id']) ? $session_user['user_id'] : -1;
			}
		}
		//User Member View Only
		$dataSearch = User::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

		$optionStatus = Utility::getOption($this->arrStatus, $search['user_status']);
		$optionRole = Utility::getOption($this->arrRole, $search['user_rid']);
		$messages = Utility::messages('messages');

		return view('admin.user.list',[
					'data'=>$dataSearch,
					'total'=>$total,
					'paging'=>$paging,
					'arrStatus'=>$this->arrStatus,
					'optionStatus'=>$optionStatus,
					'arrRole'=>$this->arrRole,
					'optionRole'=>$optionRole,
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
		$session_user = $this->user;
		if($id > 0) {
			$data = User::getById($id);
			if($session_user['user_rid'] != CGlobal::rid_admin && $session_user['user_rid'] != CGlobal::rid_manager){
				if($session_user['user_id'] != $id){
					Utility::messages('messages', 'Bạn không có quyền sửa người dùng!', 'error');
					return Redirect::route('admin.user');
				}	
			}
		}else{
			if($session_user['user_rid'] != CGlobal::rid_admin && $session_user['user_rid'] != CGlobal::rid_manager){
				Utility::messages('messages', 'Bạn không có quyền thêm người dùng mới!', 'error');
				return Redirect::route('admin.user');
			}
		}

		$arrRoleUser = UserRole::getAllRole(array());
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status'] : CGlobal::status_show);

		if($session_user['user_rid'] == CGlobal::rid_admin || $session_user['user_rid'] == CGlobal::rid_manager){
			$theme = 'admin.user.add';
		}else{
			$theme = 'admin.user.addOther';
		}

        return view($theme,[
                'id'=>$id,
                'data'=>$data,
                'optionStatus'=>$optionStatus,
                'arrRoleUser'=>$arrRoleUser,
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
		$session_user = $this->user;
		
		$dataSave = array(
				'user_name'=>array('value'=>addslashes(Request::get('user_name')), 'require'=>1, 'messages'=>'Tên không được trống!'),
				'user_pass'=>array('value'=>addslashes(Request::get('user_pass')),'require'=>1, 'messages'=>'Mật khẩu không được trống!'),
				're_user_pass'=>array('value'=>addslashes(Request::get('re_user_pass')),'require'=>1, 'messages'=>'Nhập lại mật khẩu không được trống!'),
				'user_full_name'=>array('value'=>addslashes(Request::get('user_full_name')),'require'=>1, 'messages'=>'Tên hiển thị không được trống!'),
				'user_phone'=>array('value'=>addslashes(Request::get('user_phone')),'require'=>0),
				'user_mail'=>array('value'=>addslashes(Request::get('user_mail')),'require'=>0),
				'user_created'=>array('value'=>time(),'require'=>0),
				'user_status'=>array('value'=>(int)Request::get('user_status', -1),'require'=>0),
				'user_rid'=>array('value'=>Request::get('user_rid', array()),'require'=>0),
		);
		if(!empty($dataSave['user_rid']['value']) > 0){
			$dataSave['user_rid']['value'] = implode(',', $dataSave['user_rid']['value']);
		}
		foreach($dataSave as $key=>$val){
			$data[$key] = $val['value'];
		}
		//Check User
		$error = '';
		$id = ($id == 0) ? $id_hiden : $id;
		$name = $dataSave['user_name']['value'];
		$pass = $dataSave['user_pass']['value'];
		$repass = $dataSave['re_user_pass']['value'];
		
		if($id > 0){//Edit
			unset($dataSave['user_created']);
			
			if($session_user['user_rid'] == CGlobal::rid_admin || $session_user['user_rid'] == CGlobal::rid_manager){
				if($name != ''){
					$check_valid_name = ValidForm::checkRegexName($name);
					if(!$check_valid_name){
						$error .= 'Tên không được có dấu!'.'<br/>';
					}
				}
				if($pass != '' && ($pass === $repass)){
					$check_valid_pass = ValidForm::checkRegexPass($pass, 6);
					if($check_valid_pass){
						$hash_pass = User::encode_password($pass);
						$dataSave['user_pass']['value'] = $hash_pass;
						unset($dataSave['re_user_pass']);
					}else{
						$error .= 'Mật không được ít hơn 6 ký tự và không được có dấu!'.'<br/>';
					}
				}
				
				if($pass == '' && $repass == ''){
					unset($dataSave['user_pass']);
					unset($dataSave['re_user_pass']);
				}elseif($pass != $repass){
					$error .= 'Mật khẩu không khớp!'.'<br/>';
				}
				
				$check = User::getUserByCond($id, $name);
				if(empty($check)){
					Utility::messages('messages', 'Người dùng này ko tồn tại!', 'error');
					return Redirect::route('admin.user');
				}
			}else{
				if($session_user['user_id'] == $id && $session_user['user_name'] == $name){
					unset($dataSave['user_status']);
					unset($dataSave['user_rid']);

					if($name != ''){
						$check_valid_name = ValidForm::checkRegexName($name);
						if(!$check_valid_name){
							$error .= 'Tên không được có dấu!'.'<br/>';
						}
					}
					if($pass != '' && ($pass === $repass)){
						$check_valid_pass = ValidForm::checkRegexPass($pass, 6);
						if($check_valid_pass){
							$hash_pass = User::encode_password($pass);
							$dataSave['user_pass']['value'] = $hash_pass;
							unset($dataSave['re_user_pass']);
						}else{
							$error .= 'Mật không được ít hơn 6 ký tự và không được có dấu!'.'<br/>';
						}
					}
					
					if($pass == '' && $repass == ''){
						unset($dataSave['user_pass']);
						unset($dataSave['re_user_pass']);
					}elseif($pass != $repass){
						$error .= 'Mật khẩu không khớp!'.'<br/>';
					}
					
				}else{
					Utility::messages('messages', 'Bạn không có quyền sửa người dùng này!', 'error'); 
					return Redirect::route('admin.user');
				}
			}
			unset($dataSave['user_name']);
		}else{//Add
			
			if($session_user['user_rid'] != CGlobal::rid_admin && $session_user['user_rid'] != CGlobal::rid_manager){
				Utility::messages('messages', 'Bạn không có quyền thêm người dùng mới!', 'error');
				return Redirect::route('admin.user');
			}
			
			$check_valid_name = ValidForm::checkRegexName($name);
			if(!$check_valid_name){
				$error .= 'Tên không được có dấu!'.'<br/>';
			}
			
			if($pass !='' && ($pass === $repass)){
				$check_valid_pass = ValidForm::checkRegexPass($pass, 6);
				if($check_valid_pass){
					$hash_pass = User::encode_password($pass);
					$dataSave['user_pass']['value'] = $hash_pass;
					unset($dataSave['re_user_pass']);
				}else{
					$error .= 'Mật không được ít hơn 6 ký tự và không được có dấu!'.'<br/>';
				}
			}elseif($pass != $repass){
				$error .= 'Mật khẩu không khớp!'.'<br/>';
			}
			
			//Check User Exists
			$check = User::getUserByName($name);
			if(sizeof($check) != 0){
				$this->error .= 'Tên đăng nhập này đã tồn tại!';
			}
		}
		//End Check User
		
		$this->error .= ValidForm::validInputData($dataSave);
		$this->error .= $error;
		if($this->error == ''){
		    User::saveData($id, $dataSave);
			return Redirect::route('admin.user');
		}

		$arrRoleUser = UserRole::getAllRole(array());
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status'] : -1);

        if($session_user['user_rid'] == CGlobal::rid_admin || $session_user['user_rid'] == CGlobal::rid_manager){
            $theme = 'admin.user.add';
        }else{
            $theme = 'admin.user.addOther';
        }

        return view($theme,[
            'id'=>$id,
            'data'=>$data,
            'optionStatus'=>$optionStatus,
            'optionRole'=>$optionRole,
			'arrRoleUser'=>$arrRoleUser,
            'error'=>$this->error,
        ]);
	}
	public function delete(){

		if(!in_array($this->permission_delete, $this->permission)){
			Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
			return Redirect::route('admin.dashboard');
		}

		$session_user = $this->user;
		
		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					if($session_user['user_id'] != $id){
						Trash::addItem($id, 'User', '', 'user_id', 'user_name', '', '', $this->user['user_id'], $this->user['user_name']);
						User::deleteId($id);
					}
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.user');
	}
}