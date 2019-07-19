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

class Statics extends Model {
    
    protected $table = 'statics';
    protected $primaryKey = 'statics_id';
    public  $timestamps = false;

    protected $fillable = array(
	    		'statics_id', 'statics_catid', 'statics_cat_name', 'statics_cat_alias', 'statics_title', 'statics_intro', 'statics_content',
				'statics_image', 'statics_image_other', 'statics_created', 'statics_order_no', 'statics_status', 'meta_title', 'meta_keywords', 'meta_description');
	//ADMIN
    public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
    	try{
    	  
    		$query = Statics::where('statics_id','>',0);
    	  
    		if (isset($dataSearch['statics_title']) && $dataSearch['statics_title'] != '') {
    			$query->where('statics_title','LIKE', '%' . $dataSearch['statics_title'] . '%');
    		}
    		if (isset($dataSearch['statics_status']) && $dataSearch['statics_status'] != -1) {
    			$query->where('statics_status', $dataSearch['statics_status']);
    		}
    	  	
    		if(isset($dataSearch['statics_catid']) && $dataSearch['statics_catid'] != -1){
    			$catid = $dataSearch['statics_catid'];
    			$arrCat = array($catid);
    			Category::makeListCatId($catid, 0, $arrCat);
    			if(is_array($arrCat) && !empty($arrCat)){
    				$query->whereIn('statics_catid', $arrCat);
    			}
    		}
    		
    		$total = $query->count();
    		$query->orderBy('statics_id', 'desc');
    
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
    	$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_STATICS_ID.$id) : array();
    	try {
    		if(empty($result)){
    			$result = Statics::where('statics_id', $id)->first();
    			if($result && Memcache::CACHE_ON){
    				Cache::put(Memcache::CACHE_STATICS_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
    		$data = Statics::find($id);
    		if($id > 0 && !empty($dataInput)){
    			$data->update($dataInput);
    			if(isset($data->statics_id) && $data->statics_id > 0){
    				self::removeCacheId($data->statics_id);
    				self::removeCacheCatId($data->statics_catid);
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
    		$data = new Statics();
    		if (is_array($dataInput) && count($dataInput) > 0) {
    			foreach ($dataInput as $k => $v) {
    				$data->$k = $v;
    			}
    		}
    		if ($data->save()) {
    			DB::connection()->getPdo()->commit();
    			if($data->statics_id && Memcache::CACHE_ON){
    				Statics::removeCacheId($data->statics_id);
    				self::removeCacheCatId($data->statics_catid);
    			}
    			return $data->statics_id;
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
    		Statics::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Statics::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
    
    }
    
    public static function deleteId($id=0){
    	try {
    		DB::connection()->getPdo()->beginTransaction();
    		$data = Statics::find($id);
    		if($data != null){

				//Remove Img
				$statics_image_other = ($data->statics_image_other != '') ? unserialize($data->statics_image_other) : array();
				if(is_array($statics_image_other) && !empty($statics_image_other)){
					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_STATICS.'/'.$id;
					foreach($statics_image_other as $v){
						if(is_file($path.'/'.$v)){
							@unlink($path.'/'.$v);
						}
					}
					if(is_dir($path)) {
						@rmdir($path);
					}
				}
				//End Remove Img
				$data->delete();
    			if(isset($data->statics_id) && $data->statics_id > 0){
    				self::removeCacheId($data->statics_id);
    				self::removeCacheCatId($data->statics_catid);
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
    		Cache::forget(Memcache::CACHE_STATICS_ID.$id);
    	}
    }
    
    public static function removeCacheCatId($catid=0){
    	if($catid>0){
    		Cache::forget(Memcache::CACHE_STATICS_CAT_ID.$catid);
    	}
    }
    
    //SITE
    public static function searchByConditionCatid($catid=0, $limit=0){
    	$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_STATICS_CAT_ID.$catid) : array();
    	try{
    		if(empty($result)){
    			$query = Statics::where('statics_id','>',0);
    			$query = Statics::where('statics_status','=',CGlobal::status_show);
    			if($catid != -1){
    				$arrCat = array($catid);
    				Category::makeListCatId($catid, 0, $arrCat);
    				if(is_array($arrCat) && !empty($arrCat)){
    					$query->whereIn('statics_catid', $arrCat);
    				}
    			}
    			$total = $query->count();
    			$query->orderBy('statics_order_no', 'asc');
    			 
    			if($limit > 0){
    				$query->take($limit);
    			}
    			$result = $query->get();
    			if($result && Memcache::CACHE_ON){
    				Cache::put(Memcache::CACHE_STATICS_CAT_ID.$catid, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
    			}
    		}
    		return $result;
    	}catch (PDOException $e){
    		throw new PDOException();
    	}
    }
}
