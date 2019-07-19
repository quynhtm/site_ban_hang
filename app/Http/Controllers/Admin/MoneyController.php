<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Money;
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

class MoneyController extends BaseAdminController{

	private $permission_view = 'money_view';
	private $permission_create = 'money_create';
	private $permission_edit = 'money_edit';
	private $permission_delete = 'money_delete';
	private $arrType = array(-1 => '--- Chọn kiểu ---', 1 => 'Nhập thêm', 2 => 'Chi tiêu');

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
		
		$search['money_title'] = addslashes(Request::get('money_title', ''));
		$search['money_type'] = (int)Request::get('money_type', -1);
		$search['field_get'] = '';
		
		$dataSearch = Money::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionType = Utility::getOption($this->arrType, $search['money_type']);
		$messages = Utility::messages('messages');

        return view('admin.money.list',[
                    'data'=>$dataSearch,
                    'total'=>$total,
                    'paging'=>$paging,
                    'arrType'=>$this->arrType,
                    'optionType'=>$optionType,
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
			$data = Money::getById($id);
		}
		$optionType = Utility::getOption($this->arrType, isset($data['money_type'])? $data['money_type'] : CGlobal::status_show);
        return view('admin.money.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'optionType'=>$optionType,
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
				'money_title'=>array('value'=>addslashes(Request::get('money_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'money_price'=>array('value'=>(float)Request::get('money_price', 0),'require'=>0),
				'money_type'=>array('value'=>(int)(Request::get('money_type')),'require'=>0),
				'money_infor'=>array('value'=>trim(Request::get('money_infor')),'require'=>0),
				'money_created'=>array('value'=>time(),'require'=>0),
		);

		if($id > 0){
			unset($dataSave['news_created']);
			$dataSave['money_updated']['value'] = time();
			$itemFirst = Money::getItemFirst($id);
			if(sizeof($itemFirst) > 0){
				$dataSave['money_id_first']['value'] = $itemFirst->money_id;
				if($dataSave['money_type']['value'] == 1){//Nhap quy
					$dataSave['money_total_price']['value'] = $itemFirst->money_total_price + (float)$dataSave['money_price']['value'];
				}else{
					$dataSave['money_total_price']['value'] = $itemFirst->money_total_price - (float)$dataSave['money_price']['value'];
				}
			}else{
				$dataSave['money_id_first']['value'] = 0;
				$dataSave['money_total_price']['value'] = $dataSave['money_price']['value'];
			}
		}else{
			$itemFirst = Money::getItemFirst($id);
			if($itemFirst){
				$dataSave['money_id_first']['value'] = $itemFirst->money_id;
				if($dataSave['money_type']['value'] == 1){//Nhap Quy
					$dataSave['money_total_price']['value'] = $itemFirst->money_total_price + $dataSave['money_price']['value'];
				}else{
					$dataSave['money_total_price']['value'] = $itemFirst->money_total_price - $dataSave['money_price']['value'];
				}
			}else{
				$dataSave['money_id_first']['value'] = 0;
				$dataSave['money_total_price']['value'] = $dataSave['money_price']['value'];
			}
		}
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;

			Money::saveData($id, $dataSave);
			return Redirect::route('admin.money');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}

		$optionType = Utility::getOption($this->arrType, isset($data['money_type'])? $data['money_type'] : CGlobal::status_show);

		return view('admin.money.add',[
			'id'=>$id,
			'data'=>$data,
			'optionType'=>$optionType,
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
					Trash::addItem($id, 'Money', '', 'money_id', 'money_title', '', '');
					Money::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.money');
	}
}