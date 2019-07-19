<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;

class Contact extends Model {
    
    protected $table = 'contact';
    protected $primaryKey = 'contact_id';
    public  $timestamps = false;

    protected $fillable = array(
	    		'contact_id', 'contact_title', 'contact_email', 'contact_phone', 'contact_address',
	    		'contact_content', 'contact_created', 'contact_status');
	//ADMIN
    public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
    	try{
    	  
    		$query = Contact::where('contact_id','>',0);
    	  
    		if (isset($dataSearch['contact_title']) && $dataSearch['contact_title'] != '') {
    			$query->where('contact_title','LIKE', '%' . $dataSearch['contact_title'] . '%');
    		}
    		if (isset($dataSearch['contact_status']) && $dataSearch['contact_status'] != -1) {
    			$query->where('contact_status', $dataSearch['contact_status']);
    		}
    	  
    		$total = $query->count();
    		$query->orderBy('contact_id', 'desc');
    
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
    	$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_CONTACT_ID.$id) : array();
    	try {
    		if(empty($result)){
    			$result = Contact::where('contact_id', $id)->first();
    			if($result && Memcache::CACHE_ON){
    				Cache::put(Memcache::CACHE_CONTACT_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
    		$data = Contact::find($id);
    		if($id > 0 && !empty($dataInput)){
    			$data->update($dataInput);
    			if(isset($data->contact_id) && $data->contact_id > 0){
    				self::removeCacheId($data->contact_id);
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
    		$data = new Contact();
    		if (is_array($dataInput) && count($dataInput) > 0) {
    			foreach ($dataInput as $k => $v) {
    				$data->$k = $v;
    			}
    		}
    		if ($data->save()) {
    			DB::connection()->getPdo()->commit();
    			if($data->contact_id && Memcache::CACHE_ON){
    				Contact::removeCacheId($data->contact_id);
    			}
    			return $data->contact_id;
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
    		Contact::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Contact::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
    
    }
    
    public static function deleteId($id=0){
    	try {
    		DB::connection()->getPdo()->beginTransaction();
    		$data = Contact::find($id);
    		if($data != null){
    			$data->delete();
    			if(isset($data->contact_id) && $data->contact_id > 0){
    				self::removeCacheId($data->contact_id);
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
    		Cache::forget(Memcache::CACHE_CONTACT_ID.$id);
    	}
    }
    
    //SITE
}
