<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use App\Library\PHPDev\FuncLib;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Member extends Model {
    
    protected $table = 'member';
    protected $primaryKey = 'member_id';
    public  $timestamps = false;

    protected $fillable = array(
	    		'member_id', 'member_pass', 'member_full_name',
	    		'member_phone', 'member_mail', 'member_address','member_last_login', 
	    		'member_last_ip', 'member_created', 'member_status',
    			'member_id_facebook', 'member_id_google'
    			);
	//ADMIN
    public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
    	try{
    	  
    		$query = Member::where('member_id','>',0);
    	  	
    		if (isset($dataSearch['member_mail']) && $dataSearch['member_mail'] != '') {
    			$query->where('member_mail','=', $dataSearch['member_mail']);
    		}
    		if (isset($dataSearch['member_status']) && $dataSearch['member_status'] != -1) {
    			$query->where('member_status', $dataSearch['member_status']);
    		}
    		
    		if (isset($dataSearch['member_id']) && $dataSearch['member_id'] != -1) {
    			$query->where('member_id', $dataSearch['member_id']);
    		}
    	  	
    		$total = $query->count();
    		$query->orderBy('member_id', 'desc');
    
    		$fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
    		if(!empty($fields)){
    			$result = $query->take($limit)->skip($offset)->get($fields);
    		}else{
    			$result = $query->take($limit)->skip($offset)->get();
    		}
    		return $result;
    
    	}catch (PDOException $e){
    		throw new PDOException();
    	}
    }
     
    public static function getById($id=0){
    	$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_MEMBER_ID.$id) : array();
    	try {
    		if(empty($result)){
    			$result = Member::where('member_id', $id)->first();
    			if($result && Memcache::CACHE_ON){
    				Cache::put(Memcache::CACHE_MEMBER_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
    			}
    		}
    	} catch (PDOException $e) {
    		throw new PDOException();
    	}
    	return $result;
    }
     
    public static function updateData($id=0, $dataInput=array()){
    	try {
    		DB::connection()->getPdo()->beginTransaction();
    		$data = Member::find($id);
    		if($id > 0 && !empty($dataInput)){
    			$data->update($dataInput);
    			if(isset($data->member_id) && $data->member_id > 0){
    				self::removeCacheId($data->member_id);
    			}
    		}
    		DB::connection()->getPdo()->commit();
    		return true;
    	} catch (PDOException $e) {
    		DB::connection()->getPdo()->rollBack();
    		throw new PDOException();
    	}
    }
     
    public static function addData($dataInput=array()){
    	try {
    		DB::connection()->getPdo()->beginTransaction();
    		$data = new Member();
    		if (is_array($dataInput) && count($dataInput) > 0) {
    			foreach ($dataInput as $k => $v) {
    				$data->$k = $v;
    			}
    		}
    		if ($data->save()) {
    			DB::connection()->getPdo()->commit();
    			if($data->member_id && Memcache::CACHE_ON){
    				Member::removeCacheId($data->member_id);
    			}
    			return $data->member_id;
    		}
    		DB::connection()->getPdo()->commit();
    		return false;
    	} catch (PDOException $e) {
    		DB::connection()->getPdo()->rollBack();
    		throw new PDOException();
    	}
    }
    
    public static function saveData($id=0, $data=array()){
    	$data_post = array();
    	if(!empty($data)){
    		foreach($data as $key=>$val){
    			$data_post[$key] = $val['value'];
    		}
    	}
    	if($id > 0){
    		Member::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Member::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
    
    }
    
    public static function deleteId($id=0){
    	try {
    		DB::connection()->getPdo()->beginTransaction();
    		$data = Member::find($id);
    		if($data != null){
    			$data->delete();
    			if(isset($data->member_id) && $data->member_id > 0){
    				self::removeCacheId($data->member_id);
    			}
    			DB::connection()->getPdo()->commit();
    		}
    		return true;
    	} catch (PDOException $e) {
    		DB::connection()->getPdo()->rollBack();
    		throw new PDOException();
    	}
    }
    
    public static function removeCacheId($id=0){
    	if($id>0){
    		Cache::forget(Memcache::CACHE_MEMBER_ID.$id);
    	}
    }
    //Check Member...
    public static function getMemberByCond($id, $mail){
    	$result = Member::where('member_id', $id)->where('member_mail', $mail)->first();
    	return $result;
    }
    public static function getMemberByEmail($mail){
        $result = Member::where('member_mail', $mail)->first();
        return $result;
    }
    
    public static function getMemberByIdFacebook($member_id_facebook){
    	$result = Member::where('member_id_facebook', $member_id_facebook)->first();
    	return $result;
    }
	
    public static function encode_password($password){
        return md5(md5($password));
    }

    public static function updateLogin($member = array()){
        if($member){
            $member->member_last_login = time();
            $member->member_last_ip = Request::getClientIp();
            $member->save();
        }
    }
    public static function isLogin(){
    	$result = false;
    	if(Session::has('member')) {
    		$result = true;
    	}
    	return $result;
    }
    public static function memberLogin(){
    	$member = array();
    	if(Session::has('member')){
    		$member = Session::get('member');
    	}
    	return $member;
    }
}
