<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Library\PHPDev;

use Illuminate\Support\Facades\URL;

class FuncLib{
	static function bug($data,$die=true){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		if($die){die;}
	}
	static function post_db_parse_html($t=""){
		if ( $t == "" ){
			return $t;
		}
		$t = str_replace( "&#39;"   , "'", $t );
		$t = str_replace( "&#33;"   , "!", $t );
		$t = str_replace( "&#036;"   , "$", $t );
		$t = str_replace( "&#124;"  , "|", $t );
		$t = str_replace( "&amp;"   , "&", $t );
		$t = str_replace( "&gt;"    , ">", $t );
		$t = str_replace( "&lt;"    , "<", $t );
		$t = str_replace( "&quot;"  , '"', $t );
	
		//-----------------------------------------
		// Take a crack at parsing some of the nasties
		// NOTE: THIS IS NOT DESIGNED AS A FOOLPROOF METHOD
		// AND SHOULD NOT BE RELIED UPON!
		//-----------------------------------------
	
		$t = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $t );
		$t = preg_replace( "/alert/i"      , "&#097;lert"          , $t );
		$t = preg_replace( "/about:/i"     , "&#097;bout:"         , $t );
		$t = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $t );
		$t = preg_replace( "/onmouseout/i", "&#111;nmouseout"    , $t );
		$t = preg_replace( "/onclick/i"    , "&#111;nclick"        , $t );
		$t = preg_replace( "/onload/i"     , "&#111;nload"         , $t );
		$t = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $t );
		$t = preg_replace( "/object/i"   , "&#111;bject"       , $t );
		$t = preg_replace( "/frame/i"   , "fr&#097;me"       , $t );
		$t = preg_replace( "/applet/i"   , "&#097;pplet"       , $t );
		$t = preg_replace( "/meta/i"   , "met&#097;"       , $t );
	
		return $t;
	}
	
	static function stripUnicode($str){
		if(!$str) return false;
		$marTViet = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",
            "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
            , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ");

		$marKoDau=array("a","a","a","a","a","a","a","a","a","a","a"
		,"a","a","a","a","a","a",
		"e","e","e","e","e","e","e","e","e","e","e",
		"i","i","i","i","i",
		"o","o","o","o","o","o","o","o","o","o","o","o"
		,"o","o","o","o","o",
		"u","u","u","u","u","u","u","u","u","u","u",
		"y","y","y","y","y",
		"d",
		"A","A","A","A","A","A","A","A","A","A","A","A"
		,"A","A","A","A","A",
		"E","E","E","E","E","E","E","E","E","E","E",
		"I","I","I","I","I",
		"O","O","O","O","O","O","O","O","O","O","O","O"
		,"O","O","O","O","O",
		"U","U","U","U","U","U","U","U","U","U","U",
		"Y","Y","Y","Y","Y",
		"D");

		$str = str_replace($marTViet,$marKoDau,$str);
		return $str;
	}
	static function _name_cleaner($name,$replace_string="_"){
		return preg_replace( "/[^a-zA-Z0-9\-\_]/", $replace_string , $name );
	}
	
	//Cac ky sap xep gan nhau
	static function safeTitle($text) {
		$text = FuncLib::post_db_parse_html($text);
		$text = FuncLib::stripUnicode($text);
		$text = self::_name_cleaner($text, "-");
		$text = str_replace("----", "-", $text);
		$text = str_replace("---", "-", $text);
		$text = str_replace("--", "-", $text);
		$text = trim($text, '-');

		if ($text) {
			return strtolower($text);
		} else {
			return ' ';
		}
	}
	//Number Format
	static function numberFormat($number = 0){
		if ($number >= 1000) {
			return number_format($number, 0, ',', '.');
		}
		return $number;
	}
	//Tinh khoang thoi gian
	static function showTimeAgo($timeAgo=0){
		$result = '';
		if($timeAgo > 0){
			$seconds = $timeAgo ;
			$minutes = round($timeAgo / 60 );
			$hours = round($timeAgo / 3600);
			$days = round($timeAgo / 86400 );
			$weeks = round($timeAgo / 604800);
			$months = round($timeAgo / 2600640);
			$years = round($timeAgo / 31207680);
			// Seconds
			if($seconds <= 60) {
                $result = 'Cách đây '.$seconds.' giây';
			}
			//Minutes
			else if($minutes <= 60) {
				if($minutes == 1) {
                    $result = '1 phút';
				}else {
                    $result = $minutes.' phút';
				}
			}
			//Hours
			else if($hours <= 24) {
				if($hours == 1) {
                    $result = '1 tiếng';
				}else{
                    $result = $hours.' tiếng';
				}
			}
			//Days
			else if($days <= 7) {
				if($days == 1) {
                    $result = 'Ngày hôm qua';
				}else {
                    $result = $days.' ngày';
				}
			}
			//Weeks
			else if($weeks <= 4.3) {
				if($weeks==1) {
                    $result = '1 tuần';
				}else {
                    $result = $weeks.' tuần';
				}
			}
			//Months
			else if($months <= 12) {
				if($months == 1) {
                    $result = '1 tháng';
				}else {
                    $result = $months.' tháng';
				}
			}
			//Years
			else {
				if($years == 1) {
                    $result = '1 năm';
				}else{
                    $result = $years.' năm';
				}
			}
		}
		return $result;
	}
    //Replace từ vi tri text toi vi tri khac
    public static function replaceText($str='', $start_post=0, $end_post=0, $str_replace=''){
        if($str != ''){
            $checkMail = ValidForm::checkRegexEmail($str);
            if($checkMail == true){
                $arrEXP = explode('@', $str);
                if(isset($arrEXP[1]) && $arrEXP[1] != ''){
                    $arrEXP[1] = $str_replace;
                    $str = implode('@', $arrEXP);
                }
            }else{
                $len = strlen($str);
                if($len >= $start_post){
                    $_str = substr($str, $start_post, $end_post);
                    $_len = strlen($_str);
                    if($_len <= $len){
                        $str = str_replace($_str, $str_replace, $str);
                    }
                }
            }
        }
        return $str;
    }
	//Get https or http
	public static function getBaseUrl(){
		if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$protocol = 'https://';
		}else {
			$protocol = 'http://';
		}
		$base_url = str_replace('\\','/',$protocol . $_SERVER['HTTP_HOST'] . (dirname($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : ''));
		$base_url .= $base_url[strlen($base_url)-1] != '/' ? '/' : '';
		return $base_url;
	}
	//Get root path
	public static function getRootPath(){
		$dir_root = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT'] . (dirname($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : ''));
		$dir_root .= $dir_root[strlen($dir_root)-1] != '/' ? '/' : '';
		return $dir_root;
	}
	//Buid Link Category
	static function buildLinkCategory($cat_id = 0, $cat_title = 'Danh-mục'){
		if($cat_id > 0){
			return URL::route('site.actionRouter', array('id'=>$cat_id, 'name'=>strtolower(FuncLib::safeTitle($cat_title))));
		}
		return '#';
	}
	//Buid Link News Detail
	static function buildLinkDetailNews($id = 0, $news_title = 'Chi-tiet'){
		if($id > 0){
			return URL::route('site.detailNews', array('id'=>$id, 'name'=>strtolower(FuncLib::safeTitle($news_title))));
		}
		return '#';
	}
	//Buid Link Product Detail
	static function buildLinkDetailProduct($id = 0, $news_title = 'Chi-tiet'){
		if($id > 0){
			return URL::route('site.detailProduct', array('id'=>$id, 'name'=>strtolower(FuncLib::safeTitle($news_title))));
		}
		return '#';
	}
	//Buid Link Statics Detail
	static function buildLinkDetailStatic($id = 0, $statics_title = 'st'){
		if($id > 0){
			return URL::route('site.detailStatics', array('id'=>$id, 'name'=>strtolower(FuncLib::safeTitle($statics_title))));
		}
		return '#';
	}
}