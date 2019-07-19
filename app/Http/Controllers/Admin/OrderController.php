<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Models\CodeAds;
use App\Http\Models\CommentOrder;
use App\Http\Models\Dictrict;
use App\Http\Models\EmailCustomer;
use App\Http\Models\Info;
use App\Http\Models\Product;
use App\Http\Models\Provice;
use App\Http\Models\User;
use App\Http\Models\Ward;
use App\Library\PHPDev\FuncLib;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Order;
use App\Http\Models\Trash;
use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Loader;
use App\Library\PHPDev\Pagging;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ValidForm;

class OrderController extends BaseAdminController{

    private $permission_view = 'order_view';
    private $permission_create = 'order_create';
    private $permission_edit = 'order_edit';
    private $permission_delete = 'order_delete';
    private $permission_view_only = 'order_view_only';
    private $permission_ajax_status_fast = 'order_ajax_status_fast';

    private $arrStatus = array();
    private $arrChangeStatusFast = array();
    private $arrCodeAds = array();
    private $arrPartner = array();
    private $arrUser = array();
    private $arrProvice = array();
    private $arrDictrict = array();
    private $arrWard = array();
    private $error = '';

    public function __construct(){
        parent::__construct();
        Loader::loadJS('backend/js/admin.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/datetimepicker/datetimepicker.css', CGlobal::$postHead);
        Loader::loadJS('libs/datetimepicker/jquery.datetimepicker.js', CGlobal::$postEnd);
        Loader::loadCSS('libs/jAlert/jquery.alerts.css', CGlobal::$postHead);
        Loader::loadJS('libs/jAlert/jquery.alerts.js', CGlobal::$postEnd);

        $this->arrStatus = CGlobal::$arrStatusOrder;

        $this->arrPartner = CGlobal::$arrPartner;

        $listAds = CodeAds::getAllCodeAds(array(), 0);
        $this->arrCodeAds = CodeAds::arrCodeAds($listAds);

        $listUser = User::getAllUser(array(), 0);
        $this->arrUser = User::arrUser($listUser);

        $listProvice = Provice::getAllProvice(array(), 0);
        $this->arrProvice = Provice::arrProvice($listProvice);
    }
    public function checkRoleOrder(){
        //Phân quyền là nv cskh ko chuyển đc trạng thái đơn hàng(chỉ chuyển đc từ trạng thái chờ gửi sang đơn huỷ).
        //Chỉ nv gói hàng mới chuyển trạng thái đơn hàng bình thường.
        $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
        if(!in_array(CGlobal::rid_admin, $rid) && !in_array(CGlobal::rid_manager, $rid)){
            if(in_array(CGlobal::rid_cskh, $rid)){
                foreach($this->arrStatus as $key=>$status){
                    if($key != CGlobal::cho_gui && $key != CGlobal::don_huy){
                        unset($this->arrStatus[$key]);
                    }
                }
            }
        }
		
        $this->arrChangeStatusFast = CGlobal::$arrStatusOrder;
        /*
		if(array_key_exists(CGlobal::phat_da_thanh_cong, CGlobal::$arrStatusOrder)){
            unset($this->arrChangeStatusFast[CGlobal::phat_da_thanh_cong]);
        }
		*/
        if(in_array(CGlobal::rid_cskh, $rid)){
            foreach($this->arrChangeStatusFast as $key=>$status){
                if($key != CGlobal::cho_gui && $key != CGlobal::don_huy){
                    unset($this->arrChangeStatusFast[$key]);
                }
            }
        }
    }
    public function listView(){

        if(!in_array($this->permission_view, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

        $this->checkRoleOrder();

        //Config Page
        $pageNo = (int)Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page;
        $offset = $stt =($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['order_status'] = (int)Request::get('order_status', 1);
        $search['order_title'] = addslashes(Request::get('order_title', ''));

        //Phan quyen khi so luong nhan vien nhieu
        if(in_array($this->permission_view_only, $this->permission)){
            $search['order_user_id_created'] = $this->user['user_id'];
        }

        $search['field_get'] = '';

        $dataSearch = Order::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

        $optionStatus = Utility::getOption($this->arrStatus, $search['order_status']);
        $optionChangeStatusFast = Utility::getOption($this->arrChangeStatusFast, $search['order_status']);

        $messages = Utility::messages('messages');

        $uid = 0;
        if($this->user['user_rid'] == CGlobal::rid_cskh) {
            $uid = $this->user['user_id'];
        }

        return view('admin.order.list',[
            'data'=>$dataSearch,
            'total'=>$total,
            'paging'=>$paging,
            'pageNo'=>$pageNo,
            'stt'=>$stt,
            'arrStatus'=>$this->arrStatus,
            'optionStatus'=>$optionStatus,
            'optionChangeStatusFast'=>$optionChangeStatusFast,
            'search'=>$search,
            'messages'=>$messages,
            'user'=>$this->user,
            'arrUser'=>$this->arrUser,
            'arrPartner'=>$this->arrPartner,
            'uid'=>$uid,
        ]);

    }
    public function searchListView(){
        $this->checkRoleOrder();
        //Config Page
        $pageNo = (int)Request::get('page', 1);
        $pageScroll = CGlobal::num_scroll_page;
        $limit = CGlobal::num_record_per_page;
        $offset = $stt =($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['order_status'] = (int)Request::get('order_status', 1);
        $search['order_title'] = addslashes(Request::get('order_title', ''));
        $search['field_get'] = '';

        $dataSearch = Order::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getPager($pageScroll, $pageNo, $total, $limit, $search) : '';

        $optionStatus = Utility::getOption($this->arrStatus, $search['order_status']);
        $optionChangeStatusFast = Utility::getOption($this->arrChangeStatusFast, $search['order_status']);

        $messages = Utility::messages('messages');

        $uid = 0;
        if($this->user['user_rid'] == CGlobal::rid_cskh) {
            $uid = $this->user['user_id'];
        }

        return view('admin.order.list',[
                'data'=>$dataSearch,
                'total'=>$total,
                'paging'=>$paging,
                'pageNo'=>$pageNo,
                'stt'=>$stt,
                'arrStatus'=>$this->arrStatus,
                'optionStatus'=>$optionStatus,
                'optionChangeStatusFast'=>$optionChangeStatusFast,
                'search'=>$search,
                'messages'=>$messages,
                'user'=>$this->user,
                'arrUser'=>$this->arrUser,
                'arrPartner'=>$this->arrPartner,
                'uid'=>$uid,
            ]);
    }
    public function getItem($id = 0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

        $rid = $this->user['user_id'];

        $this->checkRoleOrder();
        Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);
        Loader::loadJS('libs/number/autoNumeric.js', CGlobal::$postHead);

        $data = array();
        $comment = '';
        if ($id > 0) {
            $data = Order::getById($id);
            $comment = $this->showAllComment($id);
        }

        $optionPartner = Utility::getOption($this->arrPartner, isset($data['order_partner']) ? $data['order_partner'] : CGlobal::partner_vietttel);
        $optionProvice = Utility::getOption($this->arrProvice, isset($data['order_provice_id']) ? $data['order_provice_id'] : -1);
        $optionDictrict = Utility::getOption($this->arrDictrict, isset($data['order_dictrict_id']) ? $data['order_dictrict_id'] : -1);
        $optionWard = Utility::getOption($this->arrWard, isset($data['order_ward_id']) ? $data['order_ward_id'] : -1);
        $optionCodeAds = Utility::getOption($this->arrCodeAds, isset($data['order_ads_id']) ? $data['order_ads_id'] : -1);
        $optionStatus = Utility::getOption($this->arrStatus, isset($data['order_status']) ? $data['order_status'] : CGlobal::cho_gui);
        $optionUserConfirm = Utility::getOption($this->arrUser, isset($data['order_user_id_confirm']) ? $data['order_user_id_confirm'] : 0);

        return view('admin.order.add',[
                    'id'=>$id,
                    'data'=>$data,
                    'optionStatus'=>$optionStatus,
                    'comment'=>$comment,
                    'optionPartner'=>$optionPartner,
                    'optionProvice'=>$optionProvice,
                    'optionDictrict'=>$optionDictrict,
                    'optionWard'=>$optionWard,
                    'optionCodeAds'=>$optionCodeAds,
                    'optionUserConfirm'=>$optionUserConfirm,
                    'error'=>$this->error,
                    'rid'=>$rid
                ]);

    }
    public function postItem($id = 0){

        if(!in_array($this->permission_create, $this->permission) && !in_array($this->permission_edit, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

        $rid = $this->user['user_id'];

        $this->checkRoleOrder();
        Loader::loadJS('libs/ckeditor/ckeditor.js', CGlobal::$postHead);
        Loader::loadJS('libs/number/autoNumeric.js', CGlobal::$postHead);

        $id_hiden = (int)Request::get('id_hiden', 0);
        $data = array();
        $comment = '';
        $user = $this->user;
		//Redirect to page status
        $pageNo = (int)Request::get('page', 1);
        $status = (int)Request::get('status', 1);
        //End redirect to page status
        $dataSave = array(
            'order_title' => array('value' => addslashes(Request::get('order_title')), 'require' => 1, 'messages' => 'Tên người mua không được trống!'),
            'order_phone' => array('value' => trim(Request::get('order_phone', '')), 'require' => 1, 'messages' => 'SĐT không được trống!'),
            'order_address' => array('value' => (Request::get('order_address')), 'require' => 1, 'messages' => 'Địa chỉ không được trống!'),
            'order_email' => array('value' => trim(addslashes(Request::get('order_email'))), 'require' => 0),
            'order_provice_id' => array('value' => (int)Request::get('order_provice_id', -1), 'require' => 1, 'messages' => 'Tỉnh/thành không được trống!'),
            'order_dictrict_id' => array('value' => (int)Request::get('order_dictrict_id', -1), 'require' => 1, 'messages' => 'Quận/huyện không được trống!'),
            'order_ward_id' => array('value' => (int)Request::get('order_ward_id', -1), 'require' => 1, 'messages' => 'Xã/phường không được trống!'),
            'order_note' => array('value' => addslashes(Request::get('order_note')), 'require' => 0),
            'order_note_transport' => array('value' => addslashes(Request::get('order_note_transport')), 'require' => 0),
            'order_num' => array('value' => (int)(Request::get('order_num')), 'require' => 0),
            'order_partner' => array('value' => (int)Request::get('order_partner', -1), 'require' => 0),
            'order_price_post' => array('value' => (int)str_replace('.', '', Request::get('order_price_post', 0)), 'require' => 0, 'messages' => 'Phí vận chuyển không được trống!'),
            'order_ads_id' => array('value' => (int)Request::get('order_ads_id', -1), 'require' => 0),
            'order_gift' => array('value' => addslashes(Request::get('order_gift', '')), 'require' => 0),
            'order_time_send' => array('value' => Request::get('order_time_send'), 'require' => 0),
            'order_time_finish' => array('value' => Request::get('order_time_finish'), 'require' => 0),
            'order_total_lst' => array('value' => (int)str_replace('.', '', Request::get('order_total_lst', 0)), 'require' => 0, 'messages' => 'Tổng tiền thu hộ COD không được trống!'),
            'order_link_ship' => array('value' => addslashes(Request::get('order_link_ship')), 'require' => 0),
            'order_code_post' => array('value' => addslashes(Request::get('order_code_post')), 'require' => 0),
            'order_name_facebook' => array('value' => addslashes(Request::get('order_name_facebook')), 'require' => 0),
            'order_nick_facebook' => array('value' => addslashes(Request::get('order_nick_facebook')), 'require' => 0),
            'order_link_comment_facebook' => array('value' => addslashes(Request::get('order_link_comment_facebook')), 'require' => 0),
            'order_status' => array('value' => (int)Request::get('order_status', -1), 'require' => 0),
            'order_user_id_created' => array('value' => (int)$user['user_id'], 'require' => 0),
            'order_user_name_created' => array('value' => addslashes($user['user_name']), 'require' => 0),
            'order_user_id_confirm' => array('value' => (int)Request::get('order_user_id_confirm', -1), 'require' => 0, 'messages' => 'NV chốt đơn không được trống!'),
            'order_created' => array('value' => time(), 'require' => 0),
        );

        //Add Thoi Gian Gui Cho Don hang
        if($dataSave['order_time_send']['value'] != '') {
            $order_time_send = $dataSave['order_time_send']['value'];
            $order_time_send = ($order_time_send != '') ? strtotime($order_time_send . ' ' . date('H:i:s', time())) : 0;
            $dataSave['order_time_send']['value'] = $order_time_send;
        }

		/*
        if($dataSave['order_status']['value'] == CGlobal::phat_da_thanh_cong) {
            if($dataSave['order_time_finish']['value'] == ''){
                $dataSave['order_time_finish']['value'] = '';
                $dataSave['order_time_finish']['require'] = 1;
                $dataSave['order_time_finish']['messages'] = 'Thời gian phát hàng thành công không được trống!';
            }
        }
		*/
        if($dataSave['order_time_finish']['value'] != '') {
            $order_time_finish = $dataSave['order_time_finish']['value'];
            $order_time_finish = ($order_time_finish != '') ? strtotime($order_time_finish . ' ' . date('H:i:s', time())) : 0;
            $dataSave['order_time_finish']['value'] = $order_time_finish;
        }
        $getOrderListCode = $this->getOrderListCode();
        if(sizeof($getOrderListCode) > 0){
            if(sizeof($getOrderListCode['content']) == 0){
                $dataSave['order_list_code']['value'] = -1;
                $dataSave['order_list_code']['require'] = 1;
                $dataSave['order_list_code']['messages'] = 'Mã sản phẩm và số lượng không được trống!';
            }else{
                $dataSave['order_list_code']['value'] = serialize($getOrderListCode['content']);
            }
            $dataSave['order_num']['value'] = $getOrderListCode['num'];
        }

        //Phân quyền là nv cskh ko chuyển đc trạng thái đơn hàng(chỉ chuyển đc từ trạng thái chờ gửi sang đơn huỷ).
        //Chỉ nv gói hàng mới chuyển trạng thái đơn hàng bình thường.
        $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
        if(!in_array(CGlobal::rid_admin, $rid) && !in_array(CGlobal::rid_manager, $rid)) {
            if(in_array(CGlobal::rid_cskh, $rid)) {
                if($dataSave['order_status']['value'] != CGlobal::cho_gui && $dataSave['order_status']['value'] != CGlobal::don_huy){
                    $checkData = Order::getById($id);
                    if(sizeof($checkData) > 0){
                        $dataSave['order_status']['value'] = $checkData->order_status;
                    }else{
                        $dataSave['order_status']['value'] = CGlobal::cho_gui;
                    }
                }
            }
        }

        if($dataSave['order_status']['value'] == CGlobal::chuyen_hoan || $dataSave['order_status']['value'] == CGlobal::da_lay_hang_hoan){
            if(!in_array($dataSave['order_dictrict_id']['value'], array_keys(CGlobal::$arrNoiThanhHN))){
                $dataSave['order_price_post']['value'] = CGlobal::price_chuyen_hoan_ngoai_thanh_tinh;
            }
        }

        $this->error = ValidForm::validInputData($dataSave);
        if ($this->error == '') {
            $id = ($id == 0) ? $id_hiden : $id;
            //History
            if($id > 0){
                if($rid != CGlobal::rid_admin){
                    unset($dataSave['order_time_send']);
                }
                //Update product num
                $this->updateNumProductItem($id, $dataSave['order_status']['value']);
                unset($dataSave['order_created']);
            }
            //History::addItem($id, 'Order', 'order_id', 'order_title', $dataSave, $this->user['user_id'], $this->user['user_name']);
            if($id > 0){
                unset($dataSave['order_user_id_created']);
                unset($dataSave['order_user_name_created']);
            }
            if($dataSave['order_status']['value'] == CGlobal::da_gui && $dataSave['order_time_send']['value'] == ''){
                $dataSave['order_time_send']['value'] = time();
            }

            if($dataSave['order_user_id_confirm']['value'] > -1){
                $itemUser = User::getById($dataSave['order_user_id_confirm']['value']);
                if(sizeof($itemUser) > 0){
                    $dataSave['order_user_name_confirm']['value'] = $itemUser->user_name;
                }
            }

            Order::saveData($id, $dataSave);
            //check Add Customer
            $dataCustomer = array(
                'customer_full_name' => $dataSave['order_title']['value'],
                'customer_phone' => $dataSave['order_phone']['value'],
                'customer_address' => $dataSave['order_address']['value'],
                'customer_email' => $dataSave['order_email']['value'],
                'customer_provice_id' => $dataSave['order_provice_id']['value'],
                'customer_dictrict_id' => $dataSave['order_dictrict_id']['value'],
                'customer_ward_id' => $dataSave['order_ward_id']['value'],
                'customer_name_facebook' => $dataSave['order_name_facebook']['value'],
                'customer_link_facebook' => $dataSave['order_nick_facebook']['value'],
            );
            $this->checkAddCustomer($dataSave['order_phone']['value'], $dataCustomer);
            return Redirect::route('admin.order', ['page' => $pageNo, 'order_status'=>$status]);
        } else {
            foreach ($dataSave as $key => $val) {
                $data[$key] = $val['value'];
            }
        }

        $optionPartner = Utility::getOption($this->arrPartner, isset($data['order_partner']) ? $data['order_partner'] : CGlobal::partner_vietttel);
        $optionProvice = Utility::getOption($this->arrProvice, isset($data['order_provice_id']) ? $data['order_provice_id'] : -1);
        $optionDictrict = Utility::getOption($this->arrDictrict, isset($data['order_dictrict_id']) ? $data['order_dictrict_id'] : -1);
        $optionWard = Utility::getOption($this->arrWard, isset($data['order_ward_id']) ? $data['order_ward_id'] : -1);
        $optionCodeAds = Utility::getOption($this->arrCodeAds, isset($data['order_ads_id']) ? $data['order_ads_id'] : -1);
        $optionStatus = Utility::getOption($this->arrStatus, isset($data['order_status']) ? $data['order_status'] : CGlobal::cho_gui);
        $optionUserConfirm = Utility::getOption($this->arrUser, isset($data['order_user_id_confirm']) ? $data['order_user_id_confirm'] : 0);

        return view('admin.order.add',[
                'id'=>$id,
                'data'=>$data,
                'optionStatus'=>$optionStatus,
                'comment'=>$comment,
                'optionPartner'=>$optionPartner,
                'optionProvice'=>$optionProvice,
                'optionDictrict'=>$optionDictrict,
                'optionWard'=>$optionWard,
                'optionCodeAds'=>$optionCodeAds,
                'optionUserConfirm'=>$optionUserConfirm,
                'error'=>$this->error,
                'rid'=>$rid
            ]);
    }
    public function delete(){

        if(!in_array($this->permission_delete, $this->permission)){
            Utility::messages('messages', 'Bạn không có quyền truy cập!', 'error');
            return Redirect::route('admin.dashboard');
        }

        $listId = Request::get('checkItem', array());
        $token = Request::get('_token', '');
        if (Session::token() === $token) {
            if (!empty($listId) && is_array($listId)) {
                foreach ($listId as $id) {
                    Trash::addItem($id, 'Order', '', 'order_id', 'order_title', '', '', $this->user['user_id'], $this->user['user_name']);
                    Order::deleteId($id);
                    CommentOrder::deleteCommentByOrderId($id);
                }
                Utility::messages('messages', 'Xóa thành công!', 'success');
            }
        }
        return Redirect::route('admin.order');
    }
    public function orderPrint($id = 0){
        if ($id > 0) {
            $data = Order::getById($id);
            if (sizeof($data) != 0) {
                //Thông tin người gửi
                $info_user_send = '';
                $arrUserSend = Info::getItemByKeyword('SITE_PERSON_GIVE');
                if (!empty($arrUserSend)) {
                    $info_user_send = $arrUserSend->info_content;
                }
                //Barcode
                $url_barcode = '';
                /*
				if ($data->order_code_post != '') {
                    $folder_barcode = Config::get('config.DIR_ROOT') . 'uploads/barcode/';
                    $base_url = Config::get('config.BASE_URL') . 'uploads/barcode/';

                    if (!is_dir($folder_barcode)) {
                        @mkdir($folder_barcode, 0777, true);
                        chmod($folder_barcode, 0777);
                    }

                    $filepath = $folder_barcode . $data->order_code_post . '.png';
                    if (!is_file($filepath)) {
                        $text = $data->order_code_post;
                        $size = '40';
                        $orientation = 'horizontal';
                        $code_type = 'code128';
                        $print = true;
                        Barcode::create($filepath, $text, $size, $orientation, $code_type, $print);
                    }
                    $url_barcode = $base_url . $data->order_code_post . '.png';
                }
				*/
                //Thong tin phuong/xa - quan/huyen - tinh/thanh
                $ward = '';
                $dictrict = '';
                $provice = '';

                $ItemWard = Ward::getById($data->order_ward_id);
                if(sizeof($ItemWard) > 0){
                    $ward = $ItemWard->ward_title;
                }

                $ItemDictrict = Dictrict::getById($data->order_dictrict_id);
                if(sizeof($ItemDictrict) > 0){
                    $dictrict = $ItemDictrict->dictrict_title;
                }

                $ItemProvice = Provice::getById($data->order_provice_id);
                if(sizeof($ItemProvice) > 0){
                    $provice = $ItemProvice->provice_title;
                }

                return view('admin.print.print',[
                        'data'=>$data,
                        'info_user_send'=>$info_user_send,
                        'url_barcode'=>$url_barcode,
                        'provice'=>$provice,
                        'dictrict'=>$dictrict,
                        'ward'=>$ward,
                    ]);
            } else {
                return Redirect::route('admin.order');
            }
        } else {
            return Redirect::route('admin.order');
        }
    }
    public function btnOrdersPrint(){
        $dataId = Request::get('dataId', '');
        $listData = array();
        if($dataId != ''){
            $dataId = explode(',',$dataId);
            if(is_array($dataId) && sizeof($dataId) > 0){
                //Thông tin người gửi
                $info_user_send = '';
                $arrUserSend = Info::getItemByKeyword('SITE_PERSON_GIVE');
                if (!empty($arrUserSend)) {
                    $info_user_send = $arrUserSend->info_content;
                }
                foreach($dataId as $id){
                    $data = Order::getById((int)$id);
                    if (sizeof($data) != 0) {
                        $listData[] = $data;
                    }
                }

                return view('admin.print.prints',[
                    'listData'=>$listData,
                    'info_user_send'=>$info_user_send
                ]);
            }
        }
        die;
    }
    public static function checkAddCustomer($phone='', $data=array()){
        if($phone != '' && !empty($data)){
            $checkPhoneExist = EmailCustomer::getCustomerByPhone($phone);
            if(sizeof($checkPhoneExist) == 0){
                EmailCustomer::addData($data);
            }else{
                EmailCustomer::updateData($checkPhoneExist->customer_id, $data);
            }
        }
    }
    public function changeDictrictGetPriceShip(){
        $order_dictrict_id = (int)Request::get('dictrictId', -1);
        $priceSHip = 0;
        if($order_dictrict_id != -1){
            if(array_key_exists($order_dictrict_id, CGlobal::$arrNoiThanhHN)){
                $priceSHip = CGlobal::price_ship_noi_thanh;
            }else{
                $priceSHip = CGlobal::price_ship_ngoai_thanh;
            }
        }
        echo (int)$priceSHip;die;
    }
    public function getOrderListCode(){
        $pid = Request::get('pid', array());
        $pcode = Request::get('pcode', array());
        $psize = Request::get('psize', array());
        $pnum = Request::get('pnum', array());

        $result['content'] = array();
        $result['num'] = 0;

        if(!empty($pcode)){
            foreach($pcode as $k=>$v){
                if($v == ''){
                    unset($pcode[$k]);
                    if(isset($pid[$k])){
                        unset($pid[$k]);
                    }
                    if(isset($psize[$k])){
                        unset($psize[$k]);
                    }
                    if(isset($pnum[$k])){
                        unset($pnum[$k]);
                    }
                }else{
                    if(isset($pnum[$k]) && (int)$pnum[$k] == 0){
                        if(isset($pid[$k])){
                            unset($pid[$k]);
                        }
                        if(isset($pcode[$k])){
                            unset($pcode[$k]);
                        }
                        if(isset($psize[$k])){
                            unset($psize[$k]);
                        }
                    }else{
                        $item = array(
                            'pid'=>(int)$pid[$k],
                            'pcode'=>addslashes($pcode[$k]),
                            'psize'=>addslashes($psize[$k]),
                            'pnum'=>(int)$pnum[$k],
                        );
                        $result['content'][] = $item;
                        $result['num'] += (int)$pnum[$k];
                    }
                }
            }
        }
        return $result;
    }
    public function updateNumProductItem($order_id=0, $order_status=-1){
        $error = '';
        if($order_id > 0){
            $data = Order::getById($order_id);
            if(sizeof($data) > 0){
                //Tru Trong Kho
                if(($order_status == CGlobal::da_gui || $order_status == CGlobal::phat_da_thanh_cong || $order_status == CGlobal::da_lay_tien) && ($data->order_status == CGlobal::cho_gui)){
                    $order_list_code = ($data->order_list_code != '') ? unserialize($data->order_list_code) : array();
                    if(is_array($order_list_code) && sizeof($order_list_code) > 0){
                        foreach($order_list_code as $item){
                            $pcode = $item['pcode'];
                            $psize = $item['psize'];
                            $pnum = $item['pnum'];
                            $dataProduct = Product::getProductByCode($pcode);
                            if(sizeof($dataProduct) > 0){
                                $product_size_no = ($dataProduct->product_size_no != '') ? unserialize($dataProduct->product_size_no) : array();
                                if(is_array($product_size_no) && sizeof($product_size_no) > 0){
                                    foreach($product_size_no as $pkey => $pval){
                                        if($psize == $pval['size']){
                                            if($pval['no'] < $pnum){
                                                $error .= 'Số lượng của mã <b>'.$pcode.'</b> Không đủ để gói hàng!<br/>';
                                            }else{
                                                $product_size_no[$pkey]['no'] = $pval['no'] - $pnum;
                                            }
                                        }
                                    }
                                    $dataUpdateNumProduct['product_size_no'] = serialize($product_size_no);
                                    Product::updateData($dataProduct->product_id, $dataUpdateNumProduct);
                                }
                            }
                        }
                    }
                }
                //Cong Vao Kho
                if(($order_status == CGlobal::da_lay_hang_hoan || $order_status == CGlobal::cho_gui) && ($data->order_status != CGlobal::da_lay_hang_hoan && $data->order_status != CGlobal::khieu_nai && $data->order_status != CGlobal::don_huy && $data->order_status != CGlobal::cho_gui)){
                    $order_list_code = ($data->order_list_code != '') ? unserialize($data->order_list_code) : array();
                    if(is_array($order_list_code) && sizeof($order_list_code) > 0){
                        foreach($order_list_code as $item){
                            $pcode = $item['pcode'];
                            $psize = $item['psize'];
                            $pnum = $item['pnum'];
                            $dataProduct = Product::getProductByCode($pcode);
                            if(sizeof($dataProduct) > 0){
                                $product_size_no = ($dataProduct->product_size_no != '') ? unserialize($dataProduct->product_size_no) : array();
                                if(is_array($product_size_no) && sizeof($product_size_no) > 0){
                                    foreach($product_size_no as $pkey => $pval){
                                        if($psize == $pval['size']){
                                            $product_size_no[$pkey]['no'] = $pval['no'] + $pnum;
                                        }
                                    }
                                    $dataUpdateNumProduct['product_size_no'] = serialize($product_size_no);
                                    Product::updateData($dataProduct->product_id, $dataUpdateNumProduct);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $error;
    }
    public function btnChangeOrderStatusFast(){

        if(!in_array($this->permission_ajax_status_fast, $this->permission)){
            echo 'Bạn không có quyền truy cập!';die;
        }

        $order_status = (int)Request::get('status', -1);
        $dataId = Request::get('dataId', array());
        $html = '';
        if(array_key_exists($order_status, $this->arrStatus)){
            if(sizeof($dataId) > 0 && $order_status != -1){
                foreach($dataId as $order_id){
                    $data = Order::getById($order_id);
                    if(sizeof($data) > 0){
                        //Phân quyền là nv cskh ko chuyển đc trạng thái đơn hàng(chỉ chuyển đc từ trạng thái chờ gửi sang đơn huỷ).
                        //Chỉ nv gói hàng mới chuyển trạng thái đơn hàng bình thường.
                        $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
                        if(!in_array(CGlobal::rid_admin, $rid) && !in_array(CGlobal::rid_manager, $rid)){
                            if(in_array(CGlobal::rid_cskh, $rid)){
                                if($order_status != CGlobal::cho_gui && $order_status != CGlobal::don_huy){
                                    $order_status = $data->order_status;
                                }
                            }
                        }

                        //Tru Trong Kho
                        if(($order_status == CGlobal::da_gui || $order_status == CGlobal::phat_da_thanh_cong || $order_status == CGlobal::da_lay_tien) && ($data->order_status == CGlobal::cho_gui)){
                            $order_list_code = ($data->order_list_code != '') ? unserialize($data->order_list_code) : array();
                            if(is_array($order_list_code) && sizeof($order_list_code) > 0){
                                foreach($order_list_code as $item){
                                    $pcode = $item['pcode'];
                                    $psize = $item['psize'];
                                    $pnum = $item['pnum'];
                                    $dataProduct = Product::getProductByCode($pcode);
                                    if(sizeof($dataProduct) > 0){
                                        $product_size_no = ($dataProduct->product_size_no != '') ? unserialize($dataProduct->product_size_no) : array();
                                        if(is_array($product_size_no) && sizeof($product_size_no) > 0){
                                            foreach($product_size_no as $pkey => $pval){
                                                if($psize == $pval['size']){
                                                    if($pval['no'] < $pnum){
                                                        $error .= 'Số lượng của mã <b>'.$pcode.'</b> Không đủ để gói hàng!<br/>';
                                                    }else{
                                                        $product_size_no[$pkey]['no'] = $pval['no'] - $pnum;
                                                    }
                                                }
                                            }
                                            $dataUpdateNumProduct['product_size_no'] = serialize($product_size_no);
                                            Product::updateData($dataProduct->product_id, $dataUpdateNumProduct);
                                        }
                                    }
                                }
                            }
                        }
                        //Cong Vao Kho
                        if(($order_status == CGlobal::da_lay_hang_hoan || $order_status == CGlobal::cho_gui) && ($data->order_status != CGlobal::da_lay_hang_hoan && $data->order_status != CGlobal::khieu_nai && $data->order_status != CGlobal::don_huy && $data->order_status != CGlobal::cho_gui)){
                            $order_list_code = ($data->order_list_code != '') ? unserialize($data->order_list_code) : array();
                            if(is_array($order_list_code) && sizeof($order_list_code) > 0){
                                foreach($order_list_code as $item){
                                    $pcode = $item['pcode'];
                                    $psize = $item['psize'];
                                    $pnum = $item['pnum'];
                                    $dataProduct = Product::getProductByCode($pcode);
                                    if(sizeof($dataProduct) > 0){
                                        $product_size_no = ($dataProduct->product_size_no != '') ? unserialize($dataProduct->product_size_no) : array();
                                        if(is_array($product_size_no) && sizeof($product_size_no) > 0){
                                            foreach($product_size_no as $pkey => $pval){
                                                if($psize == $pval['size']){
                                                    $product_size_no[$pkey]['no'] = $pval['no'] + $pnum;
                                                }
                                            }
                                            $dataUpdateNumProduct['product_size_no'] = serialize($product_size_no);
                                            Product::updateData($dataProduct->product_id, $dataUpdateNumProduct);
                                        }
                                    }
                                }
                            }
                        }
                        //Update status
                        if($order_status == CGlobal::da_gui){
                            if($data->order_time_send == ''){
                                $dataUpdate['order_time_send'] = time();
                            }
                        }
                        if($order_status == CGlobal::chuyen_hoan || $order_status == CGlobal::da_lay_hang_hoan){
                            if(!in_array($data->order_dictrict_id, array_keys(CGlobal::$arrNoiThanhHN))){
                                $dataUpdate['order_price_post'] = CGlobal::price_chuyen_hoan_ngoai_thanh_tinh;
                            }
                        }

                        $dataUpdate['order_status'] = $order_status;
                        Order::updateData($order_id, $dataUpdate);
                    }
                }
                if($html != ''){
                    echo $html; die;
                }
            }else{
                $html = 'Click chọn ít nhất 1 đơn hàng chuyển trạng thái.';
            }
        }else{
            $html = 'Không tồn tại trạng thái này.';
        }
        echo $html; die;
    }
    public function btnConfirmOrderPrint(){
        $dataId = Request::get('dataId', array());
        if(sizeof($dataId) > 0){
            foreach($dataId as $order_id){
                $dataUpdate['order_confirm_print'] = CGlobal::status_show;
                Order::updateData($order_id, $dataUpdate);
            }
        }
        die;
    }
    public function btnDestroyConfirmOrderPrint(){
        $order_id = Request::get('dataId', 0);
        if($order_id > 0){
            $dataUpdate['order_confirm_print'] = CGlobal::status_hide;
            Order::updateData($order_id, $dataUpdate);
        }
        die;
    }
    public function showAllComment($pid, $sort='asc', $limit=0){
        $html='';
        if($pid > 0){
            $search['comment_pid'] = $pid;
            $CommentOrder = new CommentOrder();
            $result = $CommentOrder->getAllComment($search, $limit, $sort);
            if(!empty($result)){
                $rid = (isset($this->user['user_rid']) && $this->user['user_rid'] != '') ? explode(',', $this->user['user_rid']) : array();
                foreach ($result as $item) {
                    $html .= '<li>
                        <div class="notetxt">'.$item->comment_username.':</div>
                        <div class="contenttxt">'.$item->comment_content.'</div>
                        <div class="datetxt">'.date('d/m/Y H:i:s',$item->comment_created).'</div>';
                    if(in_array(CGlobal::rid_admin, $rid) || in_array(CGlobal::rid_manager, $rid)) {
                        $html.='<div class="comment-delete" data="'.$item->comment_id.'"><i class="fa fa-remove"></i></div>';
                    }
                    $html.='</li>';
                }
            }
        }
        return  $html;
    }
}