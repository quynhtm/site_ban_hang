<?php
/*
* @Created by: HSS
* @Author	 : quynhtm
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Library\PHPDev;

class CGlobal{
    static $cssVer = 1.0;
    static $jsVer = 1.0;

    //Position Header, Footer
    public static $postHead = 1;
    public static $postEnd = 2;

    //Add CSS, JS, Meta
    public static $extraHeaderCSS = '';
    public static $extraHeaderJS = '';
    public static $extraFooterCSS = '';
    public static $extraFooterJS = '';
    public static $extraMeta = '';

    //Dev
    const is_dev = 0;

    //Role
    const rid_admin = 1;
    const rid_manager = 2;
    const rid_cskh = 3;
    const rid_goihang = 4;

    const domain = 'HN-STORE.net';
    const nameSite = 'Phụ kiện thời trang nam';
    const phoneSupport = '0913.922.986';
    const emailAdmin = 'quynhtm';

    const txt403 = 'Không được truy cập...';
    const txt404 = 'Không tìm thấy...';

    const num_record_per_page = 30;
    const num_scroll_page = 2;

    const num_record_per_page_product_home = 25;
    const num_record_per_page_product = 20;
    const num_record_same_product = 10;
    const num_record_product_hot_random = 5;

    const num_record_per_page_news = 10;
    const num_record_same_news = 4;

    const max_num_record_order = 100;

    const status_hide = 0;
    const status_show = 1;

    //Status product
    const product_sale_off = 0;//Het Hang
    const product_sale_on = 1;//Con Hang

    //Status Order
    const cho_gui = 1;
    const da_gui = 2;
    const phat_da_thanh_cong = 3;
    const phat_chua_thanh_cong = 4;
    const chuyen_hoan = 5;
    const khieu_nai = 6;
    const don_huy = 7;
    const da_lay_tien = 8;
    const da_lay_hang_hoan = 9;
	const khach_phan_van = 10;
	const dat_lich_gui = 11;

    public static $arrStatusOrder = array(
        '-1'=>'--Chọn trạng thái--',
		self::khach_phan_van=>'Khách phân vân',
		self::dat_lich_gui=>'Đặt lịch gửi',
        self::cho_gui=>'Chờ gửi',
        self::da_gui=>'Đã gửi',
        self::phat_da_thanh_cong=>'Đã phát thành công',
        self::phat_chua_thanh_cong=>'Phát chưa thành công',
        self::da_lay_tien=>'Đã lấy tiền',
        self::chuyen_hoan=>'Chuyển hoàn',
        self::da_lay_hang_hoan=>'Đã lấy hàng hoàn',
        self::khieu_nai=>'Khiếu nại',
        self::don_huy=>'Đơn hủy',
    );

    const provice_hanoi_id = 1;
    public static $arrNoiThanhHN = array(
        3=>'Hoàn Kiếm',
        4=>'Hai Bà Trưng',
        5=>'Hoàng Mai',
        6=>'Ba Đình',
        7=>'Đống Đa',
        8=>'Long Biên',
        9=>'Tây Hồ',
        10=>'Cầu Giấy',
        11=>'Hà Đông',
        12=>'Thanh Xuân',
    );

    //Status ADS
    const codeads_status_1 = 1;
    const codeads_status_2 = 2;
    const codeads_status_3 = 3;
    public static $arrStatusAds = array(
        '-1'=>'--Chọn trạng thái--',
        self::codeads_status_1=>'Chưa quảng cáo',
        self::codeads_status_2=>'Đang quảng cáo',
        self::codeads_status_3=>'Đã dừng quảng cáo',
    );

    //Price ship
    const price_ship_noi_thanh = 20000;
    const price_ship_ngoai_thanh = 30000;
    const price_chuyen_hoan_ngoai_thanh_tinh = 30000;
    const total_price_default = 199000;

    //Size Img
    public static $arrSizeImg = array(
        '2'=>'200x200',
        '4'=>'400x400',
        '6'=>'600x600',
        '8'=>'800x800',
    );

    //Folder
    const IMAGE_ERROR = 133;
    const FOLDER_BANNER = 'banner';
    const FOLDER_NEWS = 'news';
    const FOLDER_PRODUCT = 'product';
    const FOLDER_CATEGORY = 'category';
    const FOLDER_TRASH = 'trash';
    const FOLDER_INFO = 'info';
    const FOLDER_STATICS = 'statics';

    //Partner Transport
    const partner_me = 0;
    const partner_shipchung = 1;
    const partner_goldship = 2;
    const partner_vietttel = 3;
    const partner_vnpost = 4;
    const partner_ghtk = 5;
    const partner_wefast = 6;
    const partner_nhanhvn = 7;
    const partner_ghn = 8;
    public static $arrPartner = array(
        self::partner_me=>'Tự giao hàng',
        self::partner_shipchung=>'Shipchung',
        self::partner_goldship=>'Goldship',
        self::partner_vietttel=>'Vietttel',
        self::partner_vnpost=>'VN Post',
        self::partner_ghtk=>'Giao hàng tiết kiệm',
        self::partner_ghn=>'Giao hàng nhanh',
        self::partner_wefast=>'WE FAST',
        self::partner_nhanhvn=>'Nhanh.vn',
    );

    //Link order Partner
    const link_order_shipchung = 'http://seller.shipchung.vn/#/detail/';
    const link_order_viettel = 'http://kh.vtp.vn/#/app/order/list/month/';
    const link_order_goldship = 'http://online.goldship.vn/app/order-tracking?orderNum=';

    //Link Social
    const link_social_facebook = 'https://www.facebook.com/HNStoreMen';
    const link_social_google_plus = 'https://plus.google.com/116218198759731214052';

    const catIDRecruitment = 533;
    const catIDNews = 532;
    const catIDPromotions = 541;


    //Api Key Facebook
    const facebook_app_id = '1890325594569516';
    const facebook_app_secret = '642b3b976c5d87af4a3afa329c4277d0';
    const facebook_default_graph_version = 'v2.8';
    const facebook_persistent_data_handler = 'session';

    //Api Key Google
    const googe_client_id = '1047354159382-vn6jr70nfqmchqdarhdo62a503rq63gl.apps.googleusercontent.com';
    const googe_client_secret = 'SpM-HrTPv6bUzbb3FHuoiR4P';
}