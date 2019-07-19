<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\CommentProduct;
use App\Http\Models\Order;
use App\Http\Models\Trash;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\FuncLib;

class CommentProductController extends BaseAdminController{

    private $permission_view = 'product_comment_view';
    private $permission_create = 'product_comment_create';
    private $permission_edit = 'product_comment_edit';
    private $permission_delete = 'product_comment_delete';

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

        $search['comment_username'] = addslashes(Request::get('comment_username', ''));
        $search['comment_phone'] = addslashes(Request::get('comment_phone', ''));
        $search['comment_mail'] = addslashes(Request::get('comment_mail', ''));
        $search['comment_pid'] = (int)Request::get('comment_pid', '');
        $search['comment_status'] = (int)Request::get('comment_status', -1);
        $search['comment_catid'] = 0;
        $search['field_get'] = '';

        $dataSearch = CommentProduct::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

        $optionStatus = Utility::getOption($this->arrStatus, $search['comment_status']);
        $messages = Utility::messages('messages');

        return view('admin.commentProduct.list',[
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
        $comment = '';
        if($id > 0) {
            $data = CommentProduct::getById($id);
            $pid = 0;
            if(sizeof($data) > 0){
                $pid = $data->comment_pid;
            }
            $comment = $this->showAllComment($id, $pid);
        }
        $optionStatus = Utility::getOption($this->arrStatus, isset($data['comment_status'])? $data['comment_status'] : CGlobal::status_show);
        return view('admin.commentProduct.add',[
            'id'=>$id,
            'data'=>$data,
            'comment'=>$comment,
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
            'comment_username'=>array('value'=>addslashes(Request::get('comment_username')), 'require'=>0),
            'comment_phone'=>array('value'=>trim(Request::get('comment_phone')),'require'=>0),
            'comment_mail'=>array('value'=>trim(Request::get('comment_mail')),'require'=>0),
            'comment_pid'=>array('value'=>(int)Request::get('comment_pid', 0),'require'=>0),
            'comment_status'=>array('value'=>(int)Request::get('comment_status', 0),'require'=>0),
            'comment_content'=>array('value'=>Request::get('comment_content'),'require'=>0),
            'comment_created'=>array('value'=>time(),'require'=>''),
        );

        if($id > 0){
            unset($dataSave['comment_created']);
        }

        $this->error = ValidForm::validInputData($dataSave);
        if($this->error == ''){
            $id = ($id == 0) ? $id_hiden : $id;

            CommentProduct::saveData($id, $dataSave);
            return Redirect::route('admin.commentProduct');
        }else{
            foreach($dataSave as $key=>$val){
                $data[$key] = $val['value'];
            }
        }

        if($id > 0) {
            $data = CommentProduct::getById($id);
            $pid = 0;
            if(sizeof($data) > 0){
                $pid = $data->comment_pid;
            }
            $comment = $this->showAllComment($id, $pid);
        }

        $optionStatus = Utility::getOption($this->arrStatus, isset($data['comment_status'])? $data['comment_status'] : CGlobal::status_show);
        return view('admin.commentProduct.add',[
            'id'=>$id,
            'data'=>$data,
            'optionStatus'=>$optionStatus,
            'comment'=>$comment,
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
                    Trash::addItem($id, 'CommentProduct', '', 'comment_id', 'comment_username', '', '');
                    CommentProduct::deleteId($id);
                    CommentProduct::deleteByCatId($id);
                }
                Utility::messages('messages', 'Xóa thành công!', 'success');
            }
        }
        return Redirect::route('admin.commentProduct');
    }

	public function commentAjaxAddComment(){
		if(isset($_POST) && empty($_POST)){
			return Redirect::route('admin.dashboard');
		}
		$user = $this->user;
		if(!empty($user)){
			$comment_username = $user['user_name'];
			$uid = $user['user_id'];
		}else{
			$comment_username = '';
			$uid = '';
		}
        $comment_id = (int)Request::get('commentId', 0);
        $comment_catid = $comment_id;
        $comment_pid = 0;

        if($comment_id > 0){
            $arrComment = CommentProduct::getById($comment_id);
            if(sizeof($arrComment) > 0){
                $comment_pid = $arrComment->comment_pid;
            }
        }
		$comment_content = addslashes(Request::get('frmcomment', ''));
		$comment_created = time();

		if($comment_id > 0 && $comment_pid > 0 && $comment_content != '' && $uid > 0){
			$data = array(
						'comment_pid'=>$comment_pid,
						'comment_catid'=>$comment_catid,
						'comment_content'=>$comment_content,
						'comment_created'=>$comment_created,
						'uid'=>$uid,
						'comment_username'=>$comment_username,
						'comment_status'=>CGlobal::status_show,

			);
			$id = CommentProduct::addData($data);
            $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
			$txt = '<li>
                        <div class="notetxt">'.$comment_username.':</div>
                        <div class="contenttxt">'.$comment_content.'</div>
                        <div class="datetxt">'.date('d/m/Y h:i:s',$comment_created).'</div>';
                        if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
							$txt.='<div class="comment-product-delete" data="'.$id.'"><i class="fa fa-remove"></i></div>';
						}
			$txt.='</li>';
			echo json_encode($txt);exit();
		}
		echo '';exit();
	}
	public function commentAjaxDeleteComment(){
		$user = $this->user;
		$rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
		if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
			$id = (int)Request::get('id', 0);
			if($id > 0){
				CommentProduct::deleteId($id);
                CommentProduct::deleteByCatId($id);
				echo 'ok';die;
			}
		}else{
			echo 'not ok';die;
		}
	}
	public function showAllComment($cid=0, $pid=0, $sort='asc', $limit=0){
		$html='';
		if($pid > 0 && $cid > 0){
			$search['comment_pid'] = $pid;
            $search['comment_catid'] = $cid;
			$CommentProduct = new CommentProduct();
			$result = $CommentProduct->getAllComment($search, $limit, $sort);
			if(!empty($result)){
                $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
                foreach ($result as $item) {
					$html .= '<li>
                        <div class="notetxt">'.$item->comment_username.':</div>
                        <div class="contenttxt">'.$item->comment_content.'</div>
                        <div class="datetxt">'.date('d/m/Y H:i:s',$item->comment_created).'</div>';
					if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
						$html.='<div class="comment-product-delete" data="'.$item->comment_id.'"><i class="fa fa-remove"></i></div>';
					}
					$html.='</li>';
				}
			}
		}
		return  $html;
	}
}