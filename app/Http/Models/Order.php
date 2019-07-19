<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use App\Library\PHPDev\CDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;

class Order extends Model{
    
    protected $table = 'order';
    protected $primaryKey = 'order_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'order_id', 'order_user_id_created', 'order_user_name_created', 'order_user_id_confirm', 'order_user_name_confirm',
            'order_title', 'order_list_code', 'order_code_post', 'order_price_post',
            'order_time_send', 'order_time_finish','order_email', 'order_phone', 'order_address','order_note', 'order_note_transport',
            'order_link_ship', 'order_created', 'order_num', 'order_total_lst','order_provice_id', 'order_dictrict_id', 'order_ward_id',
            'order_name_facebook','order_nick_facebook', 'order_link_comment_facebook', 'order_partner', 'order_ads_id', 'order_gift',
            'order_confirm_print', 'order_user_editing', 'order_time_editing','order_status', 'order_user_buy',
    );
    
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Order::where('order_id','>',0);
	  		
	  		if (isset($dataSearch['order_title']) && $dataSearch['order_title'] != '') {
	  			$query->where('order_phone', 'LIKE', '%'.$dataSearch['order_title'].'%');
	  			$query->orWhere('order_code_post', 'LIKE', '%'.$dataSearch['order_title'].'%');
	  		}

	  		if (isset($dataSearch['order_status']) && $dataSearch['order_status'] != -1) {
	  			$query->where('order_status', $dataSearch['order_status']);
	  		}
	  		if (isset($dataSearch['order_partner']) && $dataSearch['order_partner'] != -1) {
	  			$query->where('order_partner', $dataSearch['order_partner']);
	  		}
	  		if (isset($dataSearch['order_provice_id']) && $dataSearch['order_provice_id'] != -1) {
	  			$query->where('order_provice_id', $dataSearch['order_provice_id']);
	  		}
	  		
	  		if (isset($dataSearch['order_user_id_created']) && $dataSearch['order_user_id_created'] != -1) {
	  			$query->where('order_user_id_created', $dataSearch['order_user_id_created']);
	  		}
			if (isset($dataSearch['order_user_buy']) && $dataSearch['order_user_buy'] != 0) {
				$query->where('order_user_buy', $dataSearch['order_user_buy']);
			}


	  		if(isset($dataSearch['order_from']) && isset($dataSearch['order_to']) && $dataSearch['order_from'] !='' && $dataSearch['order_to'] !=''){
	  			$order_from = CDate::convertDate($dataSearch['order_from'].' 00:00:00');
	  			$order_to = CDate::convertDate($dataSearch['order_to']. ' 23:59:59');
	  			if($order_to >= $order_from && $order_to > 0){
	  				$query->whereBetween('order_time_send', array($order_from, $order_to));
	  			}
	  		}

			$query->orderBy('order_id', 'desc');

	  		$total = $query->count();

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
  		$result = array();
  		try {
  			if($id > 0){
	  			$result = Order::where('order_id', $id)->first();
	  		}
	  	} catch (PDOException $e) {
	  		throw new PDOException();
	  	}
	  	return $result;
  	}
  	
  	public static function updateData($id=0, $dataInput=array()){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Order::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
  				if(isset($data->order_id) && $data->order_id > 0){
  					self::removeCacheId($data->order_id);
                    Order::countOrderStatus($data->order_status);
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
            $data = new Order();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                if($data->order_id && Memcache::CACHE_ON){
                	Order::removeCacheId($data->order_id);
                    Order::countOrderStatus($data->order_status);
                }
                return $data->order_id;
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
    		Order::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		Order::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Order::find($id);
  			if($data != null){
  				$data->delete();
  				if(isset($data->order_id) && $data->order_id > 0){
  					self::removeCacheId($data->order_id);
                    Order::countOrderStatus($data->order_status);
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
  			Cache::forget(Memcache::CACHE_ORDER_ID.$id);
  		}
  	}
  	
  	public static function getByCondition($dataSearch=array(), $timeFrom='', $timeTo='', $limit=0){
  		try{
  			$query = Order::where('order_id','>',0);
  			$query->whereBetween('order_time_send', [$timeFrom, $timeTo]);
  			
  			//Upload Order Code = Null Or '' To Partner
  			if (isset($dataSearch['order_partner']) && $dataSearch['order_partner'] != -1) {
  				$query->where('order_partner', $dataSearch['order_partner']);
  			}
  			if (isset($dataSearch['order_code_post']) && $dataSearch['order_code_post'] == '') {
  				$query->whereNull('order_code_post');
  				$query->orWhere('order_code_post', '', '');
  			}
  			
  			//Export With Status
  			if (isset($dataSearch['order_status']) && $dataSearch['order_status'] != -1) {
  				$query->where('order_status', '=', $dataSearch['order_status']);
  			}
  			
  			$query->orderBy('order_id', 'desc');
  			if($limit > 0){
  				$query->take($limit);
  			}
  			$result = $query->get();
  			return $result;
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
  	
  	public static function searchByConditionPhone($phone=''){
  		try{
  		  
  			$query = Order::where('order_id','>', 0);
  			if ($phone != '') {
  				$query->where('order_phone','=', $phone);
  			}
  			$query->orderBy('order_id', 'desc');
  			return $query->count();
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
  	public static function searchByConditionCodePost($order_code_post='', $dataSearch){
  		try{
  			 
  			$query = Order::where('order_id','>', 0);
  			if ($order_code_post != '') {
  				$query->where('order_code_post', '=', $order_code_post);
  			}
  			if(isset($dataSearch) && is_array($dataSearch) && !empty($dataSearch)){
  				$query->whereNotIn('order_id', $dataSearch);
  			}
  			$query->where('order_status','=', CGlobal::da_duyet);
  			$query->orderBy('order_id', 'desc');
  			$result = $query->get();
  			return $result;
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
  	public static function searchByConditionCodePostDaNhanHangHoan($order_code_post='', $dataSearch){
  		try{
  	
  			$query = Order::where('order_id','>', 0);
  			if ($order_code_post != '') {
  				$query->where('order_code_post','=', $order_code_post);
  			}
  			if(isset($dataSearch) && is_array($dataSearch) && !empty($dataSearch)){
  				$query->whereNotIn('order_id', $dataSearch);
  			}
  			$query->where('order_status','<>', CGlobal::da_nhan_hang_hoan);
  			$query->orderBy('order_id', 'desc');
  			$result = $query->get();
  			return $result;
  			 
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
  	
  	public static function getAllProductStatusDaDuyet($id=0, $store=-1){
  		$result = array();
  		try{
  			if($id > 0 && $store != -1){
	  			$query = Order::where('order_id','<>', $id);
	  			$query->where('order_status','=', CGlobal::da_duyet);
	  			$query->where('order_store','=', $store);
	  			$query->orderBy('order_id', 'desc');
	  			$result = $query->get();
  			}
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  		return $result;
  	}
  	
  	//Dashboard
  	public static function searchByConditionDashBoard($dataSearch=array()){
  		try{
  			$query = Order::where('order_id','>',0);

			if(isset($dataSearch['order_status'])) {
				if(is_array($dataSearch['order_status']) && !empty($dataSearch['order_status'])){
					$query->whereIn('order_status', $dataSearch['order_status']);
				}else{
					if($dataSearch['order_status'] != -1){
						$query->where('order_status', $dataSearch['order_status']);
					}
				}
			}

  			if(isset($dataSearch['order_from']) && isset($dataSearch['order_to']) && $dataSearch['order_from'] !='' && $dataSearch['order_to'] !=''){
  				$order_from = CDate::convertDate($dataSearch['order_from'].' 00:00:00');
  				$order_to = CDate::convertDate($dataSearch['order_to']. ' 23:59:59');
  				if($order_to >= $order_from && $order_to > 0){
                    if(isset($dataSearch['order_lendon']) && $dataSearch['order_lendon'] == 1){
  						$query->whereBetween('order_created', array($order_from, $order_to));
  					}elseif(isset($dataSearch['order_lendon']) && $dataSearch['order_lendon'] == 2){
						$query->whereBetween('order_time_finish', array($order_from, $order_to));
					}else{
  						$query->whereBetween('order_time_send', array($order_from, $order_to));
  					}
  				}
  			}

			if(isset($dataSearch['order_user_id_created']) && $dataSearch['order_user_id_created'] != -1){
				$query->where('order_user_id_created', $dataSearch['order_user_id_created']);
			}

            if(isset($dataSearch['order_sort']) && $dataSearch['order_sort'] == 'asc'){
                $query->orderBy('order_id', 'asc');
            }else{
                $query->orderBy('order_id', 'desc');
            }

  			$fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
  			if(!empty($fields)){
  				$result = $query->get($fields);
  			}else{
  				$result = $query->get();
  			}
  			return $result;
  	
  		}catch (PDOException $e){
  			throw new PDOException();
  		}
  	}
  	
  	public static function countOrderStatus($status=-1, $uid=0){
  		$total = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_ORDER_COUNT_STATUS.$status) : 0;
  		try {
  			if($status != -1){
  				$query = Order::where('order_id','>',0);
  				$query->where('order_status', $status);
				if($uid > 0){
					$query->where('order_user_id_created', $uid);
				}
  				$total = $query->count();
  				if($total && Memcache::CACHE_ON){
  					Cache::put(Memcache::CACHE_ORDER_COUNT_STATUS.$status, $total, Memcache::CACHE_TIME_TO_LIVE_15);
  				}
  			}
  		} catch (PDOException $e) {
  			throw new PDOException();
  		}
  		return $total;
  	}
}