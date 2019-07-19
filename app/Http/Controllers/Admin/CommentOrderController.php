<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\CommentOrder;
use App\Http\Models\Order;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\FuncLib;

class CommentOrderController extends BaseAdminController{

	public function __construct(){
		parent::__construct();
		Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
		Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
		Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);
	}
	public function orderAjaxAddComment(){
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
	
		$comment_pid = (int)Request::get('orderId', 0);
		$comment_content = addslashes(Request::get('frmcomment', ''));
		$comment_created = time();
		if($comment_pid > 0 && $comment_content != '' && $uid > 0){
			$data = array(
						'comment_pid'=>$comment_pid,
						'comment_content'=>$comment_content,
						'comment_created'=>$comment_created,
						'uid'=>$uid,
						'comment_username'=>$comment_username,
						
			);
			$id = CommentOrder::addData($data);
			$rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
			$txt = '<li>
                        <div class="notetxt">'.$comment_username.':</div>
                        <div class="contenttxt">'.$comment_content.'</div>
                        <div class="datetxt">'.date('d/m/Y h:i:s',$comment_created).'</div>';
						if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
							$txt.='<div class="comment-delete" data="'.$id.'"><i class="fa fa-remove"></i></div>';
						}
			$txt.='</li>';
			echo json_encode($txt);exit();
		}
		echo '';exit();
	}
	public function orderAjaxDeleteComment(){
		$user = $this->user;
		$rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
		if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
			$id = (int)Request::get('id', 0);
			if($id > 0){
				CommentOrder::deleteId($id);
				echo 'ok';die;
			}
		}else{
			echo 'not ok';die;
		}
	}
	public function showAllComment($pid, $sort='asc', $limit=0){
		$html='';
		if($pid > 0){
			$search['comment_pid'] = $pid;
			$CommentOrder = new CommentOrder();
			$result = $CommentOrder->getAllComment($search, $limit, $sort);
			if(!empty($result)){
                $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
                foreach ($result as $item) {
					$html .= '<li>
                        <div class="notetxt">'.$item->comment_username.':</div>
                        <div class="contenttxt">'.$item->comment_content.'</div>
                        <div class="datetxt">'.date('d/m/Y H:i:s',$item->comment_created).'</div>';
					if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
						$html.='<div class="comment-delete" data="'.$item->comment_id.'"><i class="fa fa-remove"></i></div>';
					}
					$html.='</li>';
				}
			}
		}
		return  $html;
	}
	public function popupAjaxGetAllCommentOrder(){
		$html='';
		$user = $this->user;
		$orderId = (int)Request::get('orderId', 0);
		if($user['user_id'] > 0 && $orderId > 0){
			$html .= $this->showAllComment($orderId, $sort='asc');
		}
		echo $html;die();
	}
	public function popupAjaxGetOneOrder(){
		$html='';
		$user = $this->user;
		$orderId = (int)Request::get('orderId', 0);
		if($user['user_id'] > 0 && $orderId > 0){
			$data = Order::getById($orderId);
			$arrStatus = CGlobal::$arrStatusOrder;
			$str_content = '';
			if(sizeof($data) != 0){
				$html .= '<div>1.Ngày lên đơn: <b>'.date('d/m/Y H:i',$data->order_created).'</b></div>';
				$status = isset($arrStatus[$data->order_status]) ? $arrStatus[$data->order_status] : 'Chưa biết';
				$html .= '<div>2.Trạng thái: <b>'.$status.'</b></div>';
                $html .= '<div>3.Người lên đơn: <b>#'.$data->order_user_id_created.' - '.ucwords($data->order_user_name_created).'</b></div>';
                $html .= '<div>4.COD: <b>'.FuncLib::numberFormat($data->order_total_lst).'đ</b></div></br>';


				$html .= '<div><b>Thông tin khách hàng:</b></div>';
				$html .= '<div>1.Họ tên: <b>'.$data->order_title.'</b></div>';
				$html .= '<div>2.SĐT: <b>'.$data->order_phone.'</b></div>';
				$html .= '<div>3.Địa chỉ: <b>'.$data->order_address.'</b></div>';
				$html .= '<div>4.Yêu cầu của khách: <br/>'.$data->order_note.'</div>';
				
				$order_list_code = $data->order_list_code;
				if($order_list_code != ''){
                    $order_list_code = unserialize($order_list_code);
					if(is_array($order_list_code)){
						$str_content .= '<table class="content-order popup-content-order">';
						$str_content .= '<tr>
											<th width="1%">Mã[ID]</th>
				                    		<th width="5%">Cỡ</th>
				                    		<th width="5%">SL</th>
										  </tr>';
						foreach($order_list_code as $item){
							$str_content .= '<tr>
											<td>'.$item['pcode'].'</td>
				                    		<td>'.$item['psize'].'</td>
				                    		<td>'.$item['pnum'].'</td>
										  </tr>';
						}
						$str_content .='<tr>
									            <td colspan="3"><b>Tổng COD:'.FuncLib::numberFormat((int)$data->order_total_lst).'</b><sup>đ</sup></td>
									        </tr>';
						$str_content .= '<table>';
					}
				}
				$html .= $str_content;
			}
		}
		echo $html;die();
	}
}