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

class Provice extends Model{
    
    protected $table = 'provice';
    protected $primaryKey = 'provice_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'provice_id', 'provice_title','provice_order_no', 'provice_created', 'provice_status', 'provice_num', 'provice_num_gold_ship', 'provice_num_vnpost',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Provice::where('provice_id','>',0);
	  		
	  		if (isset($dataSearch['provice_title']) && $dataSearch['provice_title'] != '') {
	  			$query->where('provice_title','LIKE', '%' . $dataSearch['provice_title'] . '%');
	  		}
	  		if (isset($dataSearch['provice_status']) && $dataSearch['provice_status'] != -1) {
	  			$query->where('provice_status', $dataSearch['provice_status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('provice_order_no', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_PROVICE_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Provice::where('provice_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_PROVICE_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Provice::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->provice_id) && $data->provice_id > 0){
  					self::removeCacheId($data->provice_id);
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
            $data = new Provice();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->provice_id && Memcache::CACHE_ON){
                	Provice::removeCacheId($data->provice_id);
                }
                return $data->provice_id;
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
    		Provice::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Provice::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Provice::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->provice_id) && $data->provice_id > 0){
  					self::removeCacheId($data->provice_id);
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
  			Cache::forget(Memcache::CACHE_PROVICE_ID.$id);
  			Cache::forget(Memcache::CACHE_ALL_PROVICE);
  		}
  	}
  	
  	//Get List provice
  	public static function getAllProvice($data=array(), $limit=0){
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_PROVICE) : array();
  		try{
  			if(empty($result)){
	  			$query = Provice::where('provice_id','>',0);
	  			$query->where('provice_status', CGlobal::status_show);
	  			$query->orderBy('provice_order_no', 'asc');
	  			 
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
	  				Cache::put(Memcache::CACHE_ALL_PROVICE, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  			}
  			}
  			
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
  	public static function arrProvice($data=array()){
  		$result = array(-1=>'--Chọn--');
  		if(sizeof($data) != 0){
  			foreach($data as $v) {
  				$result[$v->provice_id] = $v->provice_title;
  			}
  		}
  		return $result;
  	}
  	//SITE
}