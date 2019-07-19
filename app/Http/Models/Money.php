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

class Money extends Model{
    
    protected $table = 'money';
    protected $primaryKey = 'money_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'money_id', 'money_id_first', 'money_title', 'money_price','money_total_price', 'money_type', 'money_infor',
    		'money_created', 'money_updated', 'money_log'
    );
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Money::where('money_id','>',0);
	  		if (isset($dataSearch['money_id']) && $dataSearch['money_id'] != '') {
	  			$query->where('money_id','=', $dataSearch['money_id']);
	  		}
			if (isset($dataSearch['money_type']) && $dataSearch['money_type'] != -1) {
				$query->where('money_type','=', $dataSearch['money_type']);
			}
	  		if (isset($dataSearch['money_title']) && $dataSearch['money_title'] != '') {
	  			$query->where('money_title','LIKE', '%' . $dataSearch['money_title'] . '%');
	  		}
	  		$total = $query->count();
	  		$query->orderBy('money_id', 'desc');
	  	
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
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_MONEY_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Money::where('money_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_MONEY_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
  			$data = Money::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->money_id) && $data->money_id > 0){
  					self::removeCacheId($data->money_id);
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
            $data = new Money();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->money_id && Memcache::CACHE_ON){
                	Money::removeCacheId($data->money_id);
                }
                return $data->money_id;
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
    		Money::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
            Money::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Money::find($id);
  			if($data != null){
  				//End Remove Img
  				$data->delete();
  				if(isset($data->money_id) && $data->money_id > 0){
  					self::removeCacheId($data->money_id);
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
  			Cache::forget(Memcache::CACHE_MONEY_ID.$id);
  		}
  	}

    public static function getItemFirst($money_id){
        if($money_id > 0){
            $result = Money::where('money_id','>',0)->where('money_id','<>',$money_id)->orderBy('money_id', 'desc')->first();
        }else{
            $result = Money::where('money_id','>',0)->orderBy('money_id', 'desc')->first();
        }
        return $result;
    }
}