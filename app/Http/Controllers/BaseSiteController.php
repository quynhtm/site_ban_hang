<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\CommentProduct;
use App\Http\Models\Info;
use App\Http\Models\Member;
use App\Http\Models\Type;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\SEOMeta;
use App\Library\PHPDev\ValidForm;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class BaseSiteController extends Controller{
    protected $member = array();

    public function __construct(){
        Loader::loadCSS('libs/fontAwesome/css/font-awesome.min.css', CGlobal::$postHead);
        Loader::loadJS('frontend/js/site.js', CGlobal::$postEnd);
        Loader::loadJS('frontend/js/member.js', CGlobal::$postEnd);
        Loader::loadCSS('frontend/css/member.css', CGlobal::$postHead);
        Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
        Loader::loadJS('frontend/js/cart.js', CGlobal::$postEnd);
        Loader::loadJS('focus/js/jquery.cookie.js', CGlobal::$postEnd);

        $this->middleware(function ($request, $next) {
            $this->member = Member::memberLogin();
            View::share('member', $this->member);

            $numCart = $this->countNumCart();
            View::share('numCart',$numCart);

            return $next($request);
        });

        //List Category
        $dataField['field_get'] = '';
        $arrCategory = Category::getAllCategory(0, array(), 0);
        View::share('arrCategory',$arrCategory);

        //Link static
        $typeId = Type::getIdByKeyword('group_static');
        $dataSearch['field_get'] = 'category_id,category_title';
        $arrCateStatic = Category::getAllCategory($typeId, $dataSearch, 0);
        View::share('arrCateStatic',$arrCateStatic);

        $textHotline = strip_tags(self::viewShareVal('SITE_HOTLINE'));
        View::share('textHotline',$textHotline);
		
        $textaddress = self::viewShareVal('SITE_FOOTER_LEFT');
        View::share('textaddress',$textaddress);

        $textlink = self::viewShareVal('SITE_TEXT_LINK_FOOTER');
        View::share('textlink',$textlink);

        $copyright = self::viewShareVal('SITE_TEXT_COPYRIGHT');
        View::share('copyright',$copyright);

        $keyword = addslashes(Request::get('keyword', ''));
        View::share('keyword',$keyword);
    }
    public static function viewShareVal($key=''){
        $str='';
        if($key != '') {
            $arrStr = Info::getItemByKeyword($key);
            if (sizeof($arrStr) > 0) {
                $str = stripslashes($arrStr->info_content);
            }
        }
       return $str;
    }
	public function page403(){
		$meta_img='';
		$meta_title = $meta_keywords = $meta_description = $txt403 = CGlobal::txt403;
		SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);
		return view('errors.page-403',['txt403'=>$txt403]);
	}
	public function page404(){
        $meta_img='';
        $meta_title = $meta_keywords = $meta_description = $txt404 = CGlobal::txt404;
        SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);
        return view('errors.page-404',['txt404'=>$txt404]);
	}
    public function countNumCart(){
        $cartItem = 0;
        if(Session::has('cart')){
            $data = Session::get('cart');
            foreach($data as $v){
                if(!empty($v)){
                    foreach($v as $num){
                        if($num > 0){
                            $cartItem += $num;
                        }
                    }
                }
            }
        }
        return $cartItem;
    }
    public static function checkBannerShow($data = array()){
        $result = array();
        foreach ($data as $k => $item){
            if ($item->banner_is_rel == 0) {
                $rel = 'rel="nofollow"';
            } else {
                $rel = '';
            }
            if ($item->banner_is_target == 0) {
                $target = 'target="_blank"';
            } else {
                $target = '';
            }
            $banner_is_run_time = 1;
            if ($item->banner_is_run_time == CGlobal::status_hide) {
                $banner_is_run_time = 1;
            }else {
                $banner_start_time = $item->banner_start_time;
                $banner_end_time = $item->banner_end_time;
                $date_current = time();
                if ($banner_start_time > 0 && $banner_end_time > 0 && $banner_start_time <= $banner_end_time) {
                    if ($banner_start_time <= $date_current && $date_current <= $banner_end_time) {
                        $banner_is_run_time = 1;
                    }
                } else {
                    $banner_is_run_time = 0;
                }
            }
            if($item->banner_image != '' && $banner_is_run_time == 1) {
                $_item = array(
                    'banner_id' => $item->banner_id,
                    'banner_intro' => strip_tags(trim($item->banner_intro)),
                    'banner_link' => $item->banner_link,
                    'banner_title_show' => $item->banner_title_show,
                    'banner_image' => $item->banner_image,
                    'rel' => $rel,
                    'target' => $target,
                    'banner_is_run_time' => $banner_is_run_time,
                );
                $result[] = $_item;
            }
        }
        return $result;
    }
}