<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Cronjob;

use App\Http\Models\Product;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Http\Controllers\BaseSiteController;
use Illuminate\Support\Facades\Response;

class PostProductToPartnerController extends BaseSiteController{

	private $error = '';
	
	public function __construct(){
		parent::__construct();
	}
	public function postProductToShopCuaTui(){
		$limit = CGlobal::num_record_per_page_product;
		$total = 0;
		$result = array();
		$search['product_status'] = CGlobal::status_show;
		$search['field_get'] = 'product_id,product_title,product_intro,product_content,product_price,product_price_normal,product_image,product_image_other,product_status';
		$data = Product::searchByCondition($search, $limit, 0, $total);
		if(sizeof($data) > 0){
			foreach($data as $item){
				$result[$item->product_id] = array(
					'product_id' => $item->product_id,
					'product_name' => $item->product_title,
					'product_intro' => $item->product_intro,
					'product_content'=> $item->product_content,
					'product_price_sell'=> $item->product_price,
					'product_image'=> $item->product_image,
					'product_image_other'=> $item->product_image_other,
					'product_type_price'=> 1,//1:Hien thị gia, 2: Hien thi lien he
					'product_status'=> $item->product_status,
				);
			}
		}
		return Response::json($result);
	}
	public function postProductToRaoVat30s(){
		$limit = CGlobal::num_record_per_page_product;
		$total = 0;
		$result = array();
		$search['product_status'] = CGlobal::status_show;
		$search['field_get'] = 'product_id,product_title,product_intro,product_content,product_price,product_price_normal,product_image,product_image_other,product_status';
		$data = Product::searchByCondition($search, $limit, 0, $total);
		if(sizeof($data) > 0){
			foreach($data as $item){
				$result[$item->product_id] = array(
					'product_id' => $item->product_id,
					'product_name' => $item->product_title,
					'product_intro' => $item->product_intro,
					'product_content'=> $item->product_content,
					'product_price_sell'=> $item->product_price,
					'product_image'=> $item->product_image,
					'product_image_other'=> $item->product_image_other,
					'product_type_price'=> 1,//1:Hien thị gia, 2: Hien thi lien he
					'product_status'=> $item->product_status,
				);
			}
		}
		return Response::json($result);
	}
}