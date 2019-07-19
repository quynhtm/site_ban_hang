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
use App\Library\PHPDev\Utility;

class EmailCustomer extends Model{
    
    protected $table = 'order_customer';
    protected $primaryKey = 'customer_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'customer_id', 'customer_email', 'customer_phone', 'customer_address', 'customer_full_name',
    		'customer_name_facebook','customer_link_facebook','customer_provice_id','customer_dictrict_id','customer_ward_id',
    );
    
    //ADMIN
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = EmailCustomer::where('customer_id','>',0);
	  		
	  		if (isset($dataSearch['customer_phone']) && $dataSearch['customer_phone'] != '') {
	  			$query->where('customer_phone','LIKE', '%' . $dataSearch['customer_phone'] . '%');
	  		}
	  		
	  		$total = $query->count();
	  		$query->orderBy('customer_id', 'desc');
	  	
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
  			if(empty($result)){
	  			$result = EmailCustomer::where('customer_id', $id)->first();
	  		}
	  	} catch (PDOException $e) {
	  		throw new PDOException();
	  	}
	  	return $result;
  	}
  	
  	public static function updateData($id=0, $dataInput=array()){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = EmailCustomer::find($id);
  			if($id > 0 && !empty($dataInput)){
  				$data->update($dataInput);
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
            $data = new EmailCustomer();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                return $data->customer_id;
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
    		EmailCustomer::updateData($id, $data_post);
    		Utility::messages('messages', 'Cập nhật thành công!');
    	}else{
    		EmailCustomer::addData($data_post);
    		Utility::messages('messages', 'Thêm mới thành công!');
    	}
 
    }
    
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = EmailCustomer::find($id);
  			if($data != null){
  				$data->delete();
  				DB::connection()->getPdo()->commit();
  			}
  			return true;
  		} catch (PDOException $e) {
  			DB::connection()->getPdo()->rollBack();
  			throw new PDOException();
  		}
  	}
	
  	//SITE
	public static function getCustomerByEmail($mail){
		$result = array();
		if($mail != ''){
        	$result = EmailCustomer::where('customer_email', $mail)->first();
		}
		return $result;
    }
    public static function getCustomerByPhone($phone){
    	$result = array();
    	if($phone != ''){
    		$result = EmailCustomer::where('customer_phone', $phone)->first();
    	}
    	return $result;
    }
}