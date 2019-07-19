<?php
/*
* @Created by: HSS
* @Author    : quynhtm
* @Date      : 08/2016
* @Version   : 1.0
*/
//Index
Route::any('/', array('as' => 'site.index','uses' => '\App\Http\Controllers\Site\IndexController@index'));

//Category Product - News
Route::get('{name}-{id}.html',array('as' => 'site.actionRouter','uses' =>'\App\Http\Controllers\Site\IndexController@actionRouter'))->where('name', '[A-Z0-9a-z_\-]+')->where('id', '[0-9]+');

//Product
Route::get('san-pham.html', array('as' => 'site.pageProductFull','uses' => '\App\Http\Controllers\Site\IndexController@pageProductFull'));
Route::get('tim-kiem.html', array('as' => 'site.pageProductSearch','uses' => '\App\Http\Controllers\Site\IndexController@pageProductSearch'));
Route::get('san-pham/{name}-{id}.html',array('as' => 'site.detailProduct','uses' =>'\App\Http\Controllers\Site\IndexController@detailProduct'))->where('name', '[A-Z0-9a-z_\-]+')->where('id', '[0-9]+');
Route::get('hang-moi-ve.html', array('as' => 'site.pageProductNew','uses' => '\App\Http\Controllers\Site\IndexController@pageProductNew'));
Route::match(['GET','POST'],'ajaxGetCommentInProduct', array('as' => 'site.ajaxGetCommentInProduct','uses' => '\App\Http\Controllers\Site\IndexController@ajaxGetCommentInProduct'));
Route::match(['GET','POST'],'ajaxAddCommentInProduct', array('as' => 'site.ajaxAddCommentInProduct','uses' => '\App\Http\Controllers\Site\IndexController@ajaxAddCommentInProduct'));

//News
Route::get('tin-tuc/{name}-{id}.html',array('as' => 'site.detailNews','uses' =>'\App\Http\Controllers\Site\IndexController@detailNews'))->where('name', '[A-Z0-9a-z_\-]+')->where('id', '[0-9]+');
Route::get('tim-kiem-tin-tuc.html', array('as' => 'site.pageNewsSearch','uses' => '\App\Http\Controllers\Site\IndexController@pageNewsSearch'));

//Statics
Route::match(['GET','POST'],'lien-he.html', array('as' => 'site.pageContact','uses' => '\App\Http\Controllers\Site\StaticController@pageContact'));
Route::get('st/{name}-{id}.html',array('as' => 'site.detailStatics','uses' =>'\App\Http\Controllers\Site\IndexController@detailStatics'))->where('name', '[A-Z0-9a-z_\-]+')->where('id', '[0-9]+');
Route::match(['GET','POST'],'huong-dan-mua-hang.html', array('as' => 'site.pageGuide','uses' => '\App\Http\Controllers\Site\StaticController@pageGuide'));

//Register - Login
Route::match(['GET','POST'],'dang-ky.html', array('as' => 'member.pageRegister','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageRegister'));
Route::match(['GET','POST'],'dang-nhap.html', array('as' => 'member.pageLogin','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageLogin'));
Route::match(['GET','POST'],'thanh-vien-thoat.html', array('as' => 'member.logout','uses' => '\App\Http\Controllers\Login\MemberLoginController@logout'));
Route::match(['GET','POST'],'quen-mat-khau.html', array('as' => 'member.pageForgetPass','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageForgetPass'));
Route::match(['GET','POST'],'quen-mat-khau', array('as' => 'member.pageGetForgetPass','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageGetForgetPass'));
//Login Facebook - Google
Route::match(['GET','POST'], 'facebooklogin', array('as' => 'loginFacebook','uses' => '\App\Http\Controllers\Login\MemberLoginController@loginFacebook'));
Route::match(['GET','POST'], 'googlelogin', array('as' => 'loginGoogle','uses' => '\App\Http\Controllers\Login\MemberLoginController@loginGoogle'));

Route::match(['GET','POST'],'thanh-vien.html', array('as' => 'member.pageMember','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageMember'));
Route::match(['GET','POST'],'thay-doi-thong-tin.html', array('as' => 'member.pageChageInfo','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageChageInfo'));
Route::match(['GET','POST'],'thay-doi-mat-khau.html', array('as' => 'member.pageChagePass','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageChagePass'));
Route::match(['GET','POST'],'lich-su-mua-hang.html', array('as' => 'member.pageHistoryOrder','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageHistoryOrder'));
Route::match(['GET','POST'],'chi-tiet-don-hang.html', array('as' => 'member.pageHistoryViewOrder','uses' => '\App\Http\Controllers\Login\MemberLoginController@pageHistoryViewOrder'));
// Register Get Promotion
Route::match(['GET','POST'],'dang-ky-nhan-khuyen-mai.html', array('as' => 'site.regSubscribe','uses' => '\App\Http\Controllers\Site\StaticController@regSubscribe'));
//Order
Route::match(['GET','POST'], 'them-vao-gio-hang.html', array('as' => 'site.ajaxAddCart','uses' => '\App\Http\Controllers\Site\CartController@ajaxAddCart'));
Route::match(['GET','POST'], 'gio-hang.html', array('as' => 'site.pageOrderCart','uses' => '\App\Http\Controllers\Site\CartController@pageOrderCart'));
Route::match(['GET','POST'], 'xoa-gio-hang.html', array('as' => 'site.deleteAllItemInCart','uses' => '\App\Http\Controllers\Site\CartController@deleteAllItemInCart'));
Route::match(['GET','POST'], 'xoa-mot-san-pham-trong-gio-hang.html', array('as' => 'site.deleteOneItemInCart','uses' => '\App\Http\Controllers\Site\CartController@deleteOneItemInCart'));
Route::match(['GET','POST'], 'gui-don-hang.html', array('as' => 'site.pageSendCart','uses' => '\App\Http\Controllers\Site\CartController@pageSendCart'));
Route::match(['GET','POST'], 'cam-on-mua-hang.html', array('as' => 'site.pageThanksBuy','uses' => '\App\Http\Controllers\Site\CartController@pageThanksBuy'));
