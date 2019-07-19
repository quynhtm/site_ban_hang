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


class UserRolePermission extends Model{
    
    protected $table = 'user_role_permission';
    protected $primaryKey = 'role_permission_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'role_id', 'permission_id',
    );

	//Get list permission by rold id
	public static function getListPermissionByRoleId($aryRoleId){
		$table_permission = with(new UserPermission())->getTable();
		$table_user_role_permission = with(new UserRolePermission())->getTable();
		$query = DB::table($table_user_role_permission);
		$query->join($table_permission, function ($join) use ($table_permission, $table_user_role_permission) {
			$join->on($table_user_role_permission . '.permission_id', '=', $table_permission . '.permission_id');
		});
		$query->where($table_permission . '.permission_status', '=', 1);
		$query->whereIn($table_user_role_permission . '.role_id', $aryRoleId);
		$query->select($table_user_role_permission . '.role_id', $table_permission . '.*');
		return $query->get();
	}
    //Get list role by permission id
    public static function getListRoleByPermissionId($aryPermissionId){
        $table_role = with(new UserRole())->getTable();
        $table_user_role_permission = with(new UserRolePermission())->getTable();
        $query = DB::table($table_user_role_permission);
        $query->join($table_role, function ($join) use ($table_role, $table_user_role_permission) {
            $join->on($table_user_role_permission . '.role_id', '=', $table_role . '.role_id');
        });
        $query->where($table_role . '.role_status', '=', 1);
        $query->whereIn($table_user_role_permission . '.permission_id', $aryPermissionId);
        $query->select($table_user_role_permission . '.permission_id', $table_role . '.*');
        return $query->get();
    }
}