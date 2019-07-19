<?php
/*
* @Created by: HSS
* @Author    : quynhtm
* @Date      : 08/2016
* @Version   : 1.0
*/
namespace App\Library\PHPDev;

class Memcache{
	
	const CACHE_ON = 1 ; // 0:Not cache, 1:Cache
	const CACHE_TIME_TO_LIVE_15 = 900; //Time cache 15 minute
	const CACHE_TIME_TO_LIVE_30 = 1800; //Time cache 30 minute
	const CACHE_TIME_TO_LIVE_60 = 3600; //Time cache 60 minute
	const CACHE_TIME_TO_LIVE_ONE_DAY = 86400; //Time cache 1 day
	const CACHE_TIME_TO_LIVE_ONE_WEEK = 604800; //Time cache 7 day
	const CACHE_TIME_TO_LIVE_ONE_MONTH = 2419200; //Time cache 1 month
	const CACHE_TIME_TO_LIVE_ONE_YEAR =  29030400; //Time cache 1 year

	//Role
	const CACHE_ROLE_ID    = 'cache_role_id_';
	//User
	const CACHE_USER_ID    = 'cache_user_id_';
	const CACHE_ALL_USER   = 'cache_user_all_';
	//Type
	const CACHE_TYPE_ID    = 'cache_type_id_';
	const CACHE_TYPE_KEYWORD    = 'cache_type_keyword_';
	const CACHE_TYPE_ALL    = 'cache_type_all';
	//Category
	const CACHE_CATEGORY_ID    = 'cache_category_id_';
	const CACHE_ALL_CATEGORY    = 'cache_all_category';
	const CACHE_ALL_CATEGORY_BY_TYPE    = 'cache_all_category_by_type_';
	const CACHE_SUB_CATEGORY    = 'cache_sub_category_';
	//Info
	const CACHE_INFO_ID    = 'cache_info_id_';
	const CACHE_INFO_KEYWORD    = 'cache_info_keyword_';
	//Banner
	const CACHE_BANNER_ID    = 'cache_banner_id_';
	const CACHE_BANNER_SITE    = 'cache_banner_site_';
	//Trash
	const CACHE_TRASH_ID    = 'cache_trash_id_';
	//Contact
	const CACHE_CONTACT_ID    = 'cache_contact_id_';
	//News
	const CACHE_NEWS_ID    = 'cache_news_id_';
	//Product
	const CACHE_PRODUCT_ID    = 'cache_product_id_';
	//Nick Support
	const CACHE_NICK_SUPPORT_ID    = 'cache_nick_support_id_';
	//Order
	const CACHE_ORDER_ID    = 'cache_order_id_';
	const CACHE_ORDER_COUNT_STATUS    = 'cache_order_count_status_';
	//Member
	const CACHE_MEMBER_ID    = 'cache_member_id_';
	//Supplier
	const CACHE_ALL_SUPPLIER = 'cache_all_supplier';
	const CACHE_SUPPLIER_ID = 'cache_supplier_id_';
	//Provice
	const CACHE_ALL_PROVICE = 'cache_all_provice';
	const CACHE_PROVICE_ID = 'cache_provice_id_';
	//Dictrict
	const CACHE_ALL_DICTRICT = 'cache_all_dictrict';
	const CACHE_DICTRICT_ID = 'cache_dictrict_id_';
	const CACHE_ALL_DICTRICT_BY_PROVICE = 'cache_all_dictrict_by_provice_';
	//Ward
	const CACHE_ALL_WARD = 'cache_all_ward';
	const CACHE_WARD_ID = 'cache_ward_id_';
	const CACHE_ALL_WARD_BY_DICTRICT = 'cache_all_ward_by_dictrict_';
	//Statics
	const CACHE_STATICS_ID = 'cache_statics_id_';
	const CACHE_STATICS_CAT_ID = 'cache_statics_cat_id_';
	//Ads
	const CACHE_CODE_ADS_ID = 'cache_code_ads_id_';
	const CACHE_ALL_CODE_ADS = 'cache_all_code_ads_';
	const CACHE_MONEY_ID = 'cache_money_id_';

	const CACHE_COMMENT_PRODUCT_ID = 'cache_comment_product_id_';
	const CACHE_PURCHASE_ID = 'cache_purchase_id_';
}