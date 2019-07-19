<?php
/*
* @Created by: Quynhtm
* @Author	 : quynhtm
* @Date 	 : 06/2016
* @Version	 : 1.0
*/

//Router Page 403 - Page 404
Route::get('page-403', array('as' => 'page.403','uses' => 'BaseSiteController@page403'));
Route::get('page-404', array('as' => 'page.404','uses' => 'BaseSiteController@page404'));

//Router Login - Logout
Route::get('user/{url?}', array('as' => 'login','uses' => 'Login\LoginController@getLogin'));
Route::post('user/{url?}', array('as' => 'login','uses' => 'Login\LoginController@postLogin'));
Route::get('logout', array('as' => 'logout','uses' => 'Login\LoginController@logout'));

//Router Frontend
Route::group(array('prefix' => '/', 'before' => ''), function () {
	require __DIR__.'/site.php';
});

//Router Backend
Route::group(array('prefix' => 'admin', 'before' => ''), function(){
	require __DIR__.'/admin.php';
});

//Router Ajax
Route::group(array('prefix' => 'ajax', 'before' => ''), function () {
	Route::post('upload', array('as' => 'ajax.upload','uses' => '\App\Http\Controllers\Admin\AjaxUploadController@upload'));
});

//Post product to website partner
Route::match(['GET','POST'],'postProductToShopCuaTui', array('as' => 'cronjob.postProductToShopCuaTui','uses' => '\App\Http\Controllers\Cronjob\PostProductToPartnerController@postProductToShopCuaTui'));
Route::match(['GET','POST'],'postProductToRaoVat30s', array('as' => 'cronjob.postProductToRaoVat30s','uses' => '\App\Http\Controllers\Cronjob\PostProductToPartnerController@postProductToRaoVat30s'));