<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Http\Controllers\Site;
use App\Http\Controllers\BaseSiteController;
use App\Http\Models\Contact;
use App\Http\Models\EmailCustomer;
use App\Http\Models\Info;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\SEOMeta;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\Utility;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class StaticController extends BaseSiteController{
	
	public function __construct(){
		parent::__construct();
	}
	public function pageContact(){

		$arrContact = Info::getItemByKeyword('SITE_CONTACT');
        $arrJoin = Info::getItemByKeyword('SITE_JOIN');
		$messages = Utility::messages('messages');

        if(sizeof($arrContact) > 0){
            $meta_title = $arrContact->meta_title;
            $meta_keywords = $arrContact->meta_keywords;
            $meta_description = $arrContact->meta_description;
            $meta_img = $arrContact->info_img;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $arrContact->info_id, $arrContact->info_img, 550, 0, '', true, true);
            }
            SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);
		}
		
		if(sizeof($_POST) > 0){
            $contact_title = addslashes(Request::get('txtName', ''));
            $contact_phone = addslashes(Request::get('txtMobile', ''));
            $contact_address = addslashes(Request::get('txtAddress', ''));
            $contact_content = addslashes(Request::get('txtMessage', ''));
            $contact_created = time();
            if($contact_title != '' && $contact_phone !=''  && $contact_address !=''  && $contact_content !=''){
                $dataInput = array(
                    'contact_title'=>$contact_title,
                    'contact_phone'=>$contact_phone,
                    'contact_address'=>$contact_address,
                    'contact_content'=>$contact_content,
                    'contact_created'=>$contact_created,
                    'contact_status'=>0
                );
                $query = Contact::addData($dataInput);
                if($query > 0){
                    $messages = Utility::messages('messages', 'Cảm ơn bạn đã gửi thông tin liên hệ. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất!');
                    return Redirect::route('site.pageContact');
                }
            }
		}
        return view('site.content.pageContact',[
                    'arrContact'=>$arrContact,
                    'arrJoin'=>$arrJoin,
                    'messages'=>$messages
                ]);
	}
    public function pageGuide(){
        echo 'Đang cập nhật...';die;
    }
	public function regSubscribe(){
		$mail = addslashes(Request::get('IMail', ''));
		if($mail != ''){
			$checkMail = ValidForm::checkRegexEmail($mail);
			if($checkMail){
				$checkEmailExist = EmailCustomer::getCustomerByEmail($mail);
				if(sizeof($checkEmailExist) == 0){
					$expMail =  explode('@', $mail);
					$data = array('customer_full_name'=>$expMail[0], 'customer_email'=>$mail);
					EmailCustomer::addData($data);
					echo 1;
				}else{
					echo "Email đã tồn tại!";
				}
			}else{
				echo "Email không đúng định dạng!";
			}
		}else{
			echo "Email không đúng định dạng!";
		}
		die;
	}
}
