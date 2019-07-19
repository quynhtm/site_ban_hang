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

class Product extends Model{
    
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'product_id', 'product_title', 'product_title_alias', 'product_code', 'product_code_factory','product_intro', 'product_content',
    		'product_catid', 'product_cat_alias', 'product_cat_name','product_wholesale', 'product_price','product_price_normal', 
    		'product_price_input', 'product_focus', 'product_size_no', 'product_order_no', 'product_image', 'product_image_hover', 'product_image_other',
			'product_khuyenmai', 'product_giamgia', 'product_moi',
			'product_created', 'product_view_num', 'product_supplier', 'product_status', 'product_sale', 'meta_title', 'meta_keywords', 'meta_description'
    		
    );
    
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Product::where('product_id','>',0);
	  		
	  		if (isset($dataSearch['product_title']) && $dataSearch['product_title'] != '') {
	  			$query->where('product_title','LIKE', '%' . $dataSearch['product_title'] . '%');
	  		}

			if (isset($dataSearch['keyword']) && $dataSearch['keyword'] != '') {
				$query->where('product_title','LIKE', '%' . $dataSearch['keyword'] . '%');
			}

			if (isset($dataSearch['product_code']) && $dataSearch['product_code'] != '') {
				$query->where('product_code','LIKE', '%' . $dataSearch['product_code'] . '%');
			}

	  		if (isset($dataSearch['product_status']) && $dataSearch['product_status'] != -1) {
	  			$query->where('product_status', $dataSearch['product_status']);
	  		}
	  		if (isset($dataSearch['product_sale']) && $dataSearch['product_sale'] != -1) {
	  			$query->where('product_sale', $dataSearch['product_sale']);
	  		}
			if (isset($dataSearch['product_khuyenmai']) && $dataSearch['product_khuyenmai'] != -1) {
				$query->where('product_khuyenmai', $dataSearch['product_khuyenmai']);
			}
			if (isset($dataSearch['product_giamgia']) && $dataSearch['product_giamgia'] != -1) {
				$query->where('product_giamgia', $dataSearch['product_giamgia']);
			}
			if (isset($dataSearch['product_moi']) && $dataSearch['product_moi'] != -1) {
				$query->where('product_moi', $dataSearch['product_moi']);
			}
	  		if(isset($dataSearch['product_catid']) && $dataSearch['product_catid'] != 0){
	  			$catid = $dataSearch['product_catid'];
	  			$arrCat = array($catid);
	  			Category::makeListCatId($catid, 0, $arrCat);
	  			if(is_array($arrCat) && !empty($arrCat)){
	  				$query->whereIn('product_catid', $arrCat);
	  			}
	  		}
	  		if (isset($dataSearch['product_focus']) && $dataSearch['product_focus'] != -1) {
	  			$query->where('product_focus', $dataSearch['product_focus']);
	  		}
	  		if (isset($dataSearch['product_supplier']) && $dataSearch['product_supplier'] != -1) {
	  			$query->where('product_supplier', $dataSearch['product_supplier']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('product_id', 'desc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_PRODUCT_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Product::where('product_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_PRODUCT_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Product::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->product_id) && $data->product_id > 0){
  					self::removeCacheId($data->product_id);
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
            $data = new Product();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->product_id && Memcache::CACHE_ON){
                	Product::removeCacheId($data->product_id);
                }
                return $data->product_id;
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
    		Product::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Product::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Product::find($id);
  			if($data != null){
  				//Remove Img
  				$product_image_other = ($data->product_image_other != '') ? unserialize($data->product_image_other) : array();
  				if(is_array($product_image_other) && !empty($product_image_other)){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_PRODUCT.'/'.$id;
  					foreach($product_image_other as $v){
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
  				if(isset($data->product_id) && $data->product_id > 0){
  					self::removeCacheId($data->product_id);
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
  			Cache::forget(Memcache::CACHE_PRODUCT_ID.$id);
  		}
  	}
	public static function getProductByCode($code){
		$result = array();
		if($code != ''){
			$result = Product::where('product_code', $code)->first();
		}
		return $result;
	}
  	//SITE
  	public static function getSameProduct($dataField='', $catid=0, $id=0, $limit=10){
  		$result = array();
  		try{
  			if($catid > 0 && $id > 0 && $limit > 0){
  				$query = Product::where('product_id','<>', $id);
  				$query->where('product_catid', $catid);
  				$query->where('product_status', CGlobal::status_show);
  				$query->orderBy('product_id', 'desc');
  					
  				$fields = (isset($dataField['field_get']) && trim($dataField['field_get']) != '') ? explode(',',trim($dataField['field_get'])): array();
  				if(!empty($fields)){
  					$result = $query->take($limit)->get($fields);
  				}else{
  					$result = $query->take($limit)->get();
  				}
  			}
  				
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		return $result;
  	}
  	public static function getProductHotRandom($dataField='', $id=0, $limit=10){
  		$result = array();
  		try{
  			if($limit > 0){
  				$query = Product::where('product_id','>', 0);
  				$query->where('product_status', CGlobal::status_show);
  				$query->where('product_focus', CGlobal::status_show);
  				if($id > 0){
  					$query->where('product_id', '<>', $id);
  				}
  				$query->orderBy(DB::raw('RAND()'));
  					
  				$fields = (isset($dataField['field_get']) && trim($dataField['field_get']) != '') ? explode(',',trim($dataField['field_get'])): array();
  				if(!empty($fields)){
  					$result = $query->take($limit)->get($fields);
  				}else{
  					$result = $query->take($limit)->get();
  				}
  			}
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		return $result;
  	}
  	public static function getOrderCart($dataSearch=array(), $limit=0, $offset=0, &$total){
  		try{
  		  
  			$query = Product::where('product_id','>',0);
  			
  			if(isset($dataSearch['product_id'])){
  				$arrId = $dataSearch['product_id'];
  				if(is_array($arrId) && !empty($arrId)){
  					$query->whereIn('product_id', $arrId);
  				}else{
  					$query->where('product_id','=', (int)$arrId);
  				}
  			}
  		  
  			$total = $query->count();
  			$query->orderBy('product_id', 'desc');
  	
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
  	public static function getProductInArrCat($dataSearch=array(), $limit=0){
  		try{
  		  
  			$query = Product::where('product_id','>',0);
  		  
  			if (isset($dataSearch['product_status']) && $dataSearch['product_status'] != -1) {
  				$query->where('product_status', $dataSearch['product_status']);
  			}
  		  
  			if(isset($dataSearch['product_catid']) && $dataSearch['product_catid'] != 0){
  				$catid = $dataSearch['product_catid'];
  				$arrCat = array($catid);
  				Category::makeListCatId($catid, 0, $arrCat);
  				if(is_array($arrCat) && !empty($arrCat)){
  					$query->whereIn('product_catid', $arrCat);
  				}
  			}
  		  
  			$query->orderBy('product_id', 'desc');
  	
  			$fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
  			if(!empty($fields)){
  				$result = $query->take($limit)->get($fields);
  			}else{
  				$result = $query->take($limit)->get();
  			}
  			return $result;
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
}