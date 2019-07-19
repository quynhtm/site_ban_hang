<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

use App\Http\Models\User;
use Illuminate\Support\Facades\View;

class BaseAdminController extends Controller{

	protected $user = array();
	protected $permission = array();
	public function __construct(){
        $this->middleware(function ($request, $next) {
            if(!User::isLogin()) {
                Redirect::route('login', array('url' => self::buildUrlEncode(URL::current())))->send();
            }
            $this->user = User::userLogin();
            if(sizeof($this->user)){
                if(sizeof($this->user['user_permission']) > 0) {
                    $this->permission = $this->user['user_permission'];
                }
            }
            View::share('aryPermission',$this->permission);
            View::share('menu',$this->menu());
            View::share('user',$this->user);
            return $next($request);
        });
	}

	public function menu(){

        $menu[] = array(
            'name'=>'QL nội dung', 'link'=>'javascript:void(0)', 'icon'=>'fa fa-desktop',
            'arr_link_sub'=>array('admin.type', 'admin.category', 'admin.news', 'admin.statics', 'admin.banner', 'admin.nicksupport', 'admin.contact', 'admin.member', 'admin.emailCustomer', 'admin.commentProduct'),
            'sub'=>array(
                array('name'=>'Kiểu danh mục', 'controller'=>'Type' ,'link'=>URL::route('admin.type'), 'router_name'=>'admin.type', 'icon'=>'fa fa-folder-open icon-4x', 'showcontent'=>1, 'permission'=>'type_view'),
                array('name'=>'Danh mục', 'link'=>URL::route('admin.category'), 'router_name'=>'admin.category', 'icon'=>'fa fa-sitemap icon-4x', 'showcontent'=>1, 'permission'=>'category_view'),
                array('name'=>'Tin tức chung', 'controller'=>'News', 'link'=>URL::route('admin.news'), 'router_name'=>'admin.news', 'icon'=>'fa fa-file-text icon-4x', 'showcontent'=>1, 'permission'=>'news_view'),
                array('name'=>'Bài viết tĩnh', 'controller'=>'Statics', 'link'=>URL::route('admin.statics'), 'router_name'=>'admin.statics', 'icon'=>'fa fa-cogs icon-4x', 'showcontent'=>0, 'permission'=>'statics_view'),
                array('name'=>'Quảng cáo', 'controller'=>'Banner', 'link'=>URL::route('admin.banner'), 'router_name'=>'admin.banner', 'icon'=>'fa fa-globe icon-4x', 'showcontent'=>1, 'permission'=>'banner_view'),
                array('name'=>'Nick hỗ trợ', 'controller'=>'NickSupport', 'link'=>URL::route('admin.nicksupport'), 'router_name'=>'admin.nicksupport', 'icon'=>'fa fa-skype icon-4x', 'showcontent'=>0, 'permission'=>'nickSupport_view'),
                array('name'=>'Liên hệ', 'controller'=>'Contact', 'link'=>URL::route('admin.contact'), 'router_name'=>'admin.contact', 'icon'=>'fa fa-comments-o icon-4x', 'showcontent'=>0, 'permission'=>'contact_view'),
                array('name'=>'Thành viên', 'controller'=>'Member', 'link'=>URL::route('admin.member'), 'router_name'=>'admin.member', 'icon'=>'fa fa-linux icon-4x', 'showcontent'=>1, 'permission'=>'member_view'),
                array('name'=>'Khách mua hàng', 'controller'=>'EmailCustomer', 'link'=>URL::route('admin.emailCustomer'), 'router_name'=>'admin.emailCustomer', 'icon'=>'fa fa-child icon-4x', 'showcontent'=>1, 'permission'=>'emailCustomer_view'),
                array('name'=>'Comment sản phẩm', 'controller'=>'CommentProduct', 'link'=>URL::route('admin.commentProduct'), 'router_name'=>'admin.commentProduct', 'icon'=>'fa fa-child icon-4x', 'showcontent'=>1, 'permission'=>'product_comment_view'),
            ),
        );
        $menu[] = array(
            'name'=>'Sản phẩm', 'link'=>'javascript:void(0)', 'icon'=>'fa fa-list',
            'arr_link_sub'=>array('admin.product', 'admin.supplier', 'admin.code_ads', 'admin.purchase'),
            'sub'=>array(
                array('name'=>'Sản phẩm', 'controller'=>'Product', 'link'=>URL::route('admin.product'), 'router_name'=>'admin.product', 'icon'=>'fa fa-file icon-4x', 'showcontent'=>1, 'permission'=>'product_view'),
                array('name'=>'Nhập kho', 'controller'=>'Purchase', 'link'=>URL::route('admin.purchase'), 'router_name'=>'admin.purchase', 'icon'=>'fa fa-file icon-4x', 'showcontent'=>1, 'permission'=>'purchase_view'),
                array('name'=>'Nhà cung cấp', 'controller'=>'Supplier', 'link'=>URL::route('admin.supplier'), 'router_name'=>'admin.supplier', 'icon'=>'fa fa-reddit icon-4x', 'showcontent'=>1, 'permission'=>'supplier_view'),
                array('name'=>'Mã quảng cáo', 'controller'=>'CodeAds', 'link'=>URL::route('admin.code_ads'), 'router_name'=>'admin.code_ads', 'icon'=>'fa fa-file-archive-o icon-4x', 'showcontent'=>1, 'permission'=>'supplier_view'),
            ),
        );
        $menu[] = array(
            'name'=>'Đơn hàng', 'link'=>URL::route('admin.order'), 'router_name'=>'admin.order', 'icon'=>'fa fa-picture-o', 'showcontent'=>1, 'permission'=>'order_view',
            'arr_link_sub'=>array('admin.order'),
        );

        $menu[] = array(
            'name'=>'Đẩy SP lên đối tác', 'link'=>'javascript:void(0)', 'icon'=>'fa fa fa-calendar',
            'arr_link_sub'=>array('cronjob.postProductToRaoVat30', 'cronjob.postProductToShopCuaTui'),
            'sub'=>array(
                array('name'=>'Đẩy lên raovat30s.vn', 'controller'=>'Product', 'link'=>URL::route('cronjob.postProductToRaoVat30s'), 'router_name'=>'cronjob.postProductToRaoVat30s', 'icon'=>'fa fa-file icon-4x', 'showcontent'=>0, 'permission'=>'productToPartner_post'),
                array('name'=>'Đẩy lên ShopCuatui.com.vn', 'controller'=>'Product', 'link'=>URL::route('cronjob.postProductToShopCuaTui'), 'router_name'=>'cronjob.postProductToShopCuaTui', 'icon'=>'fa fa-reddit icon-4x', 'showcontent'=>0, 'permission'=>'productToPartner_post'),
            ),
        );
        $menu[] = array(
            'name'=>'Hệ thống', 'link'=>'javascript:void(0)', 'icon'=>'fa fa-tag',
            'arr_link_sub'=>array('admin.money', 'admin.provice', 'admin.dictrict', 'admin.ward', 'admin.permission', 'admin.role', 'admin.user', 'admin.info', 'admin.trash'),
            'sub'=>array(
                array('name'=>'Quỹ nhóm', 'link'=>URL::route('admin.money'), 'router_name'=>'admin.money', 'icon'=>'fa fa-usd icon-4x', 'showcontent'=>0, 'permission'=>'money_view'),
                array('name'=>'Tỉnh/Thành', 'link'=>URL::route('admin.provice'), 'router_name'=>'admin.provice', 'icon'=>'fa fa-map-marker icon-4x', 'showcontent'=>0, 'permission'=>'provice_view'),
                array('name'=>'Quận/Huyện', 'link'=>URL::route('admin.dictrict'), 'router_name'=>'admin.dictrict', 'icon'=>'fa fa-map-marker icon-4x', 'showcontent'=>0, 'permission'=>'dictrict_view'),
                array('name'=>'Phường/Xã', 'link'=>URL::route('admin.ward'), 'router_name'=>'admin.ward', 'icon'=>'fa fa-map-marker icon-4x', 'showcontent'=>0, 'permission'=>'ward_view'),
                array('name'=>'Danh sách quyền', 'controller'=>'Role', 'link'=>URL::route('admin.permission'), 'router_name'=>'admin.permission', 'icon'=>'fa fa-gears icon-4x', 'showcontent'=>1, 'permission'=>'userPermission_view'),
                array('name'=>'Danh sách nhóm quyền', 'controller'=>'UserRole', 'link'=>URL::route('admin.role'), 'router_name'=>'admin.role', 'icon'=>'fa fa-group icon-4x', 'showcontent'=>1, 'permission'=>'userRole_view'),
                array('name'=>'Người dùng', 'controller'=>'User', 'link'=>URL::route('admin.user'), 'router_name'=>'admin.user', 'icon'=>'fa fa-user icon-4x', 'showcontent'=>1, 'permission'=>'user_view'),
                array('name'=>'Thông tin khác', 'controller'=>'Info', 'link'=>URL::route('admin.info'), 'router_name'=>'admin.info', 'icon'=>'fa fa-cogs icon-4x', 'showcontent'=>0, 'permission'=>'info_view'),
                array('name'=>'Thùng rác', 'controller'=>'Trash', 'link'=>URL::route('admin.trash'), 'router_name'=>'admin.trash', 'icon'=>'fa fa-trash icon-4x', 'showcontent'=>1, 'permission'=>'trash_view'),
            ),
        );

		return $menu;
	}
}