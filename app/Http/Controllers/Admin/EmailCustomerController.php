<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\EmailCustomer;
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

class EmailCustomerController extends BaseAdminController{

	private $permission_view = 'emailCustomer_view';
	private $permission_create = 'emailCustomer_create';
	private $permission_edit = 'emailCustomer_edit';
	private $permission_delete = 'emailCustomer_delete';
	private $permission_ajax = 'emailCustomer_ajax';

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
		
		$search['customer_phone'] = Request::get('customer_phone', '');
		$search['field_get'] = '';
		
		$dataSearch = EmailCustomer::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$messages = Utility::messages('messages');

        return view('admin.emailCustomer.list',[
                    'data'=>$dataSearch,
                    'total'=>$total,
                    'paging'=>$paging,
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
			$data = EmailCustomer::getById($id);
		}

        return view('admin.emailCustomer.add',[
                    'id'=>$id,
                    'data'=>$data,
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
				'customer_full_name'=>array('value'=>addslashes(Request::get('customer_full_name', '')),'require'=>0),
				'customer_email'=>array('value'=>addslashes(Request::get('customer_email')), 'require'=>0),
				'customer_phone'=>array('value'=>addslashes(Request::get('customer_phone', '')),'require'=>0),
				'customer_address'=>array('value'=>addslashes(Request::get('customer_address', '')),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			EmailCustomer::saveData($id, $dataSave);
			return Redirect::route('admin.emailCustomer');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}

        return view('admin.emailCustomer.add',[
                    'id'=>$id,
                    'data'=>$data,
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
					Trash::addItem($id, 'EmailCustomer', '', 'customer_id', 'customer_email', '', '', $this->user['user_id'], $this->user['user_name']);
					EmailCustomer::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.emailCustomer');
	}

	public function ajaxGetOrderCustomer(){

		if(!in_array($this->permission_ajax, $this->permission)){
			Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
			return Redirect::route('admin.dashboard');
		}

		$customer_phone = Request::get('orderPhone', '');
		$result = array(
				'customer_address'=>'',
				'customer_full_name'=>'',
				'customer_name_facebook'=>'',
				'customer_link_facebook'=>'',
				'customer_provice_id'=>'',
				'customer_dictrict_id'=>'',
				'customer_ward_id'=>'',
				'customer_email'=>'',
		);
		if($customer_phone != ''){
			$data = EmailCustomer::getCustomerByPhone($customer_phone);
			if(sizeof($data) > 0) {
				$result = array(
					'customer_address' => $data->customer_address,
					'customer_full_name' => $data->customer_full_name,
					'customer_name_facebook' => $data->customer_name_facebook,
					'customer_link_facebook' => $data->customer_link_facebook,
					'customer_provice_id' => $data->customer_provice_id,
					'customer_dictrict_id' => $data->customer_dictrict_id,
					'customer_ward_id' => $data->customer_ward_id,
					'customer_email' => $data->customer_email,
				);
			}
		}
		echo json_encode($result);die;
	}
}