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
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Memcache;


class Trash extends Model{
    
    protected $table = 'trash';
    protected $primaryKey = 'trash_id';
    public  $timestamps = false;
    protected $fillable = array(
	    	'trash_id', 'trash_obj_id', 'trash_title', 'trash_class', 'trash_content', 'trash_image', 'trash_image_other', 'trash_folder', 'trash_created',
    );
    
  	public static function searchByCondition($dataSearch=array(), $limit=0, $offset=0, &$total){
	  	try{
	  		
	  		$query = Trash::where('trash_id','>',0);
	  		
	  		if (isset($dataSearch['trash_title']) && $dataSearch['trash_title'] != '') {
	  			$query->where('trash_title','LIKE', '%' . $dataSearch['trash_title'] . '%');
	  		}
	  	
	  		$total = $query->count();
	  		$query->orderBy('trash_id', 'desc');
	  	
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
  	public static function addData($dataInput=array()){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = new Trash();
  			if (is_array($dataInput) && count($dataInput) > 0) {
  				foreach ($dataInput as $k => $v) {
  					$data->$k = $v;
  				}
  			}
  			if ($data->save()) {
  				DB::connection()->getPdo()->commit();
  				return $data->trash_id;
  			}
  			DB::connection()->getPdo()->commit();
  			return false;
  		} catch (PDOException $e) {
  			DB::connection()->getPdo()->rollBack();
  			throw new PDOException();
  		}
  	}
  	public static function getById($id=0){
  		$result = (Memcache::CACHE_ON) ? Cache::get(Memcache::CACHE_TRASH_ID.$id) : array();
  		try {
  			if(empty($result)){
	  			$result = Trash::where('trash_id', $id)->first();
	  			if($result && Memcache::CACHE_ON){
	  				Cache::put(Memcache::CACHE_TRASH_ID.$id, $result, Memcache::CACHE_TIME_TO_LIVE_ONE_MONTH);
	  			}
	  		}
	  	} catch (PDOException $e) {
	  		throw new PDOException();
	  	}
	  	return $result;
  	}
  	public static function deleteId($id=0){
  		try {
  			DB::connection()->getPdo()->beginTransaction();
  			$data = Trash::find($id);
  			if($data != null){
  				//Remove Img
  				$trash_image_other = ($data->trash_image_other != '') ? unserialize($data->trash_image_other) : array();
  				
  				if(is_array($trash_image_other) && !empty($trash_image_other)){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_TRASH.'/'.$data->trash_folder.'/'.$data->trash_obj_id;
  					
  					foreach($trash_image_other as $v){
  						if(is_file($path.'/'.$v)){
  							@unlink($path.'/'.$v);
  						}
  					}
  					if(is_dir($path)) {
  						@rmdir($path);
  					}
  				}
  				if($data->trash_image != ''){
  					$path = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_TRASH.'/'.$data->trash_folder.'/'.$data->trash_obj_id;
  					if(is_file($path.'/'.$data->trash_image)){
  						@unlink($path.'/'.$data->trash_image);
  					}
  					if(is_dir($path)) {
  						@rmdir($path);
  					}
  				}
  				//End Remove Img
  				$data->delete();
  				if(isset($data->trash_id) && $data->trash_id > 0){
  					self::removeCacheId($data->trash_id);
  				}
  				DB::connection()->getPdo()->commit();
  			}
  			return true;
  		} catch (PDOException $e) {
  			DB::connection()->getPdo()->rollBack();
  			throw new PDOException();
  		}
  	}
  	public static function addItem($id=0, $class='', $folder='', $field_id, $field_title='', $field_image='', $field_image_other=''){
        if($id > 0){
            $_class =  "App\Http\Models\\".$class;
  			if(class_exists($_class)){
  				$result = $_class::where($field_id, $id)->first();
  				$imgMain = '';
  				$imgOther = array();
  				$ObjClass = new $_class();
  				$arrField = $ObjClass->getFillable();

  				if($result != null){
  					if($folder != ''){
  						$folder_trash = Config::get('config.DIR_ROOT').'uploads/'.CGlobal::FOLDER_TRASH.'/'.$folder.'/'.$id;
  						if(!is_dir($folder_trash)){
  							@mkdir($folder_trash,0777,true);
  							chmod($folder_trash,0777);
  						}
  	
  						if($field_image_other != ''){
  							$strImgOther = $result->$field_image_other;
  							if($strImgOther != ''){
  								$arrImgOther = unserialize($strImgOther);
  								if(is_array($arrImgOther) && !empty($arrImgOther)){
  									foreach($arrImgOther as $img){
  										$file_current = Config::get('config.DIR_ROOT').'uploads/'.$folder.'/'.$id.'/'.$img;
  										$file_trash = $folder_trash.'/'.$img;
  										if(is_file($file_current)){
  											copy($file_current, $file_trash);
  											$imgOther[] = $img;
  										}
  									}
  								}
  							}
  							if($field_image != ''){
  								$imgMain = $result->$field_image;
  							}
  	
  						}elseif($field_image != ''){
  							$file_current = Config::get('config.DIR_ROOT').'uploads/'.$folder.'/'.$id.'/'.$result->$field_image;
  							$file_trash = $folder_trash.'/'.$result->$field_image;
  							if(is_file($file_current)){
  								copy($file_current, $file_trash);
  								$imgMain = $result->$field_image;
  							}
  						}
  					}
  	
  					$title = '';
  					if($field_title != ''){
  						if(isset($result->$field_title)){
  							$title = $result->$field_title;
  						}
  					}
  					
  					$data = array();
  					foreach($arrField as $field){
  						$data[$field] = $result->$field;
  					}
  					
  					$arrContent = $data;
  					$data = array(
  							'trash_obj_id'=>$id,
  							'trash_title'=>$title,
  							'trash_class' => $class,
  							'trash_content'=>serialize($arrContent),
  							'trash_image'=>$imgMain,
  							'trash_image_other'=>serialize($imgOther),
  							'trash_folder'=>$folder,
  							'trash_created'=>time(),
  					);
  
  					Trash::addData($data);
  				}
  			}
  		}
  		return true;
  	}
  	public static function restoreItem($id=0){
        $token = Request::get('_token', '');
        if(Session::token() === $token) {
            if ($id > 0) {
                $data = Trash::getById($id);
                if ($data != null) {
                    $class = $data->trash_class;
                    $trash_image = $data->trash_image;
                    $trash_image_other = $data->trash_image_other;
                    $dataRetore = unserialize($data->trash_content);

                    $arrImgOther = array();
                    if ($trash_image_other != '') {
                        $arrImgOther = unserialize($trash_image_other);
                    }

                    $folder_current = Config::get('config.DIR_ROOT') . 'uploads/' . $data->trash_folder . '/' . $data->trash_obj_id;

                    if ($trash_image != '' || $trash_image_other != '') {
                        if (!is_dir($folder_current)) {
                            @mkdir($folder_current, 0777, true);
                            chmod($folder_current, 0777);
                        }
                    }

                    if (is_array($arrImgOther) && !empty($arrImgOther)) {
                        foreach ($arrImgOther as $img) {
                            $file_recyclebin = Config::get('config.DIR_ROOT') . 'uploads/' . CGlobal::FOLDER_TRASH . '/' . $data->trash_folder . '/' . $data->trash_obj_id . '/' . $img;
                            $file_current = $folder_current . '/' . $img;
                            if (is_file($file_recyclebin)) {
                                copy($file_recyclebin, $file_current);
                                unlink($file_recyclebin);
                            }
                        }
                    }
                    if ($trash_image != '') {
                        $file_recyclebin = Config::get('config.DIR_ROOT') . 'uploads/' . CGlobal::FOLDER_TRASH . '/' . $data->trash_folder . '/' . $data->trash_obj_id . '/' . $trash_image;
                        $file_current = $folder_current . '/' . $trash_image;
                        if (is_file($file_recyclebin)) {
                            copy($file_recyclebin, $file_current);
                            unlink($file_recyclebin);
                        }
                    }

                    if (!empty($dataRetore)) {
                        $_class = "App\Http\Models\\" . $class;
                        $_class::addData($dataRetore);
                    }
                }

            }
        }
  		return true;
  	}
  	public static function removeCacheId($id=0){
  		if($id>0){
  			Cache::forget(Memcache::CACHE_TRASH_ID.$id);
  		}
  	}
}