<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Provice;
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

class ProviceController extends BaseAdminController{

	private $permission_view = 'provice_view';
	private $permission_create = 'provice_create';
	private $permission_edit = 'provice_edit';
	private $permission_delete = 'provice_delete';

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
		$offset = $stt = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['provice_title'] = addslashes(Request::get('provice_title', ''));
		$search['provice_status'] = (int)Request::get('provice_status', -1);
		$search['field_get'] = 'provice_id,provice_title,provice_order_no,provice_status,provice_created,provice_num,provice_num_gold_ship,provice_num_vnpost';
		
		$dataSearch = Provice::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['provice_status']);
		$messages = Utility::messages('messages');

        return view('admin.provice.list',[
                    'stt'=>$stt,
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
			$data = Provice::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['provice_status'])? $data['provice_status'] : CGlobal::status_show);
        return view('admin.provice.add',[
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
				'provice_title'=>array('value'=>addslashes(Request::get('provice_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'provice_order_no'=>array('value'=>(int)addslashes(Request::get('provice_order_no')),'require'=>0),
				'provice_status'=>array('value'=>(int)Request::get('provice_status', -1),'require'=>0),
				'provice_num'=>array('value'=>addslashes(Request::get('provice_num')),'require'=>0),
				'provice_num_gold_ship'=>array('value'=>addslashes(Request::get('provice_num_gold_ship')),'require'=>0),
				'provice_num_vnpost'=>array('value'=>addslashes(Request::get('provice_num_vnpost')),'require'=>0),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
            Provice::saveData($id, $dataSave);
			return Redirect::route('admin.provice');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['provice_status'])? $data['provice_status'] : -1);

        return view('admin.provice.add',[
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
					Trash::addItem($id, 'Provice', '', 'provice_id', 'provice_title', '', '', $this->user['user_id'], $this->user['user_name']);
					Provice::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.provice');
	}
}