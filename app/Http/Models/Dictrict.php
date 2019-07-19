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

class Dictrict extends Model{
    
    protected $table = 'dictrict';
    protected $primaryKey = 'dictrict_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'dictrict_id', 'provice_id', 'dictrict_title','dictrict_order_no', 'dictrict_created', 
    		'dictrict_status', 'dictrict_num', 'dictrict_num_gold_ship', 'dictrict_num_vnpost',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Dictrict::where('dictrict_id','>',0);
	  		
	  		if (isset($dataSearch['dictrict_title']) && $dataSearch['dictrict_title'] != '') {
	  			$query->where('dictrict_title','LIKE', '%' . $dataSearch['dictrict_title'] . '%');
	  		}
	  		if (isset($dataSearch['dictrict_status']) && $dataSearch['dictrict_status'] != -1) {
	  			$query->where('dictrict_status', $dataSearch['dictrict_status']);
	  		}
	  		
	  		if (isset($dataSearch['provice_id']) && $dataSearch['provice_id'] != -1) {
	  			$query->where('provice_id','=', $dataSearch['provice_id']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('dictrict_id', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_DICTRICT_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Dictrict::where('dictrict_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_DICTRICT_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Dictrict::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->dictrict_id) && $data->dictrict_id > 0){
  					self::removeCacheId($data->dictrict_id);
  				}
  				if(isset($data->provice_id) && $data->provice_id > 0){
  					self::removeCacheByProviceId($data->provice_id);
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
            $data = new Dictrict();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->dictrict_id && Memcache::CACHE_ON){
                	dictrict::removeCacheId($data->dictrict_id);
                }
                if(isset($data->provice_id) && $data->provice_id > 0){
                	self::removeCacheByProviceId($data->provice_id);
                }
                return $data->dictrict_id;
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
    		Dictrict::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Dictrict::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Dictrict::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->dictrict_id) && $data->dictrict_id > 0){
  					self::removeCacheId($data->dictrict_id);
  				}
  				if(isset($data->provice_id) && $data->provice_id > 0){
  					self::removeCacheByProviceId($data->provice_id);
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
  			Cache::forget(Memcache::CACHE_DICTRICT_ID.$id);
  			Cache::forget(Memcache::CACHE_ALL_DICTRICT);
  		}
  	}
  	
  	public static function removeCacheByProviceId($provice_id=0){
  		if($provice_id>0){
  			Cache::forget(Memcache::CACHE_ALL_DICTRICT_BY_PROVICE.$provice_id);
  		}
  	}
  	
  	//Get List dictrict
  	public static function getAllDictrict($data=array(), $limit=0, $provice_id=-1){
  		if($provice_id > -1){
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_DICTRICT_BY_PROVICE.$provice_id) : array();
  		}else{
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_DICTRICT) : array();
  		}
  		try{
  			if(empty($result)){
	  			$query = Dictrict::where('dictrict_id','>',0);
	  			
	  			if($provice_id > -1){
	  				$query->where('provice_id', '=', $provice_id);
	  			}
	  			
	  			$query->where('dictrict_status', CGlobal::status_show);
	  			$query->orderBy('dictrict_id', 'asc');
	  			 
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
	  				if($provice_id > -1){
	  					Cache::put(Memcache::CACHE_ALL_DICTRICT_BY_PROVICE.$provice_id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}else{
	  					Cache::put(Memcache::CACHE_ALL_DICTRICT, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}
	  			}
  			}
  			
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
  	public static function arrDictrict($data=array()){
  		$result = array(-1=>'--Chọn--');
  		if(sizeof($data) != 0){
  			foreach($data as $v) {
  				$result[$v->dictrict_id] = $v->dictrict_title;
  			}
  		}
  		return $result;
  	}
  	//SITE
}