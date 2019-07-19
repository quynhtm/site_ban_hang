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

class CommentOrder extends Model{
    
    protected $table = 'order_comment';
    protected $primaryKey = 'comment_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'comment_id', 'comment_username', 'comment_pid', 'comment_content','comment_created', 'uid',
    );
    public function getAllComment($dataSearch=array(), $limit=0, $sort='desc'){
    	$result = array();
    	try{ 
    		$query = CommentOrder::where('comment_id','>',0);
    		if (isset($dataSearch['comment_pid']) && $dataSearch['comment_pid'] > 0) {
    			$query->where('comment_pid','=',$dataSearch['comment_pid']);
    		}
    		if($sort=='desc'){
    			$query->orderBy('comment_id', 'desc');
    		}else{
    			$query->orderBy('comment_id', 'asc');
    		}
    		
    		if($limit > 0){
    			$query->take($limit);
    		}
    		$fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
    		if(!empty($fields)){
    			$result = $query->get($fields);
    		}else{
    			$result = $query->get();
    		}
    	
    	}catch (PDOException $e){
    		throw new PDOException();
    	}
    	return $result;
    }
  	public static function addData($dataInput=array()){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $data = new CommentOrder();
            if (is_array($dataInput) && count($dataInput) > 0) {
                foreach ($dataInput as $k => $v) {
                    $data->$k = $v;
                }
            }
            if ($data->save()) {
                DB::connection()->getPdo()->commit();
                return $data->comment_id;
            }
            DB::connection()->getPdo()->commit();
            return false;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = CommentOrder::find($id);
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
  	
  	public static function deleteCommentByOrderId($orderId){
  		try {
			DB::connection()->getPdo()->beginTransaction();
  			if($orderId > 0){
  				CommentOrder::where('comment_pid','=',$orderId)->delete();
  				DB::connection()->getPdo()->commit();
  			}
  			return true;
  		} catch (PDOException $e) {
  			DB::connection()->getPdo()->rollBack();
  			throw new PDOException();
  		}
  	}
}