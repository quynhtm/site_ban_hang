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
class NickSupport extends Model{
    
    protected $table = 'nicksupport';
    protected $primaryKey = 'id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'id', 'title', 'yahoo', 'skyper','phone', 'mobile', 'email', 'created', 'order_no', 'status',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = NickSupport::where('id','>',0);
	  		
	  		if (isset($dataSearch['title']) && $dataSearch['title'] != '') {
	  			$query->where('title','LIKE', '%' . $dataSearch['title'] . '%');
	  		}
	  		if (isset($dataSearch['status']) && $dataSearch['status'] != -1) {
	  			$query->where('status', $dataSearch['status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('order_no', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_NICK_SUPPORT_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = NickSupport::where('id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_NICK_SUPPORT_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = NickSupport::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->id) && $data->id > 0){
  					self::removeCacheId($data->id);
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
            $data = new NickSupport();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->id && Memcache::CACHE_ON){
                	NickSupport::removeCacheId($data->id);
                }
                return $data->id;
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
    		NickSupport::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		NickSupport::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = NickSupport::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->id) && $data->id > 0){
  					self::removeCacheId($data->id);
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
  			Cache::forget(Memcache::CACHE_NICK_SUPPORT_ID.$id);
  		}
  	}
  	
  	//SITE
  
}