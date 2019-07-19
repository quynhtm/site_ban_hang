<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Login;

use App\Http\Models\UserRolePermission;
use App\Library\PHPDev\FuncLib;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Models\User;
use App\Http\Models\UserRole;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\CGlobal;

class LoginController extends Controller{
	public function __construct(){
		Loader::loadCSS('backend/css/login.css', CGlobal::$postHead);
	}
    public function getLogin($url=''){
        if(Session::has('user')){
            if ($url === '' || $url === 'user'){
                return Redirect::route('admin.dashboard');
    		}else{
                return Redirect::to(self::buildUrlDecode($url));
    		}
    	}else{
            return view('admin.login.login');
    	}
    }
    public function postLogin($url=''){
        if(Session::has('user')){
			if ($url === '' || $url === 'user'){
                return Redirect::route('admin.dashboard');
    		}else{
                return Redirect::to(self::buildUrlDecode($url));
    		}
    	}

    	$token = Request::get('_token', '');
    	$name = Request::get('name', '');
    	$pass = Request::get('password', '');
    	$error = '';

    	if(Session::token() === $token){
	    	if($name != '' && $pass != ''){
				if (strlen($name) < 5 || strlen($name) > 50 || preg_match('/[^A-Za-z0-9_\.@]/', $name) || strlen($pass) < 5) {
	    			$error = 'Không tồn tại tên đăng nhập!';
	    		}else{
					$user = User::getUserByName($name);
                    if($user != ''){
	    				if($user->user_status == 0 || $user->user_status == -1){
	    					$error = 'Tài khoản đang bị khóa!';
	    				}elseif($user->user_status == 1){
	    					$encode_password = User::encode_password($pass);
                            if($user->user_pass == $encode_password){
                                $data = array(
		    								'user_id' => $user->user_id,
		    								'user_name' => $user->user_name,
	    									'user_full_name' => $user->user_full_name,
	    									'user_phone' => $user->user_phone,
		    								'user_mail' => $user->user_mail,
	    									'user_status' => $user->user_status,
	    									'user_created' => $user->user_created,
		    								'user_rid' => $user->user_rid,
	    								);
                                $permission_code = array();
								if($user->user_rid != ''){
                                    $rid = explode(',', $user->user_rid);
                                    if(sizeof($rid)) {
                                        $permission = UserRolePermission::getListPermissionByRoleId($rid);
                                        if(sizeof($permission) > 0){
                                            foreach($permission as $v){
                                                $permission_code[] = $v->permission_code;
                                            }
                                        }
                                    }
								}
                                $data['user_permission'] = $permission_code;

                                Session::put('user', $data, 6*24);
	    						User::updateLogin($user);
                                if ($url === '' || $url === 'user') {
                                    return Redirect::route('admin.dashboard');
	    						}else{
                                    return Redirect::to(self::buildUrlDecode($url));
	    						}
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
    	}
		return view('admin.login.login',['error'=>$error, 'name'=>$name]);
    }
    public function logout(){
        if(Session::has('user')){
            Session::forget('user');
        }
        return Redirect::route('login', array('url' => self::buildUrlEncode(URL::previous())));
    }
}