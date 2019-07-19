<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Models;

use App\Library\PHPDev\CGlobal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Library\PHPDev\Utility;

class UserPermission extends Model {

	protected $table = 'user_permission';
	protected $primaryKey = 'permission_id';
	public  $timestamps = false;

	protected $fillable = array('permission_id', 'permission_group', 'permission_title', 'permission_code', 'permission_status');

	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
		try{

			$query = UserPermission::where('permission_id','>',0);
			if (isset($dataSearch['permission_title']) && $dataSearch['permission_title'] != '') {
				$query->where('permission_title','LIKE', '%' . $dataSearch['permission_title'] . '%');
			}
			if (isset($dataSearch['permission_group']) && $dataSearch['permission_group'] != '') {
				$query->where('permission_group','LIKE', '%' . $dataSearch['permission_group'] . '%');
			}
			if (isset($dataSearch['permission_code']) && $dataSearch['permission_code'] != '') {
				$query->where('permission_code','LIKE', '%' . $dataSearch['permission_code'] . '%');
			}
			if (isset($dataSearch['permission_id']) && $dataSearch['permission_id'] != -1) {
				$query->where('permission_id', $dataSearch['permission_id']);
			}

			$total = $query->count();
			$query->orderBy('permission_id', 'asc');

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
		try {
			$result = UserPermission::where('permission_id', $id)->first();
		} catch (PDOException $e) {
			throw new PDOException();
		}
		return $result;
	}

	public static function updateData($id=0, $dataInput=array()){
		try {
			DB::connection()->getPdo()->beginTransaction();
			$data = UserPermission::find($id);
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
			$data = new UserPermission();
			if (is_array($dataInput) && count($dataInput) > 0) {
				foreach ($dataInput as $k => $v) {
					$data->$k = $v;
				}
			}
			if ($data->save()) {
				DB::connection()->getPdo()->commit();
				return $data->permission_id;
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
			UserPermission::updateData($id, $data_post);
			Utility::messages('messages', 'Cập nhật thành công!');
		}else{
			UserPermission::addData($data_post);
			Utility::messages('messages', 'Thêm mới thành công!');
		}

	}

	public static function deleteId($id=0){
		try {
			DB::connection()->getPdo()->beginTransaction();
			$data = UserPermission::find($id);
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

	public static function checkPermissionExists($permission_code, $id=0){
		$result = array();
		if($id == 0) {
			$result = UserPermission::where('permission_code', $permission_code)->first();

		}else{
			$result = UserPermission::where('permission_code', $permission_code)->where('permission_id', '!=', $id)->first();
		}
		return $result;
	}

    public static function getListPermission(){
        return UserPermission::where('permission_status', '=', CGlobal::status_show)->get();
    }
}
