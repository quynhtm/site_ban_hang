<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/

namespace App\Http\Controllers\Site;

use App\Http\Controllers\BaseSiteController;
use App\Http\Models\Banner;
use App\Http\Models\Category;
use App\Http\Models\CommentProduct;
use App\Http\Models\Info;
use App\Http\Models\News;
use App\Http\Models\Product;
use App\Http\Models\Statics;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\SEOMeta;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\ValidForm;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class IndexController extends BaseSiteController{
	
	public function __construct(){
		parent::__construct();
	}
	public function index(){
        Loader::loadJS('libs/owl.carousel/owl.carousel.min.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/owl.carousel/owl.carousel.min.css', CGlobal::$postHead);

        Loader::loadJS('libs/skitter-master/jquery.skitter.min.js', CGlobal::$postEnd);
        Loader::loadJS('libs/skitter-master/jquery.easing.1.3.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/skitter-master/skitter.css', CGlobal::$postHead);

		//Meta title
		$meta_title='';
		$meta_keywords='';
		$meta_description='';
		$meta_img='';
		$arrMeta = Info::getItemByKeyword('SITE_SEO_HOME');
		if(!empty($arrMeta)){
			$meta_title = $arrMeta->meta_title;
			$meta_keywords = $arrMeta->meta_keywords;
			$meta_description = $arrMeta->meta_description;
			$meta_img = $arrMeta->info_img;
			if($meta_img != ''){
				$meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $arrMeta->info_id, $arrMeta->info_img, 550, 0, '', true, true);
			}
		}
        SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);

        //Banner Slider
        $search['banner_status'] = CGlobal::status_show;
        $search['banner_type'] = 1;
        $search['field_get'] = 'banner_id,banner_title,banner_title_show,banner_image,banner_link,banner_is_target,banner_is_rel,banner_is_run_time,banner_start_time,banner_end_time';
        $dataBanner = Banner::getBannerSite($search, $limit=5, 'slider');
        $dataBanner = $this->checkBannerShow($dataBanner);

        //Banner Duoi Slider
        $search1['banner_status'] = CGlobal::status_show;
        $search1['banner_type'] = 4;
        $search1['field_get'] = 'banner_id,banner_title,banner_title_show,banner_image,banner_link,banner_is_target,banner_is_rel,banner_is_run_time,banner_start_time,banner_end_time';
        $dataBannerDuoiSlider = Banner::getBannerSite($search1, $limit=3, 'duoiSliderIndex');
        $dataBannerDuoiSlider = $this->checkBannerShow($dataBannerDuoiSlider);

        //Banner Trans
        $search2['banner_status'] = CGlobal::status_show;
        $search2['banner_type'] = 5;
        $search2['field_get'] = 'banner_id,banner_title,banner_title_show,banner_image,banner_link,banner_is_target,banner_is_rel,banner_is_run_time,banner_start_time,banner_end_time';
        $dataBannerTrans = Banner::getBannerSite($search2, $limit=3, 'trans');
        $dataBannerTrans = $this->checkBannerShow($dataBannerTrans);

        //News Bottom
        $dataFieldNews = array();
        $arrNews = News::getHotNews($dataFieldNews, 10);

        //Product New
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_product_home;
        $offset = $stt = ($pageNo - 1) * $limit;
        $searchPr = $data = array();
        $total = 0;

        $searchPr['product_status'] = CGlobal::status_show;
        $searchPr['product_moi'] = CGlobal::status_show;
        $searchPr['field_get'] = '';
        $dataProduct = Product::searchByCondition($searchPr, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $searchPr) : '';

		return view('site.content.index',[
            'dataBanner'=>$dataBanner,
            'dataBannerDuoiSlider'=>$dataBannerDuoiSlider,
            'dataBannerTrans'=>$dataBannerTrans,
            'dataProduct'=>$dataProduct,
            'paging'=>$paging,
            'arrNews'=>$arrNews
        ]);
	}
    public function actionRouter($catname, $catid){
        if($catid > 0 && $catname != ''){
            $arrCat = Category::getById($catid);
            if($arrCat != null){
                $type_keyword = $arrCat->category_type_keyword;
                if($type_keyword == 'group_product'){
                    return self::pageProduct($catname, $catid);
                }elseif($type_keyword == 'group_news'){
                    return self::pageNews($catname, $catid);
                }elseif($type_keyword == 'group_static'){
                    return self::pageStatic($catname, $catid);
                }
            }else{
                return Redirect::route('page.404');
            }
        }else{
            return Redirect::route('page.404');
        }
    }
    public function pageProduct($catname, $catid){
        Loader::loadJS('libs/bxslider/bxslider.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/bxslider/bxslider.css', CGlobal::$postHead);
        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_product;
        $offset = ($pageNo - 1) * $limit;
        $searchPr = $data = $dataCate = array();
        $total = 0;
        $paging = '';
        if($catid > 0){
            $searchPr['product_cat_alias'] = $catname;
            $searchPr['product_catid'] = $catid;
            $searchPr['product_status'] = CGlobal::status_show;
            $searchPr['field_get'] = '';
            $dataProduct = Product::searchByCondition($searchPr, $limit, $offset, $total);
            $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $searchPr) : '';
            $dataCate = Category::getById($catid);
        }
        if(sizeof($dataCate) != 0){
            $meta_title = $dataCate->meta_title;
            $meta_keywords = $dataCate->meta_keywords;
            $meta_description = $dataCate->meta_description;
            SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);
        }

        //News Left
        $dataFieldNews = array();
        $arrNews = News::getHotNews($dataFieldNews, 10);

        return view('site.content.pageProduct',[
            'catid'=>$catid,
            'dataCate'=>$dataCate,
            'dataProduct'=>$dataProduct,
            'paging'=>$paging,
            'arrNews'=>$arrNews,
        ]);

    }
    public function detailProduct($name='', $id=0){
        Loader::loadJS('libs/jqzoom/jquery.jqzoom-min.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/jqzoom/jqzoom.css', CGlobal::$postHead);

        Loader::loadJS('libs/slickslider/slick.min.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/slickslider/slick.css', CGlobal::$postHead);

        $data = $dataSame = $dataProductHot = array();
        if($id > 0){
            $data = Product::getById($id);
            $dataField['field_get'] = '';
            if(sizeof($data) != 0){
                $dataSame = Product::getSameProduct($dataField, $data->product_catid, $data->product_id, CGlobal::num_record_same_news);
                $dataFieldProduct['field_get'] = 'product_id,product_title,product_image,product_price_normal,product_price,product_catid,product_focus,product_order_no,product_created,product_status';
                $dataProductHot = Product::getProductHotRandom($dataFieldProduct, $id, CGlobal::num_record_product_hot_random);
            }else{
                return Redirect::route('page.404');
            }
        }
        //Meta title
        if(sizeof($data) != 0){
            $meta_title = $data->meta_title;
            $meta_keywords = $data->meta_keywords;
            $meta_description = $data->meta_description;
            $meta_img = $data->product_image;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $data->product_id, $data->product_image, 550, 0, '', true, true);
            }
            SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);
        }

        //Banner Trans
        $search2['banner_status'] = CGlobal::status_show;
        $search2['banner_type'] = 5;
        $search2['field_get'] = 'banner_id,banner_title,banner_title_show,banner_image,banner_link,banner_is_target,banner_is_rel,banner_is_run_time,banner_start_time,banner_end_time';
        $dataBannerTrans = Banner::getBannerSite($search2, $limit=3, 'trans');

        $strHuongDan = '';
        $arrSizeHuongDan = Info::getItemByKeyword('SITE_TEXT_HUONGDAN_SUDUNG');
        if(sizeof($arrSizeHuongDan) > 0){
            $strHuongDan = stripcslashes($arrSizeHuongDan->info_content);
        }
        return view('site.content.pageProductDetail', ['data'=>$data, 'dataSame'=>$dataSame,'dataProductHot'=>$dataProductHot,'dataBannerTrans'=>$dataBannerTrans, 'strHuongDan'=>$strHuongDan]);

    }
    public function pageProductSearch(){
        $meta_title = $meta_keywords = $meta_description = 'Kết quả tìm kiếm';
        $meta_img = '';
        $arrMeta = Info::getItemByKeyword('SITE_SEO_SEARCH');
        if(sizeof($arrMeta) > 0){
            $meta_title = $arrMeta->meta_title;
            $meta_keywords = $arrMeta->meta_keywords;
            $meta_description = $arrMeta->meta_description;
            $meta_img = $arrMeta->info_img;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $arrMeta->info_id, $arrMeta->info_img, 550, 0, '', true, true);
            }
        }
        SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);

        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_product;
        $offset = ($pageNo - 1) * $limit;
        $search = $dataProduct = array();
        $total = 0;
        $paging = '';

        $keyword = addslashes(Request::get('keyword', ''));
        if($keyword != ''){
            $search['keyword'] = $keyword;
            $search['product_status'] = CGlobal::status_show;
            $search['field_get'] = '';
            $dataProduct = Product::searchByCondition($search, $limit, $offset, $total);
            $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
        }

        //News Left
        $dataFieldNews = array();
        $arrNews = News::getHotNews($dataFieldNews, 10);

        return view('site.content.pageProductSearch',[
            'dataProduct'=>$dataProduct,
            'paging'=>$paging,
            'arrNews'=>$arrNews,
        ]);
    }
    public function pageProductNew(){
        $meta_title = $meta_keywords = $meta_description = 'Hàng mới về';
        $meta_img = '';
        $arrMeta = Info::getItemByKeyword('SITE_SEO_PRODUCT_NEW');
        if(sizeof($arrMeta) > 0){
            $meta_title = $arrMeta->meta_title;
            $meta_keywords = $arrMeta->meta_keywords;
            $meta_description = $arrMeta->meta_description;
            $meta_img = $arrMeta->info_img;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $arrMeta->info_id, $arrMeta->info_img, 550, 0, '', true, true);
            }
        }
        SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);

        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_product;
        $offset = ($pageNo - 1) * $limit;
        $search = $dataProduct = array();
        $total = 0;
        $paging = '';

        $search['product_moi'] = CGlobal::status_show;;
        $search['product_status'] = CGlobal::status_show;
        $search['field_get'] = '';
        $dataProduct = Product::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

        //News Left
        $dataFieldNews = array();
        $arrNews = News::getHotNews($dataFieldNews, 10);


        //Banner Duoi Hang Moi Ve
        $search1['banner_status'] = CGlobal::status_show;
        $search1['banner_type'] = 6;
        $search1['field_get'] = 'banner_id,banner_title,banner_title_show,banner_image,banner_link,banner_is_target,banner_is_rel,banner_is_run_time,banner_start_time,banner_end_time';
        $dataBannerNew = Banner::getBannerSite($search1, $limit=3, 'pNew');

        return view('site.content.pageProductNew',[
            'dataProduct'=>$dataProduct,
            'paging'=>$paging,
            'arrNews'=>$arrNews,
            'dataBannerNew'=>$dataBannerNew,
        ]);
    }
    public function pageNews($catname, $catid){
        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_news;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = $dataCate = $dataProductHot = array();
        $total = 0;
        $paging = '';
        if($catid > 0){
            $search['news_cat_alias'] = $catname;
            $search['news_catid'] = $catid;
            $search['news_status'] = CGlobal::status_show;
            $search['field_get'] = 'news_id,news_title,news_catid,news_cat_alias,news_intro,news_content,news_image,news_created,news_status';
            $data = News::searchByCondition($search, $limit, $offset, $total);
            $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
            $dataCate = Category::getById($catid);

            $dataFieldProduct['field_get'] = 'product_id,product_title,product_image,product_price_normal,product_price,product_catid,product_focus,product_order_no,product_created,product_status';
            $dataProductHot = Product::getProductHotRandom($dataFieldProduct, 0, CGlobal::num_record_product_hot_random);
        }
        if(sizeof($dataCate) != 0){
            $meta_title = $dataCate->meta_title;
            $meta_keywords = $dataCate->meta_keywords;
            $meta_description = $dataCate->meta_description;
            SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);
        }

        if($catid == CGlobal::catIDRecruitment) {
            $theme = 'site.content.pageRecruitment';
        }else{
            $theme = 'site.content.pageNews';
        }
        return view($theme, ['data'=>$data, 'dataCate'=>$dataCate, 'paging'=>$paging, 'dataProductHot'=>$dataProductHot]);
    }
    public function detailNews($name='', $id=0){
        $data = $dataSame = $dataProductHot = array();
        if($id > 0){
            $data = News::getById($id);
            if(sizeof($data) != 0){
                $dataField['field_get'] = '';
                $dataSame = News::getSameNews($dataField, $data->news_catid, $data->news_id, CGlobal::num_record_same_news);

                $dataFieldProduct['field_get'] = '';
                $dataProductHot = Product::getProductHotRandom($dataFieldProduct, 0, CGlobal::num_record_product_hot_random);
            }else{
                return Redirect::route('page.404');
            }
        }
        //Meta title
        if(sizeof($data) != 0){
            $meta_title = $data->meta_title;
            $meta_keywords = $data->meta_keywords;
            $meta_description = $data->meta_description;
            $meta_img = $data->news_image;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $data->news_id, $data->news_image, 550, 0, '', true, true);
            }
            SEOMeta::init($meta_img, $meta_title, $meta_keywords, $meta_description);
        }
        return view('site.content.pageNewsDetail', ['data'=>$data, 'dataSame'=>$dataSame, 'dataProductHot'=>$dataProductHot]);
    }
    public function pageStatic($catname, $catid){
        //Config Page
        $pageNo = (int) Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page_news;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = $dataCate = $dataProductHot = array();
        $total = 0;
        $paging = '';
        if($catid > 0){
            $search['statics_cat_alias'] = $catname;
            $search['statics_catid'] = $catid;
            $search['statics_status'] = CGlobal::status_show;
            $search['field_get'] = '';
            $data = Statics::searchByCondition($search, $limit, $offset, $total);
            $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';
            $dataCate = Category::getById($catid);

            $dataFieldProduct['field_get'] = 'product_id,product_title,product_image,product_price_normal,product_price,product_catid,product_focus,product_order_no,product_created,product_status';
            $dataProductHot = Product::getProductHotRandom($dataFieldProduct, 0, CGlobal::num_record_product_hot_random);
        }
        if(sizeof($dataCate) != 0){
            $meta_title = $dataCate->meta_title;
            $meta_keywords = $dataCate->meta_keywords;
            $meta_description = $dataCate->meta_description;
            SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);
        }

        return view('site.content.pageStatics', ['data'=>$data, 'dataCate'=>$dataCate, 'paging'=>$paging, 'dataProductHot'=>$dataProductHot]);
    }
    public function detailStatics($name='', $id=0){
        $data = array();
        if($id > 0){
            $data = Statics::getById($id);
            if(sizeof($data) == 0){
                return Redirect::route('page.404');
            }
        }
        //Meta title
        if(sizeof($data) != 0){
            $meta_title = $data->meta_title;
            $meta_keywords = $data->meta_keywords;
            $meta_description = $data->meta_description;
            $meta_img = $data->statics_image;
            if($meta_img != ''){
                $meta_img = ThumbImg::thumbBaseNormal(CGlobal::FOLDER_STATICS, $data->statics_id, $data->statics_image, 550, 0, '', true, true);
            }
            SEOMeta::init('', $meta_title, $meta_keywords, $meta_description);
        }
        return view('site.content.pageStaticsDetail', ['id'=>$id, 'data'=>$data]);
    }
    public function ajaxGetCommentInProduct(){

        $pid = addslashes(Request::get('pid', ''));
        $_token = addslashes(Request::get('_token', ''));

        $result = '';
        $html = '';
        if($pid > 0){
            $search['comment_pid'] = $pid;
            $search['comment_catid'] = 0;
            $search['comment_status'] = CGlobal::status_show;
            $CommentProduct = new CommentProduct();
            $result = $CommentProduct->getAllComment($search, 10, 'desc');
        }
        if(sizeof($result) > 0){
            foreach($result as $item){

                $search1['comment_pid'] = $pid;
                $search1['comment_catid'] = $item->comment_id;
                $search['comment_status'] = CGlobal::status_show;
                $result1 = $CommentProduct->getAllComment($search1, 5, 'asc');

                $showDate = '';
                $ago = 0;
                if($item->comment_created > 0){
                    if(time() >= $item->comment_created){
                        $ago = time() - $item->comment_created;
                        $showDate = FuncLib::showTimeAgo($ago);
                    }
                }
                $name = '';
                if($item->comment_phone != ''){
                    $name = FuncLib::replaceText($item->comment_phone, 0, 5, '*****');
                }else{
                    $name = FuncLib::replaceText($item->comment_mail, 0, 5, '*****');
                }
                $html1 = '';
                if(sizeof($result1) > 0){
                    $html1 .= '<div class="line"><div class="list-reply">';
                    foreach($result1 as $sub){
                        $name1 = '';
                        if($sub->uid > 0){
                            $name1 = $sub->comment_username;
                        }else{
                            if($item->comment_phone != ''){
                                $name1 = FuncLib::replaceText($sub->comment_phone, 0, 5, '*****');
                            }else{
                                $name1 = FuncLib::replaceText($sub->comment_mail, 0, 5, '*****');
                            }
                        }
                        $html1 .= '<div class="line">
                                        <div class="cmt-content"><b>'.$name1.'</b></div>
                                        <div class="cmt-content">'.stripcslashes($sub->comment_content).'</div>
                                    </div>';
                    }
                    $html1.=    '</div></div>';
                }

                $html .= '<div class="item-comment-show">
                        <div class="box-left-cmt">
                            <div class="icon-img-cmt">
                                <img src="'.Config::get('config.BASE_URL').'/assets/frontend/img/ic-cmt.png" alt="icon cmt">
                            </div>
                        </div>
                        <div class="box-right-cmt">
                            <div class="line">
                                <span class="cmt-time"><span title="'.date("d/m/Y, H:m:i", $item->comment_created).'">'.$showDate.'</span></span>
                                <span class="cmt-author">'.$name.'</span>
                            </div>
                            <div class="line">
                                <p class="cmt-content">'.stripcslashes($item->comment_content).'</p>
                                <a class="clickAnswer" href="javascript:void(0)"  data-pid="'.$item->comment_pid.'" data-id="'.$item->comment_id.'">Trả lời</a>
                            </div>
                            '.$html1.'
                        </div>
                    </div>';
            }
        }
        echo $html;die;
    }
    public function ajaxAddCommentInProduct(){
        $rqMailPhone = addslashes(Request::get('rqMailPhone', ''));
        $rqContent = addslashes(Request::get('rqContent', ''));
        $pid = (int)addslashes(Request::get('pid', 0));
        if($pid > 0 && $rqMailPhone != '' && $rqContent != ''){
            $data = array(
                'comment_pid'=>$pid,
                'comment_catid'=>0,
                'comment_content'=>$rqContent,
                'comment_created'=>time(),
                'uid'=>0,
                'comment_status'=>CGlobal::status_hide,
            );
            $check = ValidForm::checkRegexEmail($rqMailPhone);
            if($check == true){
                $data['comment_mail'] = $rqMailPhone;
            }else{
                $check = ValidForm::checkRegexPhone($rqMailPhone);
                if($check == true){
                    $data['comment_phone'] = $rqMailPhone;
                }
            }
            if(isset($data['comment_mail']) || isset($data['comment_phone'])){
                CommentProduct::addData($data);
            }else{
                echo 'Không đúng định dạng mail hoặc số điện thoại!';die;
            }
        }
        echo 'ok';die;
    }
}
