<?php
/*
* @Created by: HSS
* @Author    : quynhtm
* @Date      : 08/2016
* @Version   : 1.0
*/
Route::get('dashboard', array('as' => 'admin.dashboard','uses' => 'Admin\DashBoardController@listView'));

Route::get('type', array('as' => 'admin.type','uses' => '\App\Http\Controllers\Admin\TypeController@listView'));
Route::get('type/edit/{id?}', array('as' => 'admin.type_edit','uses' => 'Admin\TypeController@getItem'))->where('id', '[0-9]+');
Route::post('type/edit/{id?}', array('as' => 'admin.type_edit','uses' => 'Admin\TypeController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'type/delete', array('as' => 'admin.type_delete','uses' => 'Admin\TypeController@delete'));

Route::get('category', array('as' => 'admin.category','uses' => '\App\Http\Controllers\Admin\CategoryController@listView'));
Route::get('category/edit/{id?}', array('as' => 'admin.category_edit','uses' => 'Admin\CategoryController@getItem'))->where('id', '[0-9]+');
Route::post('category/edit/{id?}', array('as' => 'admin.category_edit','uses' => 'Admin\CategoryController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'category/delete', array('as' => 'admin.category_delete','uses' => 'Admin\CategoryController@delete'));

Route::get('product', array('as' => 'admin.product','uses' => '\App\Http\Controllers\Admin\ProductController@listView'));
Route::get('product/edit/{id?}', array('as' => 'admin.product_edit','uses' => 'Admin\ProductController@getItem'))->where('id', '[0-9]+');
Route::post('product/edit/{id?}', array('as' => 'admin.product_edit','uses' => 'Admin\ProductController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'product/delete', array('as' => 'admin.product_delete','uses' => 'Admin\ProductController@delete'));
Route::match(['GET','POST'],'product/change-sale', array('as' => 'admin.product_sale','uses' => 'Admin\ProductController@changeStatusSale'));
Route::match(['GET','POST'],'product/ajaxLoadItemCodeProductInOrderDetail', array('as' => 'admin.ajaxLoadItemCodeProductInOrderDetail','uses' => 'Admin\ProductController@ajaxLoadItemCodeProductInOrderDetail'));

//Nhap san pham vao kho cho xac nhan
Route::get('purchase', array('as' => 'admin.purchase','uses' => '\App\Http\Controllers\Admin\PurchaseController@listView'));
Route::get('purchase/edit/{id?}', array('as' => 'admin.purchase_edit','uses' => 'Admin\PurchaseController@getItem'))->where('id', '[0-9]+');
Route::post('purchase/edit/{id?}', array('as' => 'admin.purchase_edit','uses' => 'Admin\PurchaseController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'purchase/delete', array('as' => 'admin.purchase_delete','uses' => 'Admin\PurchaseController@delete'));


Route::get('supplier', array('as' => 'admin.supplier','uses' => '\App\Http\Controllers\Admin\SupplierController@listView'));
Route::get('supplier/edit/{id?}', array('as' => 'admin.supplier_edit','uses' => 'Admin\SupplierController@getItem'))->where('id', '[0-9]+');
Route::post('supplier/edit/{id?}', array('as' => 'admin.supplier_edit','uses' => 'Admin\SupplierController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'supplier/delete', array('as' => 'admin.supplier_delete','uses' => 'Admin\SupplierController@delete'));

Route::get('news', array('as' => 'admin.news','uses' => '\App\Http\Controllers\Admin\NewsController@listView'));
Route::get('news/edit/{id?}', array('as' => 'admin.news_edit','uses' => 'Admin\NewsController@getItem'))->where('id', '[0-9]+');
Route::post('news/edit/{id?}', array('as' => 'admin.news_edit','uses' => 'Admin\NewsController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'news/delete', array('as' => 'admin.news_delete','uses' => 'Admin\NewsController@delete'));

Route::get('statics', array('as' => 'admin.statics','uses' => '\App\Http\Controllers\Admin\StaticsController@listView'));
Route::get('statics/edit/{id?}', array('as' => 'admin.statics_edit','uses' => 'Admin\StaticsController@getItem'))->where('id', '[0-9]+');
Route::post('statics/edit/{id?}', array('as' => 'admin.statics_edit','uses' => 'Admin\StaticsController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'statics/delete', array('as' => 'admin.statics_delete','uses' => 'Admin\StaticsController@delete'));

Route::get('banner', array('as' => 'admin.banner','uses' => '\App\Http\Controllers\Admin\BannerController@listView'));
Route::get('banner/edit/{id?}', array('as' => 'admin.banner_edit','uses' => 'Admin\BannerController@getItem'))->where('id', '[0-9]+');
Route::post('banner/edit/{id?}', array('as' => 'admin.banner_edit','uses' => 'Admin\BannerController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'banner/delete', array('as' => 'admin.banner_delete','uses' => 'Admin\BannerController@delete'));

Route::get('contact', array('as' => 'admin.contact','uses' => '\App\Http\Controllers\Admin\ContactController@listView'));
Route::get('contact/edit/{id?}', array('as' => 'admin.contact_edit','uses' => 'Admin\ContactController@getItem'))->where('id', '[0-9]+');
Route::post('contact/edit/{id?}', array('as' => 'admin.contact_edit','uses' => 'Admin\ContactController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'contact/delete', array('as' => 'admin.contact_delete','uses' => 'Admin\ContactController@delete'));

Route::get('member', array('as' => 'admin.member','uses' => '\App\Http\Controllers\Admin\MemberController@listView'));
Route::get('member/edit/{id?}', array('as' => 'admin.member_edit','uses' => 'Admin\MemberController@getItem'))->where('id', '[0-9]+');
Route::post('member/edit/{id?}', array('as' => 'admin.member_edit','uses' => 'Admin\MemberController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'member/delete', array('as' => 'admin.member_delete','uses' => 'Admin\MemberController@delete'));

Route::get('nicksupport', array('as' => 'admin.nicksupport','uses' => '\App\Http\Controllers\Admin\NickSupportController@listView'));
Route::get('nicksupport/edit/{id?}', array('as' => 'admin.nicksupport_edit','uses' => 'Admin\NickSupportController@getItem'))->where('id', '[0-9]+');
Route::post('nicksupport/edit/{id?}', array('as' => 'admin.nicksupport_edit','uses' => 'Admin\NickSupportController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'nicksupport/delete', array('as' => 'admin.nicksupport_delete','uses' => 'Admin\NickSupportController@delete'));

Route::get('provice', array('as' => 'admin.provice','uses' => '\App\Http\Controllers\Admin\ProviceController@listView'));
Route::get('provice/edit/{id?}', array('as' => 'admin.provice_edit','uses' => '\App\Http\Controllers\Admin\ProviceController@getItem'))->where('id', '[0-9]+');
Route::post('provice/edit/{id?}', array('as' => 'admin.provice_edit','uses' => '\App\Http\Controllers\Admin\ProviceController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'provice/delete', array('as' => 'admin.provice_delete','uses' => '\App\Http\Controllers\Admin\ProviceController@delete'));

Route::get('dictrict', array('as' => 'admin.dictrict','uses' => '\App\Http\Controllers\Admin\DictrictController@listView'));
Route::get('dictrict/edit/{id?}', array('as' => 'admin.dictrict_edit','uses' => 'Admin\DictrictController@getItem'))->where('id', '[0-9]+');
Route::post('dictrict/edit/{id?}', array('as' => 'admin.dictrict_edit','uses' => 'Admin\DictrictController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'dictrict/delete', array('as' => 'admin.dictrict_delete','uses' => 'Admin\DictrictController@delete'));
Route::match(['GET','POST'],'dictrict/ajaxGetDictrictByProvice', array('as' => 'admin.ajaxGetDictrictByProvice','uses' => 'Admin\DictrictController@ajaxGetDictrictByProvice'));

Route::get('ward', array('as' => 'admin.ward','uses' => '\App\Http\Controllers\Admin\WardController@listView'));
Route::get('ward/edit/{id?}', array('as' => 'admin.ward_edit','uses' => 'Admin\WardController@getItem'))->where('id', '[0-9]+');
Route::post('ward/edit/{id?}', array('as' => 'admin.ward_edit','uses' => 'Admin\WardController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'ward/delete', array('as' => 'admin.ward_delete','uses' => 'Admin\WardController@delete'));
Route::match(['GET','POST'],'ward/ajaxGetWardByDictrict', array('as' => 'admin.ajaxGetWardByDictrict','uses' => 'Admin\WardController@ajaxGetWardByDictrict'));

Route::get('emailCustomer', array('as' => 'admin.emailCustomer','uses' => '\App\Http\Controllers\Admin\EmailCustomerController@listView'));
Route::get('emailCustomer/edit/{id?}', array('as' => 'admin.emailCustomer_edit','uses' => 'Admin\EmailCustomerController@getItem'))->where('id', '[0-9]+');
Route::post('emailCustomer/edit/{id?}', array('as' => 'admin.emailCustomer_edit','uses' => 'Admin\EmailCustomerController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'emailCustomer/delete', array('as' => 'admin.emailCustomer_delete','uses' => 'Admin\EmailCustomerController@delete'));
Route::match(['GET','POST'],'emailCustomer/ajaxGetOrderCustomer', array('as' => 'admin.ajaxGetOrderCustomer','uses' => 'Admin\EmailCustomerController@ajaxGetOrderCustomer'));

Route::get('info', array('as' => 'admin.info','uses' => '\App\Http\Controllers\Admin\InfoController@listView'));
Route::get('info/edit/{id?}', array('as' => 'admin.info_edit','uses' => 'Admin\InfoController@getItem'))->where('id', '[0-9]+');
Route::post('info/edit/{id?}', array('as' => 'admin.info_edit','uses' => 'Admin\InfoController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'info/delete', array('as' => 'admin.info_delete','uses' => 'Admin\InfoController@delete'));

//User - Role
Route::get('permission', array('as' => 'admin.permission','uses' => '\App\Http\Controllers\Admin\UserPermissionController@listView'));
Route::get('permission/edit/{id?}', array('as' => 'admin.permission_edit','uses' => 'Admin\UserPermissionController@getItem'))->where('id', '[0-9]+');
Route::post('permission/edit/{id?}', array('as' => 'admin.permission_edit','uses' => 'Admin\UserPermissionController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'permission/delete', array('as' => 'admin.permission_delete','uses' => 'Admin\UserPermissionController@delete'));

Route::get('role', array('as' => 'admin.role','uses' => '\App\Http\Controllers\Admin\UserRoleController@listView'));
Route::get('role/edit/{id?}', array('as' => 'admin.role_edit','uses' => 'Admin\UserRoleController@getItem'))->where('id', '[0-9]+');
Route::post('role/edit/{id?}', array('as' => 'admin.role_edit','uses' => 'Admin\UserRoleController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'role/delete', array('as' => 'admin.role_delete','uses' => 'Admin\UserRoleController@delete'));

Route::get('users', array('as' => 'admin.user','uses' => '\App\Http\Controllers\Admin\UserController@listView'));
Route::get('users/edit/{id?}', array('as' => 'admin.user_edit','uses' => 'Admin\UserController@getItem'))->where('id', '[0-9]+');
Route::post('users/edit/{id?}', array('as' => 'admin.user_edit','uses' => 'Admin\UserController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'users/delete', array('as' => 'admin.user_delete','uses' => 'Admin\UserController@delete'));

Route::get('trash', array('as' => 'admin.trash','uses' => '\App\Http\Controllers\Admin\TrashController@listView'));
Route::get('trash/edit/{id?}', array('as' => 'admin.trash_edit','uses' => 'Admin\TrashController@getItem'))->where('id', '[0-9]+');
Route::post('trash/edit/{id?}', array('as' => 'admin.trash_edit','uses' => 'Admin\TrashController@getItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'trash/delete', array('as' => 'admin.trash_delete','uses' => 'Admin\TrashController@delete'));
Route::match(['GET','POST'],'trash/restore', array('as' => 'admin.trash_delete','uses' => 'Admin\TrashController@restore'));

Route::get('order', array('as' => 'admin.order','uses' => '\App\Http\Controllers\Admin\OrderController@listView'));
Route::get('order/edit/{id?}', array('as' => 'admin.order_edit','uses' => 'Admin\OrderController@getItem'))->where('id', '[0-9]+');
Route::post('order/edit/{id?}', array('as' => 'admin.order_edit','uses' => 'Admin\OrderController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'order/delete', array('as' => 'admin.order_delete','uses' => 'Admin\OrderController@delete'));
Route::get('order/printer/{id?}', array('as' => 'admin.orderPrint','uses' => 'Admin\OrderController@orderPrint'))->where('id', '[0-9]+');
Route::post('order/changeDictrictGetPriceShip', array('as' => 'admin.changeDictrictGetPriceShip','uses' => 'Admin\OrderController@changeDictrictGetPriceShip'));
Route::post('order/btnChangeOrderStatusFast', array('as' => 'admin.btnChangeOrderStatusFast','uses' => 'Admin\OrderController@btnChangeOrderStatusFast'));
Route::get('orderSearch', array('as' => 'admin.orderSearch','uses' => 'Admin\OrderController@searchListView'));
Route::post('order/btnConfirmOrderPrint', array('as' => 'admin.btnConfirmOrderPrint','uses' => 'Admin\OrderController@btnConfirmOrderPrint'));
Route::post('order/btnDestroyConfirmOrderPrint', array('as' => 'admin.btnDestroyConfirmOrderPrint','uses' => 'Admin\OrderController@btnDestroyConfirmOrderPrint'));
Route::match(['GET','POST'],'order/btnOrdersPrint', array('as' => 'admin.btnOrdersPrint','uses' => 'Admin\OrderController@btnOrdersPrint'));

Route::match(['GET','POST'],'order/ajaxcomment', array('as' => 'admin.orderAjaxAddComment','uses' => '\App\Http\Controllers\Admin\CommentOrderController@orderAjaxAddComment'));
Route::match(['GET','POST'],'order/ajaxdeletecomment', array('as' => 'admin.orderAjaxDeleteComment','uses' => 'Admin\CommentOrderController@orderAjaxDeleteComment'));
Route::match(['GET','POST'],'order/popupajaxgetallcommentorder', array('as' => 'admin.popupAjaxGetAllCommentOrder','uses' => 'Admin\CommentOrderController@popupAjaxGetAllCommentOrder'));
Route::match(['GET','POST'],'order/popupajaxgetoneorder', array('as' => 'admin.popupAjaxGetOneOrder','uses' => 'Admin\CommentOrderController@popupAjaxGetOneOrder'));

Route::get('code-ads', array('as' => 'admin.code_ads','uses' => '\App\Http\Controllers\Admin\CodeAdsController@listView'));
Route::get('code-ads/edit/{id?}', array('as' => 'admin.code_ads_edit','uses' => 'Admin\CodeAdsController@getItem'))->where('id', '[0-9]+');
Route::post('code-ads/edit/{id?}', array('as' => 'admin.code_ads_edit','uses' => 'Admin\CodeAdsController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'code-ads/delete', array('as' => 'admin.code_ads_delete','uses' => 'Admin\CodeAdsController@delete'));

Route::get('comment-product', array('as' => 'admin.commentProduct','uses' => '\App\Http\Controllers\Admin\CommentProductController@listView'));
Route::get('comment-product/edit/{id?}', array('as' => 'admin.commentProduct_edit','uses' => 'Admin\CommentProductController@getItem'))->where('id', '[0-9]+');
Route::post('comment-product/edit/{id?}', array('as' => 'admin.commentProduct_edit','uses' => 'Admin\CommentProductController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'comment-product/delete', array('as' => 'admin.commentProduct_delete','uses' => 'Admin\CommentProductController@delete'));
Route::match(['GET','POST'],'comment-product/ajaxcomment', array('as' => 'admin.commentProductAjaxAddComment','uses' => '\App\Http\Controllers\Admin\CommentProductController@commentAjaxAddComment'));
Route::match(['GET','POST'],'comment-product/ajaxdeletecomment', array('as' => 'admin.commentProductAjaxDeleteComment','uses' => 'Admin\CommentProductController@commentAjaxDeleteComment'));

//Money
Route::get('money', array('as' => 'admin.money','uses' => '\App\Http\Controllers\Admin\MoneyController@listView'));
Route::get('money/edit/{id?}', array('as' => 'admin.money_edit','uses' => 'Admin\MoneyController@getItem'))->where('id', '[0-9]+');
Route::post('money/edit/{id?}', array('as' => 'admin.money_edit','uses' => 'Admin\MoneyController@postItem'))->where('id', '[0-9]+');
Route::match(['GET','POST'],'money/delete', array('as' => 'admin.money_delete','uses' => 'Admin\MoneyController@delete'));

//Excel
Route::match(['GET','POST'],'createFileExcelOrder', array('as' => 'admin.createFileExcelOrder','uses' => '\App\Http\Controllers\Admin\ExcelOrderController@createFileExcelOrder'));