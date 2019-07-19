<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/

namespace App\Http\Controllers\Login;

use App\Http\Controllers\BaseSiteController;
use App\Http\Models\Category;
use App\Http\Models\Member;
use App\Http\Models\News;
use App\Http\Models\Order;
use App\Http\Models\Product;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\SEOMeta;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

class MemberLoginController extends BaseSiteController{
	public function __construct(){
		parent::__construct();
        Loader::loadCSS('libs/fontAwesome/css/font-awesome.min.css', CGlobal::$postHead);
	}
	//Register - Login
    public function pageLogin($url=''){
    	
    	if(Session::has('member')){
            die('');
    	}

    	$token = addslashes(Request::get('_token', ''));
    	$mail = addslashes(Request::get('sys_login_mail', ''));
    	$pass = addslashes(Request::get('sys_login_pass', ''));
    	$error = '';
    	if(Session::token() === $token){
	    	if($mail != '' && $pass != ''){
	    		$checkMail = ValidForm::checkRegexEmail($mail);
				if(!$checkMail) {
	    			$error = 'Email đăng nhập không đúng!';
	    		}else{
	    			$member = Member::getMemberByEmail($mail);
	    			if(sizeof($member) > 0){
	    				if($member->member_status == 0 || $member->member_status == -1){
	    					$error = 'Tài khoản đang bị khóa!';
	    				}elseif($member->member_status == 1){
	    					$encode_password = Member::encode_password($pass);
	    					if($member->member_pass == $encode_password){
	    						$data = array(
		    								'member_id' => $member->member_id,
	    									'member_full_name' => $member->member_full_name,
	    									'member_phone' => $member->member_phone,
		    								'member_mail' => $member->member_mail,
	    									'member_address' => $member->member_address,
	    									'member_status' => $member->member_status,
	    									'member_created' => $member->member_created,
	    								);
	    						Session::put('member', $data, 60*24);
	    						Session::save();
	    						Member::updateLogin($member);
	    					}else{
	    						$error = 'Mật khẩu chưa đúng!';
	    					}
	    				}
	    			}else{
	    				$error = 'Không tồn tại tên đăng nhập!';
	    			}
	    		}
	    	}else{
	    		$error = 'Thông tin đăng nhập chưa đúng!';
	    	}
    	}else{
    		$error .= 'Phiên làm việc hết hạn. Bạn refresh lại trang web!';
    	}
    	echo $error;die;
    }
    public function logout(){
    	if(Session::has('member')){
        	Session::forget('member');
        }
        return Redirect::route('site.index');
    }
    public function pageRegister(){

		if(Session::has('member')){
			die('');
		}
    	
    	$token = addslashes(Request::get('_token', ''));
    	$mail = addslashes(Request::get('sys_reg_email', ''));
    	$pass = addslashes(Request::get('sys_reg_pass', ''));
    	$repass = addslashes(Request::get('sys_reg_re_pass', ''));
    	$fullname = addslashes(Request::get('sys_reg_full_name', ''));
    	$phone = addslashes(Request::get('sys_reg_phone', ''));
    	$address = addslashes(Request::get('sys_reg_address', ''));
    	$error = '';
    	$hash_pass = '';
		if(Session::token() === $token) {
			//Mail
			if ($mail != '') {
				$checkMail = ValidForm::checkRegexEmail($mail);
				if (!$checkMail) {
					$error .= 'Email đăng nhập không đúng!';
				}
			} else {
				$error .= 'Email đăng nhập không được trống!';
			}
			//Pass
			if ($pass != '' && ($pass === $repass)) {
				$check_valid_pass = ValidForm::checkRegexPass($pass, 5);
				if ($check_valid_pass) {
					$hash_pass = Member::encode_password($pass);
				} else {
					$error .= 'Mật không được ít hơn 5 ký tự và không được có dấu!' . '<br/>';
				}
			}
			if ($pass == '' && $repass == '') {
				$error .= 'Mật khẩu không được trống!' . '<br/>';
			} elseif ($pass != $repass) {
				$error .= 'Mật khẩu không khớp!' . '<br/>';
			}

			//Check Member Exists
			$check = Member::getMemberByEmail($mail);
			if (sizeof($check) != 0) {
				$error .= 'Email đăng nhập này đã tồn tại!' . '<br/>';
			}

			if ($mail != '' && $pass != '' && $repass != '' && $fullname != '' && $phone != '' && $address != '') {
				if ($error == '') {
					$data = array(
						'member_mail' => $mail,
						'member_pass' => $hash_pass,
						'member_full_name' => $fullname,
						'member_phone' => $phone,
						'member_address' => $address,
						'member_created' => time(),
						'member_status' => CGlobal::status_show,
					);
					$id = Member::addData($data);
					$data['member_id'] = $id;
					Session::put('member', $data, 60 * 24);
					Session::save();
					$member = Member::getMemberByEmail($mail);
					Member::updateLogin($member);
				}
			} else {
				$error .= 'Thông tin đăng ký chưa đầy đủ!';
			}
		}else{
            $error .= 'Phiên làm việc hết hạn. Bạn refresh lại trang web!';
		}
    	echo $error;die;
    }
	//Change Info - Chage Pass
	public function pageChageInfo(){
		if(!Session::has('member')){
			return Redirect::route('site.index');
		}
        $meta_title = 'Thay đổi thông tin cá nhân';
        $meta_keywords = 'Thay đổi thông tin cá nhân';
        $meta_description = 'Thay đổi thông tin cá nhân';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$error = '';
		$messages = Utility::messages('messages');
		if(isset($_POST) && !empty($_POST)){
			$token = Request::get('_token', '');
			$mail = Request::get('sys_change_email', '');
			$full_name = Request::get('sys_change_full_name', '');
			$phone = Request::get('sys_change_phone', '');
			$address = Request::get('sys_change_address', '');
			if(Session::token() === $token){
				$session_member = $this->member;
				$sessionMail = $session_member['member_mail'];

				if($sessionMail == $mail){
					if($mail != '' && $full_name != '' && $phone !='' && $address != ''){
						$data = array(
								'member_full_name' =>$full_name,
								'member_phone' =>$phone,
								'member_address' =>$address,
								);
						Member::updateData($session_member['member_id'], $data);
						Utility::messages('messages', 'Thay đổi thông tin thành công', 'success');
						//Upate Session
						$dataSess = array(
								'member_id' => $session_member['member_id'],
								'member_mail'=>$mail,
								'member_full_name'=>$full_name,
								'member_phone'=>$phone,
								'member_address'=>$address,
								'member_created'=>$session_member['member_created'],
								'member_status'=>$session_member['member_status'],
						);
						Session::put('member', $dataSess, 60*24);
						Session::save();
						$this->member = $dataSess;
						return Redirect::route('member.pageChageInfo');
					}
				}else{
					$error .= 'Email của bạn không đúng!';
				}
			}
		}

        return view('site.member.pageChageInfo',[
            'error'=>$error,
            'messages'=>$messages,
        ]);
	}
	public function pageChagePass(){
		if(!Session::has('member')){
			return Redirect::route('site.index');
		}

        $meta_title = 'Thay đổi mật khẩu';
        $meta_keywords = 'Thay đổi mật khẩu';
        $meta_description = 'Thay đổi mật khẩu';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$error = '';
		$messages = Utility::messages('messages');
		if(isset($_POST) && !empty($_POST)){
			$token = Request::get('_token', '');
			$mail = Request::get('sys_change_email', '');
			$pass = Request::get('sys_change_pass', '');
			$repass = Request::get('sys_change_re_pass', '');
			$hash_pass = '';
			if(Session::token() === $token){
				$session_member = $this->member;
				$sessionMail = $session_member['member_mail'];
				if($sessionMail == $mail){
					//Pass
					if($pass != '' && ($pass === $repass)){
						$check_valid_pass = ValidForm::checkRegexPass($pass, 5);
						if($check_valid_pass){
							$hash_pass = Member::encode_password($pass);
						}else{
							$error .= 'Mật không được ít hơn 5 ký tự và không được có dấu!'.'<br/>';
						}
					}
					if($pass == '' && $repass == ''){
						$error .= 'Mật khẩu không được trống!'.'<br/>';
					}elseif($pass != $repass){
						$error .= 'Mật khẩu không khớp!'.'<br/>';
					}
					
					if($mail != '' && $pass != '' && $repass !=''){
						if($error == ''){
							$data = array(
									'member_pass' =>$hash_pass,
									);
							Member::updateData($session_member['member_id'], $data);
							Utility::messages('messages', 'Thay đổi mật khẩu thành công', 'success');
							//Upate Session
							$dataSess = array(
									'member_id' => $session_member['member_id'],
									'member_mail'=>$mail,
									'member_full_name'=>$session_member['member_full_name'],
									'member_phone'=>$session_member['member_phone'],
									'member_address'=>$session_member['member_address'],
									'member_created'=>$session_member['member_created'],
									'member_status'=>$session_member['member_status'],
							);
							Session::put('member', $dataSess, 60*24);
							Session::save();
							$this->member = $dataSess;
							return Redirect::route('member.pageChagePass');
						}
					}
				}else{
					$error .= 'Email của bạn không đúng!';
				}
			}
		}

		return view('site.member.pageChagePass',[
			'member'=>$this->member,
			'error'=>$error,
			'messages'=>$messages,
		]);

	}
	public function pageForgetPass(){

        if(Session::has('member')){
            die('');
        }
    	
    	$token = addslashes(Request::get('_token', ''));
    	$mail = addslashes(Request::get('sys_forget_mail', ''));
    	$error = '';

    	if(Session::token() === $token){

    		if($mail != ''){
    			$checkMail = ValidForm::checkRegexEmail($mail);
    			if(!$checkMail) {
    				$error = 'Email đăng nhập không đúng!';
    			}
    		}else{
    			$error = 'Email đăng nhập không được trống!';
    		}
    		//Check mail exists
    		$arrUser = Member::getMemberByEmail($mail);
    		if(sizeof($arrUser) > 0){
    			//Send mail
    			$key_secret = Utility::randomString(32);
    			if($key_secret != ''){
    				$emails = [$mail, CGlobal::emailAdmin];
    				$dataTheme = array(
    						'key_secret'=>$key_secret,
    						'phone_support'=>CGlobal::phoneSupport,
    						'domain'=>CGlobal::domain,
    				);
    				$data_session = array(
    						'key_secret'=>$key_secret,
    						'mail'=>$mail,
    				);

    				$data_session = serialize($data_session);
	    			Session::put('get_new_forget_pass', $data_session, 5);
	    			Session::save();

                    Mail::send('site.member.mailTempForgetPass', array('data'=>$dataTheme), function($message) use ($emails){
    					$message->to($emails, 'Member')
    							->subject('Hướng dẫn thay đổi mật khẩu '.date('d/m/Y h:i',  time()));
    				});

    				echo 1; die;
    			}else{
    				$error = 'Không tồn tại chuỗi bảo mật!';
    			}
    		}else{
    			$error = 'Email đăng ký không đúng!';
    		}
    	}else{
    		$error = 'Phiên làm việc hết hạn!';
    	}
    	
    	echo $error;die;
	}
	public function pageGetForgetPass(){
		if(!Session::has('get_new_forget_pass')){
			return Redirect::route('site.index');
		}
        $meta_title = 'Thay đổi mật khẩu mới';
        $meta_keywords = 'Thay đổi mật khẩu mới';
        $meta_description = 'Thay đổi mật khẩu mới';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$sessionGetNewPass = Session::get('get_new_forget_pass');
		$arrSession = unserialize($sessionGetNewPass);
		$error = '';
		if(empty($arrSession)){
			return Redirect::route('site.index');
		}
		$key_secret = $arrSession['key_secret'];
		$mail = $arrSession['mail'];
		//Post
		if(isset($_POST) && !empty($_POST)){
			$token = Request::get('_token', '');
			$pass = Request::get('sys_change_new_pass', '');
			$repass = Request::get('sys_change_new_re_pass', '');
			$hash_pass = '';
				
			if(Session::token() === $token){
				if($mail != ''){
					if($pass != '' && ($pass === $repass)){
						$check_valid_pass = ValidForm::checkRegexPass($pass, 5);
						if($check_valid_pass){
							$hash_pass = Member::encode_password($pass);
						}else{
							$error .= 'Mật không được ít hơn 5 ký tự và không được có dấu!'.'<br/>';
						}
					}
					if($pass == '' && $repass == ''){
						$error .= 'Mật khẩu không được trống!'.'<br/>';
					}elseif($pass != $repass){
						$error .= 'Mật khẩu không khớp!'.'<br/>';
					}
		
					if($pass != '' && $repass !=''){
		
						//Check mail exists
						$arrUser = Member::getMemberByEmail($mail);
						if(sizeof($arrUser) == 0){
							$error .= 'Email đăng nhập không tồn tại!'.'<br/>';
						}
		
						if($error == '' && sizeof($arrUser) > 0){
							$data = array(
									'member_pass' =>$hash_pass,
							);
							Member::updateData($arrUser->member_id, $data);
							Utility::messages('messages', 'Thay đổi mật khẩu thành công', 'success');
							//Upate Session
							$dataSess = array(
									'member_id' => $arrUser->member_id,
									'member_mail'=>$mail,
									'member_full_name'=>$arrUser->member_full_name,
									'member_phone'=>$arrUser->member_phone,
									'member_address'=>$arrUser->member_address,
									'member_created'=>$arrUser->member_created,
									'member_status'=>$arrUser->member_status,
							);
								
							Session::forget('get_new_forget_pass');
							Session::put('member', $dataSess, 60*24);
							Session::save();
								
							return Redirect::route('member.pageChageInfo');
						}
					}
				}else{
					$error .= 'Email của bạn không đúng!';
				}
			}
		}
		//Get
		$key = addslashes(Request::get('k', ''));
		if($key != ''){
			if($key_secret != $key){
				return Redirect::route('site.index');
			}
			
		}
        return view('site.member.pageGetNewPass',[
                    'error'=>$error,
                ]);
	}
	public function pageHistoryOrder(){
		if(!Session::has('member')){
			return '';
		}

		$meta_title = $meta_keywords = $meta_description = 'Gửi thông tin mua hàng';
		SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

		$session_member = $this->member;
		if($session_member['member_id'] > 0){
			//Config Page
			$pageNo = (int) Request::get('page', 1);
			$pageScroll = CGlobal::num_scroll_page;
			$limit = CGlobal::num_record_per_page;
			$offset = ($pageNo - 1) * $limit;
			$search = $data = array();
			$total = 0;
			$paging = '';
			$search['order_user_buy'] = isset($session_member['member_id']) ? $session_member['member_id'] : 0;
			$search['field_get'] = '';
			
			$dataSearch = Order::searchByCondition($search, $limit, $offset, $total);
			$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
			
			if(sizeof($dataSearch) != 0){
				foreach($dataSearch as $v){
					$data[] = array(
							'order_id'=>$v->order_id,
							'order_title'=>$v->order_title,
							'order_phone'=>$v->order_phone,
							'order_num'=>$v->order_num,
							'order_total_lst'=>$v->order_total_lst,
							'order_created'=>$v->order_created,
							'order_status'=>$v->order_status,
					
					);
				}
			}
			return view('site.member.pageHistoryOrder',['data'=>$data, 'paging'=>$paging, 'arrStatus'=>CGlobal::$arrStatusOrder]);

		}else{
			return Redirect::route('site.index');
		}
	}
	public function pageHistoryViewOrder(){
		if(!Session::has('member')){
			return '';
		}
		$html = '';
		$str_content = '';
		if(isset($_POST)){
			$session_member = $this->member;
			if($session_member['member_id'] > 0){
				$orderId = (int)Request::get('item', 0);
				if($orderId > 0){
					$data = Order::getById($orderId);
					$arrStatus = CGlobal::$arrStatusOrder;
					if(sizeof($data) > 0){
						$order_content = ($data->order_list_code != '') ? unserialize($data->order_list_code) : array();
						if(is_array($order_content)){
							$str_content .= '<table class="content-order">';
							$str_content .= '<tr>
											<th width="5%">Mã[ID]</th>
				                    		<th width="40%">Tên Sản phẩm</th>
				                    		<th width="5%" style="padding: 5px;text-align: center;">Cỡ</th>
				                    		<th width="5%" style="padding: 5px;text-align: center;">SL</th>
										  </tr>';
							foreach($order_content as $item){
								$dataProduct = Product::getById($item['pid']);
								if(sizeof($dataProduct) > 0){
									$str_content .= '<tr>
										<td>'.$item['pcode'].'</td>
										<td><a href="'.FuncLib::buildLinkDetailProduct($dataProduct['product_id'], $dataProduct['product_title']).'" target="_blank">'.$dataProduct['product_title'].'</a></td>
										<td style="padding: 5px;text-align: center;">'.$item['psize'].'</td>
										<td style="padding: 5px;text-align: center;">'.$item['pnum'].'</td>
									  </tr>';
								}
							}
							$str_content .= '<table>';
						}

						$html .= '<div>1.Ngày tạo đơn: <b>'.date('d/m/Y h:i',$data->order_created).'</b></div>';
						$status = isset($arrStatus[$data->order_status]) ? $arrStatus[$data->order_status] : 'Chưa biết';
						$html .= '<div>2. Trạng thái: <b>'.$status.'</b></div></br>';
						
						$html .= '<div><b>Thông tin của bạn:</b></div>';
						$html .= '<div>1.Họ tên: <b>'.$data->order_title.'</b></div>';
						$html .= '<div>2.SĐT: <b>'.$data->order_phone.'</b></div>';
						$html .= '<div>3.Địa chỉ: <b>'.$data->order_address.'</b></div>';
						$html .= '<div>4.Yêu cầu: <br/>'.$data->order_note.'</div></br>';
						
						$html .= '<div><b>Sản phẩm bạn đã mua:</b></div>';
						$html .= '<div>'.$str_content.'</div>';
					}
				}
			}
		}
		return json_encode($html);
	}
	public function pageMember(){
        if(!Session::has('member')){
            return Redirect::route('site.index');
        }
        $meta_title = 'Thành viên';
        $meta_keywords = 'Thành viên';
        $meta_description = 'Thành viên';
        SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);

        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_news;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;
        $paging = '';
        $catid = CGlobal::catIDPromotions;
        if($catid > 0){
            $dataCate = Category::getById($catid);
            $search['news_cat_alias'] = $dataCate->category_title_alias;
            $search['news_catid'] = $catid;
            $search['news_status'] = CGlobal::status_show;
            $search['field_get'] = 'news_id,news_title,news_catid,news_cat_alias,news_intro,news_content,news_image,news_created,news_status';
            $data = News::searchByCondition($search, $limit, $offset, $total);
            $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
            $dataFieldProduct['field_get'] = 'product_id,product_title,product_image,product_price_normal,product_price,product_catid,product_focus,product_order_no,product_created,product_status';
        }

