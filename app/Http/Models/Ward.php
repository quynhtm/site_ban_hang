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

class Ward extends Model{
    
    protected $table = 'ward';
    protected $primaryKey = 'ward_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'ward_id', 'ward_title', 'provice_id', 'dictrict_id', 'ward_order_no', 'ward_created', 'ward_status', 'ward_num', 'ward_num_gold_ship', 'ward_num_vnpost',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Ward::where('ward_id','>',0);
	  		
	  		if (isset($dataSearch['ward_title']) && $dataSearch['ward_title'] != '') {
	  			$query->where('ward_title','LIKE', '%' . $dataSearch['ward_title'] . '%');
	  		}
	  		if (isset($dataSearch['ward_status']) && $dataSearch['ward_status'] != -1) {
	  			$query->where('ward_status', $dataSearch['ward_status']);
	  		}
	  		
	  		if (isset($dataSearch['provice_id']) && $dataSearch['provice_id'] != -1) {
	  			$query->where('provice_id','=',$dataSearch['provice_id']);
	  		}
	  		
	  		if (isset($dataSearch['dictrict_id']) && $dataSearch['dictrict_id'] != -1) {
	  			$query->where('dictrict_id','=',$dataSearch['dictrict_id']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('provice_id', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_WARD_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Ward::where('ward_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_WARD_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Ward::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->ward_id) && $data->ward_id > 0){
  					self::removeCacheId($data->ward_id);
  				}
  				if(isset($data->dictrict_id) && $data->dictrict_id > 0){
  					self::removeCacheByDictrictId($data->dictrict_id);
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
            $data = new ward();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->ward_id && Memcache::CACHE_ON){
                	Ward::removeCacheId($data->ward_id);
                }
                if(isset($data->dictrict_id) && $data->dictrict_id > 0){
                	self::removeCacheByDictrictId($data->dictrict_id);
                }
                return $data->ward_id;
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
    		Ward::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Ward::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Ward::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->ward_id) && $data->ward_id > 0){
  					self::removeCacheId($data->ward_id);
  				}
  				if(isset($data->dictrict_id) && $data->dictrict_id > 0){
  					self::removeCacheByDictrictId($data->dictrict_id);
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
  			Cache::forget(Memcache::CACHE_WARD_ID.$id);
  			Cache::forget(Memcache::CACHE_ALL_WARD);
  		}
  	}
  	public static function removeCacheByDictrictId($dictrict_id=0){
  		if($dictrict_id>0){
  			Cache::forget(Memcache::CACHE_ALL_WARD_BY_DICTRICT.$dictrict_id);
  		}
  	}
  	
  	//Get List ward
  	public static function getAllWard($data=array(), $limit=0, $dictrictId=-1){
  		if($dictrictId > -1){
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_WARD_BY_DICTRICT.$dictrictId) : array();
  		}else{
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_WARD) : array();
  		}
  		
  		try{
  			if(empty($result)){
	  			$query = Ward::where('ward_id','>',0);
	  			if($dictrictId > -1){
	  				$query->where('dictrict_id', '=', $dictrictId);
	  			}
	  			$query->where('ward_status', CGlobal::status_show);
	  			$query->orderBy('ward_order_no', 'asc');
	  			 
	  			$fields = (isset($data['field_get']) && trim($data['field_get']) != '') ? explode(',',trim($data['field_get'])): array();
	  			
	  			if($limit > 0){
	  				$query->take($limit);
	  			}
	  			
	  			if(!empty($fields)){
	  				$result = $query->get($fields);
	  			}else{
	  				$result = $query->get();
	  			}
	  			
	  			if($result && Memcache::CACHE_ON){
	  				if($dictrictId > -1){
	  					Cache::put(Memcache::CACHE_ALL_WARD_BY_DICTRICT.$dictrictId, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}else{
	  					Cache::put(Memcache::CACHE_ALL_WARD, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}
	  			}
  			}
  			
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
  	public static function arrWard($data=array()){
  		$result = array(-1=>'--Chọn--');
  		if(sizeof($data) != 0){
  			foreach($data as $v) {
  				$result[$v->ward_id] = $v->ward_title;
  			}
  		}
  		return $result;
  	}
  	//SITE
}