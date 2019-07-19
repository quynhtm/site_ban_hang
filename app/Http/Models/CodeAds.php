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

class CodeAds extends Model{
    
    protected $table = 'order_ads';
    protected $primaryKey = 'code_ads_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'code_ads_id', 'code_ads_title','code_ads_content', 'code_ads_price', 'code_ads_created', 'code_ads_status',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = CodeAds::where('code_ads_id','>',0);
	  		
	  		if (isset($dataSearch['code_ads_title']) && $dataSearch['code_ads_title'] != '') {
	  			$query->where('code_ads_title','LIKE', '%' . $dataSearch['code_ads_title'] . '%');
	  		}
	  		if (isset($dataSearch['code_ads_status']) && $dataSearch['code_ads_status'] != -1) {
	  			$query->where('code_ads_status', $dataSearch['code_ads_status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('code_ads_id', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_CODE_ADS_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = CodeAds::where('code_ads_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_CODE_ADS_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = CodeAds::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->code_ads_id) && $data->code_ads_id > 0){
  					self::removeCacheId($data->code_ads_id);
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
            $data = new CodeAds();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->code_ads_id && Memcache::CACHE_ON){
                	CodeAds::removeCacheId($data->code_ads_id);
                }
                return $data->code_ads_id;
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
    		CodeAds::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		CodeAds::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = CodeAds::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->code_ads_id) && $data->code_ads_id > 0){
  					self::removeCacheId($data->code_ads_id);
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
  			Cache::forget(Memcache::CACHE_CODE_ADS_ID.$id);
  			Cache::forget(Memcache::CACHE_ALL_CODE_ADS);
  		}
  	}
  	
  	//Get List CodeAds
  	public static function getAllCodeAds($data=array(), $limit=0){
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_CODE_ADS) : array();
  		try{
  			if(empty($result)){
	  			$query = CodeAds::where('code_ads_id','>',0);
	  			$query->where('code_ads_status','<>', -1);
	  			$query->orderBy('code_ads_id', 'asc');
	  			 
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
	  				Cache::put(Memcache::CACHE_ALL_CODE_ADS, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  			}
  			}
  			
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
  	public static function arrCodeAds($data=array()){
  		$result = array(-1=>'--Chọn mã quảng cáo--');
  		if(sizeof($data) != 0){
  			foreach($data as $v) {
  				$result[$v->code_ads_id] = $v->code_ads_title;
  			}
  		}
  		return $result;
  	}
  	//SITE
}