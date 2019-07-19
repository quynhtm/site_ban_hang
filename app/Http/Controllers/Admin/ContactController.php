<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Contact;
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
class ContactController extends BaseAdminController{

	private $permission_view = 'contact_view';
	private $permission_create = 'contact_create';
	private $permission_edit = 'contact_edit';
	private $permission_delete = 'contact_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Chưa duyệt', CGlobal::status_show => 'Đã duyệt');
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
		
		$search['contact_title'] = addslashes(Request::get('contact_title', ''));
		$search['contact_status'] = (int)Request::get('contact_status', -1);
		$search['field_get'] = 'contact_id,contact_title,contact_email,contact_phone,contact_address,contact_content,contact_created,contact_status';
		
		$dataSearch = Contact::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['contact_status']);
		$messages = Utility::messages('messages');

		return view('admin.contact.list',[
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

		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$data = array();
		if($id > 0) {
			$data = Contact::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['contact_status'])? $data['contact_status'] : CGlobal::status_hide);

		return view('admin.contact.add',[
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

		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$id_hiden = (int)Request::get('id_hiden', 0);
		$data = array();
		
		$dataSave = array(
				'contact_title'=>array('value'=>addslashes(Request::get('contact_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'contact_phone'=>array('value'=>addslashes(Request::get('contact_phone')),'require'=>0),
				'contact_email'=>array('value'=>addslashes(Request::get('contact_email')),'require'=>0),
				'contact_address'=>array('value'=>addslashes(Request::get('contact_address')),'require'=>0),
				'contact_content'=>array('value'=>addslashes(Request::get('contact_content')),'require'=>0),
				'contact_created'=>array('value'=>time()),
				'contact_status'=>array('value'=>(int)Request::get('contact_status', -1),'require'=>0),	
		);
		
		if($id > 0){
			unset($dataSave['contact_created']);
		}
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			Contact::saveData($id, $dataSave);
			return Redirect::route('admin.contact');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['contact_status'])? $data['contact_status'] : CGlobal::status_hide);

		return view('admin.contact.add',[
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
					Trash::addItem($id, 'Contact', '', 'contact_id', 'contact_title', '', '');
					Contact::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.contact');
	}
}