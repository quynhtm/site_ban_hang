<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Member;
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

class MemberController extends BaseAdminController{

	private $permission_view = 'member_view';
	private $permission_create = 'member_create';
	private $permission_edit = 'member_edit';
	private $permission_delete = 'member_delete';

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
		
		$search['member_rid'] = (int)Request::get('member_rid', -1);
		$search['member_mail'] = addslashes(Request::get('member_mail', ''));
		$search['member_status'] = (int)Request::get('member_status', -1);
		$search['field_get'] = '';
		
		$dataSearch = Member::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['member_status']);
		$messages = Utility::messages('messages');

		return view('admin.member.list',[
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
			$data = Member::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status'] : CGlobal::status_show);
		return view('admin.member.add',[
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
		
		$session_user = $this->user;

		$id_hiden = (int)Request::get('id_hiden', 0);
		$data = array();
		
		$dataSave = array(
				'member_mail'=>array('value'=>addslashes(Request::get('member_mail')),'require'=>1, 'messages'=>'Email đăng nhập không được trống!'),
				'member_pass'=>array('value'=>addslashes(Request::get('member_pass')),'require'=>1, 'messages'=>'Mật khẩu không được trống!'),
				're_member_pass'=>array('value'=>addslashes(Request::get('re_member_pass')),'require'=>1, 'messages'=>'Nhập lại mật khẩu không được trống!'),
				'member_full_name'=>array('value'=>addslashes(Request::get('member_full_name')),'require'=>1, 'messages'=>'Tên hiển thị không được trống!'),
				'member_phone'=>array('value'=>addslashes(Request::get('member_phone')),'require'=>0),
				'member_address'=>array('value'=>addslashes(Request::get('member_address')),'require'=>0),
				'member_created'=>array('value'=>time(),'require'=>0),
				'member_status'=>array('value'=>(int)Request::get('member_status', -1),'require'=>0),
		);
		foreach($dataSave as $key=>$val){
			$data[$key] = $val['value'];
		}
		//Check User
		$error = '';
		$id = ($id == 0) ? $id_hiden : $id;
		$mail = $dataSave['member_mail']['value'];
		$pass = $dataSave['member_pass']['value'];
		$repass = $dataSave['re_member_pass']['value'];
		
		if($id > 0){//Edit
			unset($dataSave['member_created']);
			
			if($session_user['user_rid'] == 1 || $session_user['user_rid'] == 2){//Admin || Manager
				
				if($mail != ''){
					$checkMail = ValidForm::checkRegexEmail($mail);
					if(!$checkMail) {
						$error .= 'Email không đúng định dạng!'.'<br/>';
					}
				}
				if($pass != '' && ($pass === $repass)){
					$check_valid_pass = ValidForm::checkRegexPass($pass, 5);
					if($check_valid_pass){
						$hash_pass = Member::encode_password($pass);
						$dataSave['member_pass']['value'] = $hash_pass;
						unset($dataSave['re_member_pass']);
					}else{
						$error .= 'Mật không được ít hơn 5 ký tự và không được có dấu!'.'<br/>';
					}
				}
				
				if($pass == '' && $repass == ''){
					unset($dataSave['member_pass']);
					unset($dataSave['re_member_pass']);
				}elseif($pass != $repass){
					$error .= 'Mật khẩu không khớp!'.'<br/>';
				}
				
				$check = Member::getMemberByCond($id, $mail);
				
				if(empty($check)){
					Utility::messages('messages', 'Thành viên này ko tồn tại!', 'error');
					return Redirect::route('admin.member');
				}
			}else{
				return Redirect::route('admin.member');
			}
		}else{//Add
			
			if($session_user['user_rid'] != 1 && $session_user['user_rid'] != 2){
				Utility::messages('messages', 'Bạn không có quyền thêm thành viên mới!', 'error');
				return Redirect::route('admin.member');
			}
			
			$checkMail = ValidForm::checkRegexEmail($mail);
			
			if(!$checkMail) {
				$error .= 'Email không đúng định dạng!'.'<br/>';
			}
			
			if($pass !='' && ($pass === $repass)){
				$check_valid_pass = ValidForm::checkRegexPass($pass, 5);
				if($check_valid_pass){
					$hash_pass = Member::encode_password($pass);
					$dataSave['member_pass']['value'] = $hash_pass;
					unset($dataSave['re_member_pass']);
				}else{
					$error .= 'Mật không được ít hơn 5 ký tự và không được có dấu!'.'<br/>';
				}
			}elseif($pass != $repass){
				$error .= 'Mật khẩu không khớp!'.'<br/>';
			}
			
			//Check Member Exists
			$check = Member::getMemberByEmail($mail);
			if(sizeof($check) != 0){
				$this->error .= 'Email đăng nhập này đã tồn tại!';
			}
		}
		//End Check User
		
		$this->error .= ValidForm::validInputData($dataSave);
		$this->error .= $error;
		if($this->error == ''){
			Member::saveData($id, $dataSave);
			return Redirect::route('admin.member');
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status'] : -1);

		return view('admin.member.add',[
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

		$session_user = $this->user;
		if($session_user['user_rid'] != 1 && $session_user['user_rid'] != 2){
			Utility::messages('messages', 'Bạn không có quyền xóa thành viên!', 'error');
			return Redirect::route('admin.member');
		}
		
		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::addItem($id, 'Member', '', 'member_id', 'member_mail', '', '');
					Member::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.member');
	}
}