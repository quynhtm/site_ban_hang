<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\News;
use App\Http\Models\Type;
use App\Library\PHPDev\ThumbImg;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Category;
use App\Http\Models\Trash;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

class NewsController extends BaseAdminController{

	private $permission_view = 'news_view';
	private $permission_create = 'news_create';
	private $permission_edit = 'news_edit';
	private $permission_delete = 'news_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $arrHot = array(-1 => 'Chọn hot', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrFocus = array(-1 => 'Chọn nổi bật', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrCate = array(-1=>'Chọn danh mục cha');
	private $strCategoryProduct = '';
	private $error = '';
	public function __construct(){
		parent::__construct();
        Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/upload/cssUpload.css', CGlobal::$postHead);
        Loader::loadJS('libs/upload/jquery.uploadfile.js', CGlobal::$postEnd);
        Loader::loadJS('backend/js/upload-admin.js', CGlobal::$postEnd);
        Loader::loadJS('libs/dragsort/jquery.dragsort.js', CGlobal::$postHead);
        Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
        Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);
		
		$typeId = Type::getIdByKeyword('group_news');
		$this->arrCate = CategoryController::getArrCategory($typeId);
	}
	public function listView(){

        if(!in_array($this->permission_view, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		//Config Page
		$pageNo = (int) Request::get('page', 1);
		$pageScroll = CGlobal::num_scroll_page;
		$limit = CGlobal::num_record_per_page;
		$offset = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['news_title'] = addslashes(Request::get('news_title', ''));
		$search['news_status'] = (int)Request::get('news_status', -1);
		$search['news_catid'] = (int)Request::get('news_catid', -1);
		$search['news_hot'] = (int)Request::get('news_hot', -1);
		$search['news_focus'] = (int)Request::get('news_focus', -1);
		$search['field_get'] = '';
		
		$dataSearch = News::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['news_status']);
		$optionHot = Utility::getOption($this->arrHot, $search['news_hot']);
		$optionFocus = Utility::getOption($this->arrFocus, $search['news_focus']);
		$messages = Utility::messages('messages');
		
		$typeId = Type::getIdByKeyword('group_news');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($search['news_catid']) ? $search['news_catid'] : 0);

        return view('admin.news.list',[
                    'data'=>$dataSearch,
                    'total'=>$total,
                    'paging'=>$paging,
                    'arrStatus'=>$this->arrStatus,
                    'optionStatus'=>$optionStatus,
                    'arrFocus'=>$this->arrFocus,
                    'optionFocus'=>$optionFocus,
                    'arrHot'=>$this->arrHot,
                    'optionHot'=>$optionHot,
                    'arrCate'=>$this->arrCate,
                    'strCategoryProduct'=>$this->strCategoryProduct,
                    'search'=>$search,
                    'messages'=>$messages,
                ]);
	}
	public function getItem($id=0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$data = array();
		$news_image = '';
		$news_image_other = array();
		
		if($id > 0) {
			$data = News::getById($id);
			if($data != null){
				if($data->news_image_other != ''){
                    $newsImageOther = unserialize($data->news_image_other);
                    if(!empty($newsImageOther)){
                        foreach($newsImageOther as $k=>$v){
                            $url_thumb = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $id, $v, 400, 400, '', true, true);
                            $news_image_other[] = array('img_other'=>$v,'src_img_other'=>$url_thumb);
                        }
                    }
                }
                //Main Img
               $news_image = trim($data->news_image);
			}
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['news_status'])? $data['news_status'] : CGlobal::status_show);
		$optionHot = Utility::getOption($this->arrHot, isset($data['news_hot'])? $data['news_hot'] : -1);
		$optionFocus = Utility::getOption($this->arrFocus, isset($data['news_focus'])? $data['news_focus'] : 0);
		
		$typeId = Type::getIdByKeyword('group_news');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($data['news_catid'])? $data['news_catid'] : 0);

        return view('admin.news.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'news_image'=>$news_image,
                    'news_image_other'=>$news_image_other,
                    'optionStatus'=>$optionStatus,
                    'optionFocus'=>$optionFocus,
                    'optionHot'=>$optionHot,
                    'optionCategoryProduct'=>$this->strCategoryProduct,
                    'error'=>$this->error,
                ]);


	}
	public function postItem($id=0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);

		$id_hiden = (int)Request::get('id_hiden', 0);
		$data = array();
		
		$dataSave = array(
				'news_title'=>array('value'=>addslashes(Request::get('news_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'news_intro'=>array('value'=>addslashes(Request::get('news_intro')),'require'=>0),
				'news_content'=>array('value'=>trim(Request::get('news_content')),'require'=>0),
				'news_order_no'=>array('value'=>(int)Request::get('news_order_no', 0),'require'=>0),
				'news_catid'=>array('value'=>(int)(Request::get('news_catid')),'require'=>0),
				'news_hot'=>array('value'=>(int)(Request::get('news_hot')),'require'=>0),
				'news_focus'=>array('value'=>(int)(Request::get('news_focus')),'require'=>0),
				'news_created'=>array('value'=>time(),'require'=>0),
				'news_status'=>array('value'=>(int)(Request::get('news_status')),'require'=>0),
				'news_image'=>array('value'=>trim(Request::get('image_primary')),'require'=>''),
				
				'meta_title'=>array('value'=>trim(Request::get('meta_title')),'require'=>0),
				'meta_keywords'=>array('value'=>trim(Request::get('meta_keywords')),'require'=>0),
				'meta_description'=>array('value'=>trim(Request::get('meta_description')),'require'=>0),
				
		);
		
		//get news_cat_name, news_cat_alias
		if(isset($dataSave['news_catid']['value']) && $dataSave['news_catid']['value'] > 0){
			$arrCat = Category::getById($dataSave['news_catid']['value']);
			if($arrCat != null){
				$dataSave['news_cat_name']['value'] = $arrCat->category_title;
				$dataSave['news_cat_alias']['value'] = $arrCat->category_title_alias;
			}
		}
		
		//Main Img
		$image_primary = addslashes(Request::get('image_primary', ''));
		//Other Img
		$arrInputImgOther = array();
		$getImgOther = Request::get('img_other',array());
		if(!empty($getImgOther)){
			foreach($getImgOther as $k=>$val){
				if($val !=''){
					$arrInputImgOther[] = $val;
				}
			}
		}
		if (!empty($arrInputImgOther) && count($arrInputImgOther) > 0) {
			//Neu Ko chon Anh Chinh, Lay Anh Chinh La Cai Dau Tien
			$dataSave['news_image']['value'] = ($image_primary != '') ? $image_primary : $arrInputImgOther[0];
			$dataSave['news_image_other']['value'] = serialize($arrInputImgOther);
		}
		
		if($id > 0){
			unset($dataSave['news_created']);
		}
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;

			News::saveData($id, $dataSave);
			return Redirect::route('admin.news');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['news_status'])? $data['news_status'] : -1);
		$optionHot = Utility::getOption($this->arrHot, isset($data['news_hot'])? $data['news_hot'] : 0);
		$optionFocus = Utility::getOption($this->arrFocus, isset($data['news_focus'])? $data['news_focus'] : 0);
		
		$typeId = Type::getIdByKeyword('group_news');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($data['news_catid'])? $data['news_catid'] : 0);

        return view('admin.news.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'news_image'=>$image_primary,
                    'news_image_other'=>$arrInputImgOther,
                    'optionStatus'=>$optionStatus,
                    'optionFocus'=>$optionFocus,
                    'optionHot'=>$optionHot,
                    'optionCategoryProduct'=>$this->strCategoryProduct,
                    'error'=>$this->error,
                ]);
	}
	public function delete(){

        if(!in_array($this->permission_delete, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

		$listId = Request::get('checkItem', array());
		$token = Request::get('_token', '');
		if(Session::token() === $token){
			if(!empty($listId) && is_array($listId)){
				foreach($listId as $id){
					Trash::addItem($id, 'News', CGlobal::FOLDER_NEWS, 'news_id', 'news_title', 'news_image', 'news_image_other');
					News::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.news');
	}
}