        return view('site.member.pageMember',['dataCate'=>$dataCate, 'data'=>$data, 'paging'=>$paging]);
    }

	public function loginFacebook(){

        $fb = new Facebook([
                'app_id' => CGlobal::facebook_app_id,
                'app_secret' => CGlobal::facebook_app_secret,
                'default_graph_version' => CGlobal::facebook_default_graph_version,
                'persistent_data_handler' => CGlobal::facebook_persistent_data_handler
            ]);
		 
		$helper = $fb->getRedirectLoginHelper();
		try{
			$accessToken = $helper->getAccessToken();
		}catch(FacebookResponseException $e) {
			//When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		}catch(FacebookSDKException $e) {
            //When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		 
		if (!isset($accessToken)) {
			$permissions = array('public_profile','email'); //Optional permissions
			$loginUrl = $helper->getLoginUrl(Config::get('config.BASE_URL').'facebooklogin', $permissions);
			header("Location: ".$loginUrl);
			exit;
		}
		 
		try{
			//Returns a 'Facebook\FacebookResponse' object
			$fields = array('id', 'name', 'email','first_name', 'last_name', 'birthday', 'gender', 'locale');
			$response = $fb->get('/me?fields='.implode(',', $fields).'', $accessToken);
		}catch(FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		}catch(FacebookResponseException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

        $user = $response->getDecodedBody();

		if(sizeof($user) > 0){
			$data = array();
			if(isset($user['id'])){
				$data['member_id_facebook'] = $user['id'];
			}
			if(isset($user['email'])){
				$data['member_mail'] = $user['email'];
			}
			if(isset($user['name'])){
				$data['member_full_name'] = $user['name'];
			}
			
			if(isset($data['member_id_facebook']) && $data['member_id_facebook'] != ''){
				if(isset($data['member_mail']) && $data['member_mail'] != ''){
					$member = Member::getMemberByEmail($data['member_mail']);
					if(sizeof($member) > 0){
						if(($member->member_id_facebook == '' || $member->member_id_facebook == null) && $member->member_status != CGlobal::status_hide){
							$dataUpdate = array(
									'member_id_facebook' => $data['member_id_facebook'],
									'member_last_login' => time(),
									'member_last_ip' => Request::getClientIp(),
							);
							Member::updateData($member->member_id, $dataUpdate);
							$member = Member::getMemberByEmail($data['member_mail']);
						}
					}else{
						$data['member_created'] = time();
						$data['member_status'] = CGlobal::status_show;
						$data['member_last_ip'] = Request::getClientIp();
						$data['member_last_login'] = time();
						$data['member_phone'] = '';
						$data['member_address'] = '';
						Member::addData($data);
						$member = Member::getMemberByIdFacebook($data['member_id_facebook']);
					}
					Session::put('member', $member, 60*24);
					Session::save();
				}else{
					$data['member_created'] = time();
					$data['member_status'] = CGlobal::status_show;
					$data['member_last_ip'] = Request::getClientIp();
					$data['member_last_login'] = time();
					$data['member_phone'] = '';
					$data['member_address'] = '';
					Member::addData($data);
					$member = Member::getMemberByIdFacebook($data['member_id_facebook']);
					Session::put('member', $member, 60*24);
					Session::save();
				}
			}
			echo '<script>window.close();</script>';die;
		}
	}
	public function loginGoogle(){
		$client_id = CGlobal::googe_client_id;
		$client_secret = CGlobal::googe_client_secret;
		$redirect_uri = Config::get('config.BASE_URL').'googlelogin';

		$client = new \Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("email");
		$client->addScope("profile");

		$service = new \Google_Service_Oauth2($client);
		$access_token = '';
		$member = array();
		if(isset($_GET['code'])){
			$client->authenticate($_GET['code']);
			$access_token = $client->getAccessToken();
			$client->setAccessToken($access_token);
			$user = $service->userinfo->get();

            if(sizeof($user) > 0){
				$data = array();
				if(isset($user['id'])){
					$data['member_id_google'] = $user['id'];
				}
				if(isset($user['email'])){
					$data['member_mail'] = $user['email'];
				}
				if(isset($user['name'])){
					$data['member_full_name'] = $user['name'];
				}
				if(isset($data['member_id_google']) && $data['member_id_google'] != ''){
					if(isset($data['member_mail']) && $data['member_mail'] != ''){
						$member = Member::getMemberByEmail($data['member_mail']);
						if(sizeof($member) > 0){
							if(($member->member_id_google == '' || $member->member_id_google == null) && $member->member_status != CGlobal::status_hide){
								$dataUpdate = array(
										'member_id_google' => $data['member_id_google'],
										'member_last_login' => time(),
										'member_last_ip' => Request::getClientIp(),
								);
								Member::updateData($member->member_id, $dataUpdate);
								$member = Member::getMemberByEmail($data['member_mail']);
							}
						}else{
							$data['member_created'] = time();
							$data['member_status'] = CGlobal::status_show;
							$data['member_last_ip'] = Request::getClientIp();
							$data['member_last_login'] = time();
							$data['member_phone'] = '';
							$data['member_address'] = '';
							Member::addData($data);
							$member = Member::getMemberByEmail($data['member_mail']);
						}
						Session::put('member', $member, 60*24);
						Session::save();
					}else{
						echo '<script>alert("Bạn chưa công khai email!");window.close();</script>';die;
					}
				}
			}
			echo '<script>window.close();</script>';die;
		}else{
			$authUrl = $client->createAuthUrl();
			header("Location: ".$authUrl);
		}
		die;
	}
}