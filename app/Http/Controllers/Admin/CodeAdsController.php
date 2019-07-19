<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\CodeAds;
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

class CodeAdsController extends BaseAdminController{
	
	private $arrStatus = array();
	private $error = '';

	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);

        $this->arrStatus = CGlobal::$arrStatusAds;
	}
	public function listView(){
		//Config Page
		$pageNo = (int)Request::get('page', 1);
		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::num_record_per_page;
		$offset = $stt = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;

		$search['code_ads_title'] = addslashes(Request::get('code_ads_title', ''));
		$search['code_ads_status'] = (int)Request::get('code_ads_status', -1);
		$search['field_get'] = '';

		$dataSearch = CodeAds::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

		$optionStatus = Utility::getOption($this->arrStatus, $search['code_ads_status']);
		$messages = Utility::messages('messages');


		return view('admin.codeads.list', [
			'stt' => $stt,
			'data' => $dataSearch,
			'total' => $total,
			'paging' => $paging,
			'arrStatus' => $this->arrStatus,
			'optionStatus' => $optionStatus,
			'search' => $search,
			'messages' => $messages,
		]);
	}
	public function getItem($id=0){
		
		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$data = array();
		if($id > 0) {
			$data = CodeAds::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['code_ads_status'])? $data['code_ads_status'] : CGlobal::status_show);

		return view('admin.codeads.add', [
			'id' => $id,
			'data' => $data,
			'optionStatus' => $optionStatus,
			'error' => $this->error,
		]);
	}
	public function postItem($id=0){
		
		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$id_hiden = (int)Request::get('id_hiden', 0);
		$data = array();
		
		$dataSave = array(
				'code_ads_title'=>array('value'=>addslashes(Request::get('code_ads_title')), 'require'=>1, 'messages'=>'Mã không được trống!'),
				'code_ads_content'=>array('value'=>addslashes(Request::get('code_ads_content', '')),'require'=>0),
				'code_ads_price'=>array('value'=>(int)Request::get('code_ads_price', 0),'require'=>1, 'messages'=>'Giá không được trống!'),
				'code_ads_status'=>array('value'=>(int)Request::get('code_ads_status', -1),'require'=>0),
				'code_ads_created'=>array('value'=>time()),
		);
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;
			if($id > 0){
				unset($dataSave['code_ads_created']);
			}
			CodeAds::saveData($id, $dataSave);
			return Redirect::route('admin.code_ads');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['code_ads_status'])? $data['code_ads_status'] : -1);
		return view('admin.codeads.add', [
				'id' => $id,
				'data' => $data,
				'optionStatus' => $optionStatus,
				'error' => $this->error,
			]);
	}
	public function delete(){
		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::addItem($id, 'CodeAds', '', 'code_ads_id', 'code_ads_title', '', '', $this->user['user_id'], $this->user['user_name']);
					CodeAds::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.code_ads');
	}
}