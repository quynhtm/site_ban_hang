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

class Supplier extends Model{
    
    protected $table = 'supplier';
    protected $primaryKey = 'supplier_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'supplier_id', 'supplier_title', 'supplier_mobile', 'supplier_email', 'supplier_address',
			'supplier_created', 'supplier_order_no', 'supplier_intro', 'supplier_status',
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Supplier::where('supplier_id','>',0);
	  		
	  		if (isset($dataSearch['supplier_title']) && $dataSearch['supplier_title'] != '') {
	  			$query->where('supplier_title','LIKE', '%' . $dataSearch['supplier_title'] . '%');
	  		}
	  		if (isset($dataSearch['supplier_status']) && $dataSearch['supplier_status'] != -1) {
	  			$query->where('supplier_status', $dataSearch['supplier_status']);
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('supplier_order_no', 'asc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_SUPPLIER_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Supplier::where('supplier_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_SUPPLIER_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Supplier::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->supplier_id) && $data->supplier_id > 0){
  					self::removeCacheId($data->supplier_id);
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
            $data = new Supplier();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->supplier_id && Memcache::CACHE_ON){
                	Supplier::removeCacheId($data->supplier_id);
                }
                return $data->supplier_id;
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
			Supplier::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
			Supplier::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Supplier::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->supplier_id) && $data->supplier_id > 0){
  					self::removeCacheId($data->supplier_id);
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
  			Cache::forget(Memcache::CACHE_SUPPLIER_ID.$id);
			Cache::forget(Memcache::CACHE_ALL_SUPPLIER);
  		}
  	}

	public static function getAllSupplier($data=array(), $limit=0){
		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ALL_SUPPLIER) : array();
		try{
			if(empty($result)){
				$query = Supplier::where('supplier_id', '>', 0);
				$query->where('supplier_status', CGlobal::status_show);

				$query->orderBy('supplier_order_no', 'asc');
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
					Cache::put(Memcache::CACHE_ALL_SUPPLIER, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
				}
			}

		}catch (PDOException $e){
			throw new PDOException();
		}

		return $result;
	}
	public static function arrSupplier($data=array()){
		$result = array(-1=>'Chọn nhà cung cấp');
		if(sizeof($data) != 0){
			foreach($data as $v) {
				$result[$v->supplier_id] = $v->supplier_title;
			}
		}
		return $result;
	}
	//SITE
}