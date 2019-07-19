<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use App\Library\PHPDev\Memcache;
use App\Library\PHPDev\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommentProduct extends Model{
    
    protected $table = 'product_comment';
    protected $primaryKey = 'comment_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'comment_id', 'comment_catid', 'comment_username', 'comment_phone', 'comment_mail', 'comment_pid', 'comment_content','comment_created', 'uid', 'comment_status'
    );
	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
		try{

			$query = CommentProduct::where('comment_id','>',0);

			if (isset($dataSearch['comment_id']) && $dataSearch['comment_id'] != '') {
				$query->where('comment_id','=', $dataSearch['comment_id']);
			}
            if(isset($dataSearch['comment_catid']) && $dataSearch['comment_catid'] == 0) {
                $query->where('comment_catid','=', $dataSearch['comment_catid']);
            }
            if (isset($dataSearch['comment_phone']) && $dataSearch['comment_phone'] != '') {
                $query->where('comment_phone','=', $dataSearch['comment_phone']);
            }

            if (isset($dataSearch['comment_mail']) && $dataSearch['comment_mail'] != '') {
                $query->where('comment_mail','=', $dataSearch['comment_mail']);
            }

            if (isset($dataSearch['comment_status']) && $dataSearch['comment_status'] != -1) {
                $query->where('comment_status','=', $dataSearch['comment_status']);
            }

			if (isset($dataSearch['comment_username']) && $dataSearch['comment_username'] != '') {
				$query->where('comment_username','LIKE', '%' . $dataSearch['comment_username'] . '%');
			}

			$total = $query->count();
			$query->orderBy('comment_id', 'desc');

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
        $result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_COMMENT_PRODUCT_ID.$id) : array();
        try {
            if(empty($result)){
                $result = CommentProduct::where('comment_id', $id)->first();
                if($result && Memcache::CACHE_ON){
                    Cache::put(Memcache::CACHE_COMMENT_PRODUCT_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
            $data = CommentProduct::find($id);
            if($id > 0 && !empty($dataInput)){
                $data->update($dataInput);
                if(isset($data->comment_id) && $data->comment_id > 0){
                    self::removeCacheId($data->comment_id);
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
            $data = new CommentProduct();
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
    public static function saveData($id=0, $data=array()){
        $data_post = array();
        if(!empty($data)){
            foreach($data as $key=>$val){
                $data_post[$key] = $val['value'];
            }
        }
        if($id > 0){
            CommentProduct::updateData($id, $data_post);
            Utility::messages('messages', 'Cập nhật thành công!');
        }else{
            CommentProduct::addData($data_post);
            Utility::messages('messages', 'Thêm mới thành công!');
        }

    }
  	public static function deleteId($id=0){
    try {
        DB::connection()->getPdo()->beginTransaction();
        $data = CommentProduct::find($id);
        if($data != null){
            $data->delete();
            if(isset($data->comment_id) && $data->comment_id > 0){
                self::removeCacheId($data->comment_id);
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
            Cache::forget(Memcache::CACHE_COMMENT_PRODUCT_ID.$id);
        }
    }
    public static function deleteByCatId($id=0){
        try {
            $datas = CommentProduct::where('comment_catid','=', $id)->get();
            if(sizeof($datas) > 0){
                foreach($datas as $data){
                    if(isset($data->comment_id) && $data->comment_id > 0){
                        CommentProduct::deleteId($data->comment_id);
                    }
                }
            }
            return true;
        } catch (PDOException $e) {
            throw new PDOException();
        }
    }
    public function getAllComment($dataSearch=array(), $limit=0, $sort='desc'){
        $result = array();
        try{
            $query = CommentProduct::where('comment_id','>',0);
            if (isset($dataSearch['comment_pid']) && $dataSearch['comment_pid'] > 0) {
                $query->where('comment_pid','=',$dataSearch['comment_pid']);
            }
            if (isset($dataSearch['comment_catid'])) {
                $query->where('comment_catid','=',$dataSearch['comment_catid']);
            }
            if (isset($dataSearch['comment_status']) && $dataSearch['comment_status'] == 1) {
                $query->where('comment_status','=',$dataSearch['comment_status']);
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
  	public static function deleteCommentByProductId($orderId){
  		try {
			DB::connection()->getPdo()->beginTransaction();
  			if($orderId > 0){
				CommentProduct::where('comment_pid','=',$orderId)->delete();
  				DB::connection()->getPdo()->commit();
  			}
  			return true;
  		} catch (PDOException $e) {
  			DB::connection()->getPdo()->rollBack();
  			throw new PDOException();
  		}
  	}
}