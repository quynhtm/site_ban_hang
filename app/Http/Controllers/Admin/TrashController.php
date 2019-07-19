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
use App\Http\Controllers\BaseAdminController;
use App\Http\Models\Trash;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;

class TrashController extends BaseAdminController{

	private $permission_view = 'trash_view';
	private $permission_create = 'trash_create';
	private $permission_edit = 'trash_edit';
	private $permission_delete = 'trash_delete';

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
		
		$search['trash_title'] = addslashes(Request::get('trash_title', ''));
		$search['field_get'] = 'trash_id,trash_obj_id,trash_title,trash_class,trash_folder,trash_created';
		
		$dataSearch = Trash::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$messages = Utility::messages('messages');

        return view('admin.trash.list',[
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
		$arrField = array();
		if($id > 0) {
			$data = Trash::getById($id);
			$class = $data->trash_class;
			$_class =  "App\Http\Models\\".$class;
			$ObjClass = new $_class();
			$arrField = $ObjClass->getFillable();
		}
        return view('admin.trash.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'arrField'=>$arrField,
                    'error'=>$this->error,
                ]);

	}
	
	public function delete(){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.trash');
	}
	public function restore(){

        if(!in_array($this->permission_delete, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::restoreItem($id);
					Trash::deleteId($id);
				}
				Utility::messages('messages', 'Khôi phục thành công!', 'success');
			}
		}
		return Redirect::route('admin.trash');
	}
}