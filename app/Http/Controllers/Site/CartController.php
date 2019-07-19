<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Http\Controllers\Site;

use App\Http\Controllers\BaseSiteController;
use App\Http\Models\EmailCustomer;
use App\Http\Models\Info;
use App\Http\Models\Order;
use App\Http\Models\Product;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\SEOMeta;
use App\Library\PHPDev\ValidForm;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class CartController extends BaseSiteController{
	
	public function __construct(){
		parent::__construct();
	}
	public function ajaxAddCart(){
		
		if(empty($_POST)){
			return '';
		}

		$pid = (int)Request::get('pid');
		$psize = addslashes(Request::get('psize', 'One'));
		$pnum = (int)Request::get('pnum');
		$data = array();
		
		if($pid > 0 && $pnum > 0){
			$result = Product::getById($pid);
			if(sizeof($result) != 0){
				if($result->product_sale == CGlobal::product_sale_off){
					echo 'Tạm hết hàng'; exit();
				}
				$product_size_no = ($result->product_size_no != '') ? @unserialize($result->product_size_no) : array();
				if(is_array($product_size_no) && !empty($product_size_no)){
					$_no = 0;
					foreach($product_size_no as $item){
						$_no += (int)$item['no'];
					}
					if($_no <= 0){
						echo 'Tạm hết hàng'; exit();
					}
				}
				
				if(Session::has('cart')){
					$data = Session::get('cart');
					if(isset($data[$pid][$psize])){
						$data[$pid][$psize] += 1;
					}else{
						$data[$pid][$psize] = 1;
					}
				}else{
					$data[$pid][$psize] = 1;
				}
				Session::put('cart', $data, 60*24);
				echo 1;
			}else{
				if(Session::has('cart')){
					$data = Session::get('cart');
					if(isset($data[$pid][$psize])){
						unset($data[$pid][$psize]);
					}
					Session::put('cart', $data, 60*24);
				}
				echo 'Không tồn tại sản phẩm này'; exit();
			}
			Session::save();
		 }
		 exit();
	}
	public function pageOrderCart(){
		
		$meta_title = $meta_keywords = $meta_description = 'Sản phẩm trong giỏ hàng';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$dataCart = array();
		//Update Cart
		if(!empty($_POST)){
			$token = Request::get('_token', '');
			if(Session::token() === $token){
				$updateCart = Request::get('listCart', array());
				$dataCart = Session::get('cart');
				
				foreach($updateCart as $k=>$v){
					foreach($v as $size => $num){
						if($num == 0){
							if(isset($dataCart[$k][$size])){
								unset($dataCart[$k][$size]);
							}
							if(empty($dataCart[$k])){
								unset($dataCart[$k]);
							}
						}else{
							if(isset($dataCart[$k][$size])){
								$dataCart[$k][$size] = (int)$num;
							}
						}
					}
				}
				
				Session::put('cart', $dataCart);
				Session::save();
				unset($_POST);
				return Redirect::route('site.pageOrderCart');
			}
		}
		//End Update Cart
		
		if(Session::has('cart')){
			$dataCart = Session::get('cart');
		}
		
		//Config Page
		$pageNo = (int) Request::get('page', 1);
		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::max_num_record_order;
		$offset = ($pageNo - 1) * $limit;
		$search = $dataItem = array();
		$total = 0;
		$paging = '';
		
		if(!empty($dataCart)){
			$arrId = array_keys($dataCart);
			$paging = '';
			if(!empty($arrId)){
				$search['product_id'] = $arrId;
				$search['field_get'] = '';
				$dataItem = Product::getOrderCart($search, $limit, $offset, $total);
				$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
			}
		}

        return view('site.content.pageOrderCart', ['dataCart'=>$dataCart, 'dataItem'=>$dataItem,'paging'=>$paging]);
	}
	public function deleteOneItemInCart(){
		
		if(empty($_POST)){
			return '';
		}
		
		$pid = (int)Request::get('pid', 0);
		$psize = addslashes(Request::get('psize', ''));
		if($pid > 0 && $psize!=''){
			if(Session::has('cart')){
				$data = Session::get('cart');
				if(isset($data[$pid][$psize])){
					unset($data[$pid][$psize]);
				}
				if(isset($data[$pid]) && empty($data[$pid])){
					unset($data[$pid]);
				}
				Session::put('cart', $data, 60*24);
				Session::save();
			}
		}
		echo 'ok';exit();
	}
	public function deleteAllItemInCart(){
		if(empty($_POST)){
			return '';
		}
		$dell = addslashes(Request::get('all', ''));
		if($dell == 'del-all'){
			if(Session::has('cart')){
				Session::forget('cart');
				Session::save();
			}
		}
		echo 'ok';exit();
	}
	public function pageSendCart(){
		$member = $this->member;
		if(!Session::has('cart')){
			return Redirect::route('site.index');
		}

        $meta_title = $meta_keywords = $meta_description = 'Gửi thông tin đơn hàng';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$dataCart = array();
		if(Session::has('cart')){
			$dataCart = Session::get('cart');
		}
		
		//Config Page
		$pageNo = (int) Request::get('page', 1);
		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::max_num_record_order;
		$offset = ($pageNo - 1) * $limit;
		$search = $dataItem = array();
		$total = 0;
		$paging = '';
		
		if(!empty($dataCart)){
			$arrId = array_keys($dataCart);
			if(!empty($arrId)){
				$search['product_id'] = $arrId;
				$search['field_get'] = '';
				$dataItem = Product::getOrderCart($search, $limit, $offset, $total);
				$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
			}
		}
		
		if(!empty($_POST)){
			$token = Request::get('_token', '');
			if(Session::token() === $token){
				$txtName = addslashes(trim(Request::get('txtName', '')));
				$txtMobile = addslashes(trim(Request::get('txtMobile', '')));
				$txtAddress = addslashes(trim(Request::get('txtAddress', '')));
				$txtMessage = addslashes(trim(Request::get('txtMessage', '')));
				$txtEmail = addslashes(trim(Request::get('txtEmail', '')));
				
				if($txtName!= '' && $txtMobile != '' && $txtAddress != ''){
					$total_num = 0;
					$total = 0;
					$arr_content_mail_customer = array();
                    $arr_content_admin = array();
					foreach($dataItem as $item){
						foreach($dataCart as $k=>$v){
							if($item->product_id == $k){
								foreach($v as $size=>$num){
                                    $item_content_mail_customer = array(
                                                    'product_id'=>$item->product_id,
                                                    'product_code'=>$item->product_code,
                                                    'product_title'=>$item->product_title,
                                                    'product_size'=>$size,
                                                    'product_num'=>$num,
                                                    'product_price'=>(int)$item->product_price,
									);
                                    $item_content_admin = array(
                                                    'pid'=>$item->product_id,
                                                    'pcode'=>$item->product_code,
                                                    'psize'=>$size,
                                                    'pnum'=>$num,
                                    );
                                    $arr_content_mail_customer[] = $item_content_mail_customer;
                                    $arr_content_admin[] = $item_content_admin;
									$total_num += $num;
									if($item->product_price > 0){
										$total += (int)$item->product_price * $num;
									}
								}
							}
						}
					}
                    $data_content_customer = array(
                        'order_title' => $txtName,
                        'order_total' => $total,
                        'order_phone' => $txtMobile,
                        'order_address' => $txtAddress,
                        'order_email' => $txtEmail,
                        'order_list_code'=>serialize($arr_content_mail_customer),
                        'order_note' => $txtMessage,
                        'order_num'=> $total_num,
                        'order_status'=> 1,
                        'order_created' => time(),
                    );

					$data_content_admin = array(
						'order_title' => $txtName,
						'order_total_lst' => $total,
						'order_phone' => $txtMobile,
						'order_address' => $txtAddress,
						'order_email' => $txtEmail,
						'order_list_code'=>serialize($arr_content_admin),
						'order_note' => $txtMessage,
						'order_num'=> $total_num,
						'order_status'=> 1,
						'order_created' => time(),
					);

					if(Session::has('member')){
						$session_member = Session::get('member');
                        $data_content_admin['order_user_buy'] = $session_member['member_id'];
					}else{
                        $data_content_admin['order_user_buy'] = 1;//Default
                    }
					
					//Add Order
					$query = Order::addData($data_content_admin);
					
					//Send Mail To Admin And Customer
					$this->sendMailOrder($data_content_customer);
					//Send Mail To Customer
					$this->sendMailOrderToCustomer($txtEmail, $data_content_customer);
					
					//Add Custommer to EmailCustomer
					$dataCustomer = array(
						'customer_email'=>$txtEmail,
						'customer_phone'=>$txtMobile,
						'customer_address'=>$txtAddress,
						'customer_full_name'=>$txtName,
					);
					$this->addCustomer($txtEmail, $dataCustomer);
					
					if(Session::has('cart')){
						Session::forget('cart');
						return Redirect::route('site.pageThanksBuy');
					}
				}
			}
		}
        $strThanhToan = '';
        $arrThanToan = Info::getItemByKeyword('SITE_TEXT_THANHTOAN');
        if (sizeof($arrThanToan) > 0) {
            $strThanhToan = stripslashes($arrThanToan->info_content);
        }

        return view('site.content.pageSendCart', ['dataCart'=>$dataCart, 'dataItem'=>$dataItem,'paging'=>$paging,'member'=>$member,'strThanhToan'=>$strThanhToan]);
	}
	public function pageThanksBuy(){
        $meta_title = $meta_keywords = $meta_description = 'Cảm ơn đã mua hàng';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);
        return view('site.content.pageThanksBuy');
	}
	
	public function sendMailOrder($data){
		if(!empty($data)){
			$emails = [CGlobal::emailAdmin];
			Mail::send('emails.mailReportOrderToAdmin', array('data'=>$data), function($message) use ($emails){
				$message->to($emails, 'Order')
				        ->subject('Đơn hàng từ website '.date('d/m/Y h:i',  time()));
			});
		}
		return true;
	}
	
	public function sendMailOrderToCustomer($mail='', $data){
		if($mail !='' && !empty($data)){
			$checkRegexEmail = ValidForm::checkRegexEmail($mail);
			if($checkRegexEmail){
				$emails = [$mail];
				Mail::send('emails.mailReportOrderToCustomer', array('data'=>$data), function($message) use ($emails){
					$message->to($emails, 'Order')
					        ->subject(ucwords(CGlobal::domain).' - Bạn đã đặt mua sản phẩm '.date('d/m/Y h:i',  time()));
				});
			}
		}
		return true;
	}
	
	public function addCustomer($mail='', $data=array()){
		if($mail != '' && !empty($data)){
			$checkMail = ValidForm::checkRegexEmail($mail);
			if($checkMail){
				$checkEmailExist = EmailCustomer::getCustomerByEmail($mail);
				if(sizeof($checkEmailExist) == 0){
					EmailCustomer::addData($data);
				}
			}
		}
	}
}
