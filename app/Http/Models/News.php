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

class News extends Model{
    
    protected $table = 'news';
    protected $primaryKey = 'news_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'news_id', 'news_catid', 'news_cat_name', 'news_cat_alias','news_title', 'news_title_alias', 'news_intro',
    		'news_content', 'news_image', 'news_image_other','news_created', 'news_hot','news_focus', 'news_order_no',
    		'news_view_num', 'news_status', 'meta_title', 'meta_keywords', 'meta_description',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = News::where('news_id','>',0);
	  		
	  		if (isset($dataSearch['news_id']) && $dataSearch['news_id'] != '') {
	  			$query->where('news_id','=', $dataSearch['news_id']);
	  		}
	  		
	  		if (isset($dataSearch['news_title']) && $dataSearch['news_title'] != '') {
	  			$query->where('news_title','LIKE', '%' . $dataSearch['news_title'] . '%');
	  		}
	  		
	  		if(isset($dataSearch['news_catid']) && $dataSearch['news_catid'] != -1){
	  			$catid = $dataSearch['news_catid'];
	  			$arrCat = array($catid);
	  			Category::makeListCatId($catid, 0, $arrCat);
	  			if(is_array($arrCat) && !empty($arrCat)){
	  				$query->whereIn('news_catid', $arrCat);
	  			}
	  		}
	  		
	  		if (isset($dataSearch['news_status']) && $dataSearch['news_status'] != -1){
	  			$query->where('news_status', $dataSearch['news_status']);
	  		}
	  		
	  		if (isset($dataSearch['news_hot']) && $dataSearch['news_hot'] != -1) {
	  			$query->where('news_hot', $dataSearch['news_hot']);
	  		}
	  		
	  		if (isset($dataSearch['news_focus']) && $dataSearch['news_focus'] != -1) {
	  			$query->where('news_focus', $dataSearch['news_focus']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('news_id', 'desc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_NEWS_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = News::where('news_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_NEWS_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = News::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->news_id) && $data->news_id > 0){
  					self::removeCacheId($data->news_id);
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
            $data = new News();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->news_id && Memcache::CACHE_ON){
                	News::removeCacheId($data->news_id);
                }
                return $data->news_id;
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
    		News::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		News::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = News::find($id);
  			if($data != null){
  				//Remove Img
  				$news_image_other = ($data->news_image_other != '') ? unserialize($data->news_image_other) : array();
  				if(is_array($news_image_other) && !empty($news_image_other)){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_NEWS.'/'.$id;
  					foreach($news_image_other as $v){
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
  				if(isset($data->news_id) && $data->news_id > 0){
  					self::removeCacheId($data->news_id);
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
  			Cache::forget(Memcache::CACHE_NEWS_ID.$id);
  		}
  	}
  	
  	//SITE
	public static function getHotNews($dataField='', $limit=10){
		$result = array();
		try{
			if($limit > 0){
				$query = News::where('news_id','>', 0);
                $query->where('news_hot', CGlobal::status_show);
                $query->where('news_status', CGlobal::status_show);
				$query->orderBy('news_id', 'desc');
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
  	public static function getSameNews($dataField='', $catid=0, $id=0, $limit=10){
  		$result = array();
  		try{
  			if($catid > 0 && $id > 0 && $limit > 0){
  				$query = News::where('news_id','<>', $id);
  				$query->where('news_catid', $catid);
  				$query->where('news_status', CGlobal::status_show);
  				$query->orderBy('news_id', 'desc');
  				 
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
}