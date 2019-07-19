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
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;

class Type extends Model{
    
    protected $table = 'type';
    protected $primaryKey = 'type_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'type_id', 'type_title', 'type_intro', 'type_keyword','type_order_no', 'type_created', 'type_status',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		$query = Type::where('type_id','>',0);
	  		
	  		if (isset($dataSearch['type_title']) && $dataSearch['type_title'] != '') {
	  			$query->where('type_title','LIKE', '%' . $dataSearch['type_title'] . '%');
	  		}
	  		if (isset($dataSearch['type_status']) && $dataSearch['type_status'] != -1) {
	  			$query->where('type_status', $dataSearch['type_status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('type_order_no', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_TYPE_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Type::where('type_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_TYPE_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Type::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->type_id) && $data->type_id > 0){
  					self::removeCacheId($data->type_id);
  				}
  				if(isset($data->type_keyword) && $data->type_keyword != ''){
  					self::removeCacheKeyword($data->type_keyword);
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
            $data = new Type();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->type_id && Memcache::CACHE_ON){
                	Type::removeCacheId($data->type_id);
                }
                return $data->type_id;
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
    		Type::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Type::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Type::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->type_id) && $data->type_id > 0){
  					self::removeCacheId($data->type_id);
  				}
  				if(isset($data->type_keyword) && $data->info_keyword != ''){
  					self::removeCacheKeyword($data->type_keyword);
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
  			Cache::forget(Memcache::CACHE_TYPE_ID.$id);
  			Cache::forget(Memcache::CACHE_TYPE_ALL);
  		}
  	}
	public static function removeCacheKeyword($keyword=''){
    	if($keyword != ''){
    		Cache::forget(Memcache::CACHE_TYPE_KEYWORD.$keyword);
    	}
    }
  	public static function getIdByKeyword($keyword=''){
  		$id = 0;
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_TYPE_KEYWORD.$keyword) : array();
  		try {
  			if(empty($result)){
  				$result = Type::where('type_keyword', $keyword)->first();
  				if($result && Memcache::CACHE_ON){
  					Cache::put(Memcache::CACHE_TYPE_KEYWORD.$keyword, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
  				}
  			}
			if(sizeof($result) > 0){
				$id = $result->type_id;
			}
  		} catch (PDOException $e) {
  			throw new PDOException();
  		}
  		return $id;
  	}
  	//Get List Type
  	public static function getAllType($data=array(), $limit=0){
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_TYPE_ALL) : array();
  		try{
  			if(empty($result)){
	  			$query = Type::where('type_id','>',0);
	  			$query->where('type_status', CGlobal::status_show);
	  			$query->orderBy('type_order_no', 'asc');
	  			 
	  			$fields = (isset($data['field_get']) && trim($data['field_get']) != '') ? explode(',',trim($data['field_get'])): array();
	  			if(!empty($fields)){
	  				$result = $query->take($limit)->get($fields);
	  			}else{
	  				$result = $query->take($limit)->get();
	  			}
	  			
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_TYPE_ALL, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  			}
  			}
  			
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
}