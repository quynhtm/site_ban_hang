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
use App\Http\Models\Type;
use App\Http\Models\Trash;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

class TypeController extends BaseAdminController{

    private $permission_view = 'type_view';
    private $permission_create = 'type_create';
    private $permission_edit = 'type_edit';
    private $permission_delete = 'type_delete';

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
		$pageNo = (int)Request::get('page', 1);

		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::num_record_per_page;
		$offset = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['type_title'] = addslashes(Request::get('type_title', ''));
		$search['type_status'] = (int)Request::get('type_status', -1);
		$search['field_get'] = 'type_id,type_title,type_intro,type_keyword,type_order_no,type_created,type_status';
		
		$dataSearch = Type::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['type_status']);
		$messages = Utility::messages('messages');

        return view('admin.type.list',[
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
			$data = Type::getById($id);
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['type_status'])? $data['type_status'] : CGlobal::status_show);
        return view('admin.type.add',[
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

        $token = Request::get('_token', '');
        $id_hiden = (int)Request::get('id_hiden', 0);

        $data = array();
        if(Session::token() === $token) {
            $dataSave = array(
                'type_title' => array('value' => addslashes(Request::get('type_title')), 'require' => 1, 'messages' => 'Tiêu đề không được trống!'),
                'type_keyword' => array('value' => addslashes(Request::get('type_keyword')), 'require' => 1, 'messages' => 'Từ khóa không được trống!'),
                'type_intro' => array('value' => addslashes(Request::get('type_intro')), 'require' => 0),
                'type_order_no' => array('value' => (int)addslashes(Request::get('type_order_no')), 'require' => 0),
                'type_status' => array('value' => (int)Request::get('type_status', -1), 'require' => 0),
            );
            if ($id > 0) {
                unset($dataSave['type_keyword']);
            }

            $this->error = ValidForm::validInputData($dataSave);
            if ($this->error == '') {
                $id = ($id == 0) ? $id_hiden : $id;
                Type::saveData($id, $dataSave);
                return Redirect::route('admin.type');
            } else {
                foreach ($dataSave as $key => $val) {
                    $data[$key] = $val['value'];
                }
            }
        }
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['type_status'])? $data['type_status'] : -1);
        return view('admin.type.add',[
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
                    Trash::addItem($id, 'Type', '', 'type_id', 'type_title', '', '');
					Type::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.type');
	}
	//Get Array Type
	public static function getArrType(){
		$result = array(-1=>'Chọn kiểu danh mục');
		$dataSearch['field_get'] = 'type_id,type_title';
		$arrType = Type::getAllType($dataSearch, $limit=1000);
		if(!empty($arrType)){
			foreach($arrType as $cate){
				$result[$cate->type_id] =  $cate->type_title;
			}
		}
		return $result;
	}
}