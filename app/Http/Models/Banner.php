<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;

class Banner extends Model{
    
    protected $table = 'banner';
    protected $primaryKey = 'banner_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'banner_id', 'banner_title', 'banner_title_show', 'banner_intro', 'banner_image','banner_link', 'banner_order_no', 'banner_is_target',
    		'banner_is_rel', 'banner_type', 'banner_status', 'banner_is_run_time','banner_start_time', 'banner_end_time', 'banner_create_time',
    );
    
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Banner::where('banner_id','>',0);
	  		
	  		if (isset($dataSearch['banner_title']) && $dataSearch['banner_title'] != '') {
	  			$query->where('banner_title','LIKE', '%' . $dataSearch['banner_title'] . '%');
	  		}
	  		if (isset($dataSearch['banner_status']) && $dataSearch['banner_status'] != -1) {
	  			$query->where('banner_status', $dataSearch['banner_status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('banner_id', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_BANNER_ID.$id) : array();
  		try {
	  		if(empty($result)){
	  			$result = Banner::where('banner_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_BANNER_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Banner::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->banner_id) && $data->banner_id > 0){
  					self::removeCacheId($data->banner_id);
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
            $data = new Banner();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->banner_id && Memcache::CACHE_ON){
                	Banner::removeCacheId($data->banner_id);
                }
                return $data->banner_id;
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
    		Banner::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Banner::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Banner::find($id);
  			if($data != null){
  				//Remove Img
  				$banner_image = ($data->banner_image != '') ? $data->banner_image : '';
  				if($banner_image != ''){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_BANNER.'/'.$id;
  					if(is_file($path.'/'.$data->banner_image)){
  						@unlink($path.'/'.$data->banner_image);
  					}
  					if(is_dir($path)) {
  						@rmdir($path);
  					}
  				}
  				//End Remove Img
  				$data->delete();
  				if(isset($data->banner_id) && $data->banner_id > 0){
  					self::removeCacheId($data->banner_id);
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
    		Cache::forget(Memcache::CACHE_BANNER_ID.$id);
    		Cache::forget(Memcache::CACHE_BANNER_SITE);
    		//Banner: Slider, Right
    		Cache::forget(Memcache::CACHE_BANNER_SITE.'slider');
    		Cache::forget(Memcache::CACHE_BANNER_SITE.'right');
    		Cache::forget(Memcache::CACHE_BANNER_SITE.'duoiSliderIndex');
    		Cache::forget(Memcache::CACHE_BANNER_SITE.'trans');
    		Cache::forget(Memcache::CACHE_BANNER_SITE.'pNew');
    	}
    }
  	
  	//SITE
  	public static function getBannerSite($dataSearch=array(), $limit=0, $txt=''){
  		if($limit==0){
  			$limit = 10;
  		}
  		if($txt != ''){
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_BANNER_SITE.$txt) : array();
  		}else{
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_BANNER_SITE) : array();
  		}
  		
  		try {
  			if(empty($result)){
  				$query = Banner::where('banner_id','>',0);
  				
  				if(isset($dataSearch['banner_type']) && $dataSearch['banner_type'] != -1) {
  					$query->where('banner_type', $dataSearch['banner_type']);
  				}
  				if(isset($dataSearch['banner_status']) && $dataSearch['banner_status'] != -1) {
  					$query->where('banner_status', $dataSearch['banner_status']);
  				}
  				
  				$query->orderBy('banner_order_no', 'asc');
  						
  				$fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
  				if(!empty($fields)){
  					$result = $query->take($limit)->get($fields);
  				}else{
  					$result = $query->take($limit)->get();
  				}
  				
  				if($result && Memcache::CACHE_ON){
  					if($txt != ''){
  						Cache::put(Memcache::CACHE_BANNER_SITE.$txt, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
  					}else{
  						Cache::put(Memcache::CACHE_BANNER_SITE, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
  					}
  				}
  			}
  		} catch (PDOException $e) {
  			throw new PDOException();
  		}
  	
  		return $result;
  	}
}