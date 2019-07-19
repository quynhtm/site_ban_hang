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

class Category extends Model{
    
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'category_id', 'category_parent_id', 'category_type_id', 'category_type_keyword', 'category_title','category_title_alias', 'category_intro', 'category_content',
    		'category_menu', 'category_menu_left', 'category_menu_content', 'category_menu_footer', 'category_created','category_order_no', 'category_status', 'category_image',
    		'meta_title', 'meta_keywords', 'meta_description',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Category::where('category_id','>',0);
	  		
	  		if (isset($dataSearch['category_title']) && $dataSearch['category_title'] != '') {
	  			$query->where('category_title','LIKE', '%' . $dataSearch['category_title'] . '%');
	  		}
	  		if (isset($dataSearch['category_status']) && $dataSearch['category_status'] != -1) {
	  			$query->where('category_status', $dataSearch['category_status']);
	  		}
	  		if (isset($dataSearch['category_type_id']) && $dataSearch['category_type_id'] != -1) {
	  			$query->where('category_type_id', $dataSearch['category_type_id']);
	  		}
	  		if (isset($dataSearch['category_menu']) && $dataSearch['category_menu'] != -1) {
	  			$query->where('category_menu', $dataSearch['category_menu']);
	  		}
	  		if (isset($dataSearch['category_menu_left']) && $dataSearch['category_menu_left'] != -1) {
	  			$query->where('category_menu_left', $dataSearch['category_menu_left']);
	  		}
	  		if (isset($dataSearch['category_menu_content']) && $dataSearch['category_menu_content'] != -1) {
	  			$query->where('category_menu_content', $dataSearch['category_menu_content']);
	  		}
			if (isset($dataSearch['category_menu_footer']) && $dataSearch['category_menu_footer'] != -1) {
				$query->where('category_menu_footer', $dataSearch['category_menu_footer']);
			}
	  		$total = $query->count();
	  		$query->orderBy('category_order_no', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_CATEGORY_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Category::where('category_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_CATEGORY_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Category::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->category_id) && $data->category_id > 0){
  					self::removeCacheId($data->category_id);
  				}
  				if(isset($data->category_type_id) && $data->category_type_id > 0){
  					self::removeCacheTypeId($data->category_type_id);
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
            $data = new Category();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->category_id && Memcache::CACHE_ON){
                	Category::removeCacheId($data->category_id);
                }
                return $data->category_id;
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
    		Category::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Category::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Category::find($id);
  			if($data != null){
  				///Remove Img
  				$category_image = ($data->category_image != '') ? $data->category_image : '';
  				if($category_image != ''){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_CATEGORY.'/'.$id;
  					if(is_file($path.'/'.$data->category_image)){
  						@unlink($path.'/'.$data->category_image);
  					}
  					if(is_dir($path)) {
  						@rmdir($path);
  					}
  				}
  				//End Remove Img
  				$data->delete();
  				if(isset($data->category_id) && $data->category_id > 0){
  					self::removeCacheId($data->category_id);
  				}
  				if(isset($data->category_type_id) && $data->category_type_id > 0){
  					self::removeCacheTypeId($data->category_type_id);
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
  			Cache::forget(Memcache::CACHE_CATEGORY_ID.$id);
  			Cache::forget(Memcache::CACHE_ALL_CATEGORY);
  		}
  	}
  	
  	public static function removeCacheTypeId($type_id=0){
  		if($type_id>0){
  			Cache::forget(Memcache::CACHE_ALL_CATEGORY_BY_TYPE.$type_id);
  		}
  	}
  	
  	public static function getAllCategory($typeid=0, $data=array(), $limit=0){
  		if($typeid > 0){
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_CATEGORY_BY_TYPE.$typeid) : array();
  		}else{
  			$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_CATEGORY) : array();
  		}
  		
  		try{
  			if(empty($result)){
	  			$query = Category::where('category_id', '>', 0);
	  			$query->where('category_status', CGlobal::status_show);
	  			if($typeid > 0){
	  				$query->where('category_type_id', $typeid);
	  			}
	  			
	  			$query->orderBy('category_order_no', 'asc');
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
	  				if($typeid > 0){
	  					Cache::put(Memcache::CACHE_ALL_CATEGORY_BY_TYPE.$typeid, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}else{
	  					Cache::put(Memcache::CACHE_ALL_CATEGORY, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  				}
	  			}
  			}
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		
  		return $result;
  	}
  	
  	//Make List Category
  	public static function makeListCatId($catid=0, $level=0, &$arrCat){
  		$listcat = explode(',', $catid);
  		if(!empty($listcat)){
  			$query = Category::where('category_status', '=', CGlobal::status_show);
  			foreach($listcat as $cat){
  				if($cat != end($listcat)){
  					$query->orWhere('category_parent_id',$cat);
  				}else{
  					$query->where('category_parent_id', $cat);
  				}
  			}
  			$result = $query->get();
  		}
  		if ($result != null){
  			foreach ($result as $k => $v){
  				array_push($arrCat, $v->category_id);
  				self::makeListCatId($v->category_id, $level+1, $arrCat);
  			}
  		}
  		return true;
  	}
  	
  	public static function sortListView($data=array(), $parentId = 0, &$newData){
  	
  		if(!empty($data)){
  			foreach($data as $k=>$v){
  				if($v['category_parent_id'] == $parentId){
  					if($parentId == 0){
  						$newData[$v['category_id']]['parent'] = $data[$k];
  					}else{
  						$newData[$v['category_parent_id']]['sub'][] = $data[$k];
  					}
  					unset($data[$k]);
  					Category::sortListView($data, $v['category_id'], $newData);
  				}
  			}
  		}
  		return array();
  	}
  	//SITE
  	public static function getSubCate($id=0){
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_SUB_CATEGORY.$id) : array();
  		try{
  			if(empty($result)){
  				$query = Category::where('category_parent_id', $id);
  				$query->where('category_status', CGlobal::status_show);
  				$query->orderBy('category_id', 'asc');
  				$fields = (isset($dataField['field_get']) && trim($dataField['field_get']) != '') ? explode(',',trim($dataField['field_get'])): array();
  				if(!empty($fields)){
  					$result = $query->get($fields);
  				}else{
  					$result = $query->get();
  				}
  				if($result && Memcache::CACHE_ON){
  					Cache::put(Memcache::CACHE_SUB_CATEGORY.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
  				}
  			}
  		} catch (PDOException $e) {
  			throw new PDOException();
  		}
  		return $result;
  	}
}