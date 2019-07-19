<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\Product;
use App\Http\Models\Supplier;
use App\Http\Models\Type;
use App\Library\PHPDev\FuncLib;
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

class ProductController extends BaseAdminController{

	private $permission_view = 'product_view';
	private $permission_create = 'product_create';
	private $permission_edit = 'product_edit';
	private $permission_delete = 'product_delete';

	private $arrStatus = array(-1 => 'Chọn trạng thái', CGlobal::status_hide => 'Ẩn', CGlobal::status_show => 'Hiện');
	private $arrFocus = array(-1 => 'Chọn nổi bật', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrWholesale = array(-1 => 'Chọn bán sỉ', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrKhuyenMai = array(-1 => 'Chọn khuyến mãi', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrGiamGia = array(-1 => 'Chọn giảm giá', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrMoi = array(-1 => 'Chọn mới', CGlobal::status_hide => 'Không', CGlobal::status_show => 'Có');
	private $arrSale = array(-1 => 'Cập nhật tình trạng sản phẩm', CGlobal::product_sale_off => 'Hết hàng', CGlobal::product_sale_on => 'Còn hàng');
	
	private $arrCate = array(-1=>'Chọn danh mục cha');
	private $arrSupplier = array();
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
        Loader::loadJS('libs/number/autoNumeric.js', CGlobal::$postHead);

		$typeId = Type::getIdByKeyword('group_product');
		$this->arrCate = CategoryController::getArrCategory($typeId);

		$listSupplier = Supplier::getAllSupplier(array(), 0);
		$this->arrSupplier = Supplier::arrSupplier($listSupplier);
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
		$offset = $stt = ($pageNo - 1) * $limit;
		$search = $data = array();
		$total = 0;
		
		$search['product_id'] = addslashes(Request::get('product_id', 0));
		$search['product_title'] = addslashes(Request::get('product_title', ''));
		$search['product_status'] = (int)Request::get('product_status', -1);
		$search['product_catid'] = (int)Request::get('product_catid', 0);
		$search['product_focus'] = (int)Request::get('product_focus', -1);
        $search['product_supplier'] = (int)Request::get('product_supplier', -1);
        $search['product_sale'] = (int)Request::get('product_sale', -1);
        $search['product_khuyenmai'] = (int)Request::get('product_khuyenmai', -1);
        $search['product_giamgia'] = (int)Request::get('product_giamgia', -1);
        $search['product_moi'] = (int)Request::get('product_moi', -1);

		$search['field_get'] = '';
		
		$dataSearch = Product::searchByCondition($search, $limit, $offset, $total);
		$paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
		
		$optionStatus = Utility::getOption($this->arrStatus, $search['product_status']);
		$optionFocus = Utility::getOption($this->arrFocus, $search['product_focus']);
		$optionSupplier = Utility::getOption($this->arrSupplier, $search['product_supplier']);
		$optionSale = Utility::getOption($this->arrSale, $search['product_sale']);
		$optionKhuyenMai = Utility::getOption($this->arrKhuyenMai, $search['product_khuyenmai']);
		$optionGiamGia = Utility::getOption($this->arrGiamGia, $search['product_giamgia']);
		$optionMoi = Utility::getOption($this->arrMoi, $search['product_moi']);

        $messages = Utility::messages('messages');
		
		$typeId = Type::getIdByKeyword('group_product');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($search['product_catid']) ? $search['product_catid'] : 0);

        return view('admin.product.list',[
                    'data'=>$dataSearch,
                    'total'=>$total,
                    'stt'=>$stt,
                    'paging'=>$paging,
                    'arrStatus'=>$this->arrStatus,
                    'optionStatus'=>$optionStatus,
                    'arrFocus'=>$this->arrFocus,
                    'optionFocus'=>$optionFocus,
                    'arrCate'=>$this->arrCate,
                    'strCategoryProduct'=>$this->strCategoryProduct,
                    'optionSupplier'=>$optionSupplier,
                    'arrSupplier'=>$this->arrSupplier,
                    'optionSale'=>$optionSale,
                    'arrSale'=>$this->arrSale,
					'optionKhuyenMai'=>$optionKhuyenMai,
					'arrKhuyenMai'=>$this->arrKhuyenMai,
					'optionGiamGia'=>$optionGiamGia,
					'arrGiamGia'=>$this->arrGiamGia,
                    'arrMoi'=>$this->arrMoi,
                    'optionMoi'=>$optionMoi,
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
		$product_image = '';
		$product_image_other = array();
		
		if($id > 0) {
			$data = Product::getById($id);
			
			if($data != null){
				if($data->product_image_other != ''){
                    $productImageOther = unserialize($data->product_image_other);
                    if(!empty($productImageOther)){
                        foreach($productImageOther as $k=>$v){
                            $url_thumb = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $id, $v, 400, 400, '', true, true);
                            $product_image_other[] = array('img_other'=>$v,'src_img_other'=>$url_thumb);
                        }
                    }
                }
                //Main Img
               $product_image = trim($data->product_image);
			}
		}
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['product_status'])? $data['product_status'] : CGlobal::status_show);
		$optionFocus = Utility::getOption($this->arrFocus, isset($data['product_focus'])? $data['product_focus'] : 0);
		$optionWholesale = Utility::getOption($this->arrWholesale, isset($data['product_wholesale'])? $data['product_wholesale'] : 0);
		$optionSale = Utility::getOption($this->arrSale, isset($data['product_sale'])? $data['product_sale'] : 1);
		$optionKhuyenMai = Utility::getOption($this->arrKhuyenMai, isset($data['product_khuyenmai'])? $data['product_khuyenmai'] : -1);
		$optionGiamGia = Utility::getOption($this->arrGiamGia, isset($data['product_giamgia'])? $data['product_giamgia'] : -1);
		$optionMoi = Utility::getOption($this->arrMoi, isset($data['product_moi'])? $data['product_moi'] : -1);

		$typeId = Type::getIdByKeyword('group_product');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($data['product_catid'])? $data['product_catid'] : 0);

		$optionSupplier = Utility::getOption($this->arrSupplier, isset($data['product_supplier'])? $data['product_supplier'] : 1);
        return view('admin.product.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'product_image'=>$product_image,
                    'product_image_other'=>$product_image_other,
                    'optionStatus'=>$optionStatus,
                    'optionFocus'=>$optionFocus,
                    'optionWholesale'=>$optionWholesale,
                    'optionSupplier'=>$optionSupplier,
                    'optionCategoryProduct'=>$this->strCategoryProduct,
                    'optionSale'=>$optionSale,
                    'optionKhuyenMai'=>$optionKhuyenMai,
                    'optionGiamGia'=>$optionGiamGia,
                    'optionMoi'=>$optionMoi,
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
				'product_title'=>array('value'=>addslashes(Request::get('product_title')), 'require'=>1, 'messages'=>'Tiêu đề không được trống!'),
				'product_code'=>array('value'=>addslashes(Request::get('product_code')), 'require'=>1, 'messages'=>'Mã không được trống!'),
				'product_code_factory'=>array('value'=>addslashes(Request::get('product_code_factory')), 'require'=>0),

				'product_intro'=>array('value'=>addslashes(Request::get('product_intro')),'require'=>0),
				'product_content'=>array('value'=>addslashes(Request::get('product_content')),'require'=>0),
				'product_order_no'=>array('value'=>(int)Request::get('product_order_no', 0),'require'=>0),
				'product_catid'=>array('value'=>(int)(Request::get('product_catid')),'require'=>0),
				'product_focus'=>array('value'=>(int)(Request::get('product_focus')),'require'=>0),
				'product_created'=>array('value'=>time(),'require'=>0),
				'product_status'=>array('value'=>(int)(Request::get('product_status')),'require'=>0),
				'product_image'=>array('value'=>trim(Request::get('product_image')),'require'=>''),
				
				'product_price_input'=>array('value'=>(Request::get('product_price_input', 0)),'require'=>0),
				'product_price_normal'=>array('value'=>(Request::get('product_price_normal', 0)),'require'=>0),
				'product_price'=>array('value'=>(Request::get('product_price', 0)),'require'=>0),
				
				'product_wholesale'=>array('value'=>(int)(Request::get('product_wholesale')),'require'=>0),
				'product_supplier'=>array('value'=>(int)(Request::get('product_supplier')),'require'=>0),
				'product_sale'=>array('value'=>(int)(Request::get('product_sale')),'require'=>0),
				'product_khuyenmai'=>array('value'=>(int)(Request::get('product_khuyenmai')),'require'=>0),
				'product_giamgia'=>array('value'=>(int)(Request::get('product_giamgia')),'require'=>0),
				'product_moi'=>array('value'=>(int)(Request::get('product_moi')),'require'=>0),

				'meta_title'=>array('value'=>trim(Request::get('meta_title')),'require'=>0),
				'meta_keywords'=>array('value'=>trim(Request::get('meta_keywords')),'require'=>0),
				'meta_description'=>array('value'=>trim(Request::get('meta_description')),'require'=>0),
				
		);
		if(isset($dataSave['product_price_input']['value'])){
			$dataSave['product_price_input']['value'] = (int)str_replace('.', '', $dataSave['product_price_input']['value']);
		}
		if(isset($dataSave['product_price_normal']['value'])){
			$dataSave['product_price_normal']['value'] = (int)str_replace('.', '', $dataSave['product_price_normal']['value']);
		}
		if(isset($dataSave['product_price']['value'])){
			$dataSave['product_price']['value'] = (int)str_replace('.', '', $dataSave['product_price']['value']);
		}

		//get news_cat_name, news_cat_alias
		if(isset($dataSave['product_catid']['value']) && $dataSave['product_catid']['value'] > 0){
			$arrCat = Category::getById($dataSave['product_catid']['value']);
			if($arrCat != null){
				$dataSave['product_cat_name']['value'] = $arrCat->category_title;
				$dataSave['product_cat_alias']['value'] = $arrCat->category_title_alias;
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
			$dataSave['product_image']['value'] = ($image_primary != '') ? $image_primary : $arrInputImgOther[0];
			$dataSave['product_image_other']['value'] = serialize($arrInputImgOther);
		}
		
		if($id > 0){
			unset($dataSave['product_created']);
		}
		
		//Begin Size
		$size_no = Request::get('size', array());
		$num_no = Request::get('num', array());
		$arrSize = array();
		if(!empty($size_no)){
			foreach($size_no as $ksize => $kno){
				if($kno == ''){
					unset($size_no[$ksize]);
					unset($num_no[$ksize]);
				}
			}
			foreach ($size_no as $ks=>$vs) {
				foreach ($num_no  as $kn=>$vn) {
					if($ks == $kn){
						$item_size = array(
								'size'=>$vs,
								'no'=>(int)$vn,
						);
						array_push($arrSize, $item_size);
					}
				}
			}
			$dataSave['product_size_no']['value'] = serialize($arrSize);
		}
		//End Begin Size
		
		$this->error = ValidForm::validInputData($dataSave);
		if($this->error == ''){
			$id = ($id == 0) ? $id_hiden : $id;

			Product::saveData($id, $dataSave);
			return Redirect::route('admin.product');
		}else{
			foreach($dataSave as $key=>$val){
				$data[$key] = $val['value'];
			}
		}
		
		$optionStatus = Utility::getOption($this->arrStatus, isset($data['product_status'])? $data['product_status'] : -1);
		$optionFocus = Utility::getOption($this->arrFocus, isset($data['product_focus'])? $data['product_focus'] : 0);
		$optionWholesale = Utility::getOption($this->arrWholesale, isset($data['product_wholesale'])? $data['product_wholesale'] : 0);
		$optionSale = Utility::getOption($this->arrSale, isset($data['product_sale'])? $data['product_sale'] : 1);
        $optionKhuyenMai = Utility::getOption($this->arrKhuyenMai, isset($data['product_khuyenmai'])? $data['product_khuyenmai'] : -1);
        $optionGiamGia = Utility::getOption($this->arrGiamGia, isset($data['product_giamgia'])? $data['product_giamgia'] : -1);
        $optionMoi = Utility::getOption($this->arrMoi, isset($data['product_moi'])? $data['product_moi'] : -1);

		$typeId = Type::getIdByKeyword('group_product');
		$this->strCategoryProduct = CategoryController::createOptionCategory($typeId, isset($data['product_catid'])? $data['product_catid'] : 0);

		$optionSupplier = Utility::getOption($this->arrSupplier, isset($data['product_supplier'])? $data['product_supplier'] : 0);

        return view('admin.product.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'optionStatus'=>$optionStatus,
                    'optionFocus'=>$optionFocus,
                    'optionWholesale'=>$optionWholesale,
                    'optionSupplier'=>$optionSupplier,
                    'optionCategoryProduct'=>$this->strCategoryProduct,
                    'optionSale'=>$optionSale,
                    'optionKhuyenMai'=>$optionKhuyenMai,
                    'optionGiamGia'=>$optionGiamGia,
                    'optionMoi'=>$optionMoi,
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
					Trash::addItem($id, 'Product', CGlobal::FOLDER_PRODUCT, 'product_id', 'product_title', 'product_image', 'product_image_other');
					Product::deleteId($id);
				}
				Utility::messages('messages', 'Xóa thành công!', 'success');
			}
		}
		return Redirect::route('admin.product');
	}
	public function changeStatusSale(){
		$listId = Request::get('listId', array());
		$valueChange = (int)Request::get('valueChange');
		if(!empty($listId) && $valueChange > -1){
			$data['product_sale'] = $valueChange;
			foreach($listId as $id){
				if($id > 0){
					Product::updateData($id, $data);
				}
			}
			echo 1;exit();
		}
		echo 0;exit();
	}
	public function ajaxLoadItemCodeProductInOrderDetail(){
		$keyword = Request::get('keyword', '');
		$dataId = Request::get('dataId', array());
		$dataId = explode(',', $dataId);

		$html = '';
		if($keyword != ''){
			$limit = CGlobal::num_record_per_page;
			$offset = $total = 0;
			$search['product_code'] = $keyword;
			$search['product_id'] = $dataId;
			$search['notInID'] = 1;
			$data = Product::searchByCondition($search, $limit, $offset, $total);
			if(sizeof($data) > 0){
				$html.='<ul class="listCode">';
				foreach ($data as $v) {
					$html.='<li datacode="'.$v['product_code'].'" dataid="'.$v['product_id'].'" price1="'.$v['product_price_1'].'">'.$v['product_code'].'</li>';
				}
				$html.='</ul>';
			}
		}
		echo $html;die;
	}
}