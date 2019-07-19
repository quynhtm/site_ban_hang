<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\Utility;
?>
@extends('site.layout.html')
@section('header')
	@include('site.block.header')
@stop
@section('footer')
    @include('site.block.footer')
@stop
@if(sizeof($member) == 0)
@section('popupHide')
    @include('site.member.popupHide')
@stop
@endif
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-8">
                <div class="line postViewHead">
                    @if(sizeof($data) != 0)
                    <h2><span class="block">&nbsp;</span><a title="{{$data->news_cat_name}}" href="{{FuncLib::buildLinkCategory($data->news_catid, $data->news_cat_name)}}">{{$data->news_cat_name}}</a></h2>
                    <h1 class="title-view">{{$data->news_title}}</h1>
                    <div class="line">
                        @if(CGlobal::is_dev == 0)
                            <div class="social-share-view">
                                <div class="div-share">
                                    <div id="fb-root"></div>
                                    <script>(function(d, s, id) {
                                            var js, fjs = d.getElementsByTagName(s)[0];
                                            if (d.getElementById(id)) return;
                                            js = d.createElement(s); js.id = id;
                                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
                                            fjs.parentNode.insertBefore(js, fjs);
                                        }(document, 'script', 'facebook-jssdk'));</script>
                                    <div class="fb-like" data-href="{{FuncLib::buildLinkDetailNews($data->news_id, $data->news_title)}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                                </div>
                                <div class="div-share google">
                                    <script src="https://apis.google.com/js/platform.js" async defer></script>
                                    <g:plus action="share" annotation="bubble"></g:plus>
                                    <div class="g-plusone" data-size="medium"></div>
                                </div>
                            </div>
                        @endif
                        <div class="date">
                            {{date('h:i', $data->news_created)}} Ngày {{date('d/m/Y', $data->news_created)}}
                        </div>
                    </div>
                @endif
                </div>
                <div class="line">
                    @if($data->news_intro != '')
                    <div class="line intro-view">{!! stripslashes($data->news_intro) !!}</div>
                    @endif
                    @if($data->news_content != '')
                    <div class="line content-view">{!! stripslashes($data->news_content) !!}</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="line postViewHead">
                    @if(sizeof($dataProductHot) > 0)
                    <div class="title-other-product">Sản phẩm #</div>
                    <div class="content-other-product">
                        <ul>
                            @foreach($dataProductHot as $item)
                            <li class="product">
                                <a href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" title="{{stripcslashes($item->product_title)}}" class="linkprod">
                                    @if($item->product_image != '')
                                    <img class="thumbNormal" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true, false)}}">
                                    @endif
                                    @if($item->product_image != '')
                                        <img class="thumbHover" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true, false)}}">
                                    @endif
                                </a>
                                <div class="grid">
                                    <div class="line">
                                        <h3 class="inline">
                                            <a href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" title="{{stripcslashes($item->product_title)}}">{{stripcslashes($item->product_title)}}</a>
                                        </h3>
                                        @if($item->product_moi > 0)
                                        <span class="icon icon-new"></span>
                                        @endif
                                    </div>
                                    <div class="line">
                                        @if($item->product_price > 0)
                                        <b class="product-price-km">{{FuncLib::numberFormat($item->product_price)}} VNĐ</b>
                                        @else
                                        <b class="product-price-km">Liên hệ</b>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-2">

            </div>
        </div>
        @if(sizeof($dataSame) != 0)
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-10">
                <div class="title-same">CÁC TIN KHÁC</div>
                <div class="line mgt15">
                    <div class="row">
                        @foreach($dataSame as $item)
                        <div class="col-lg-6 col-md-6 col-sm-12 item-post-news">
                            <a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
                            @if($item->news_image != '')
                                <img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
                            @endif
                            </a>
                            <h3><a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{$item->news_title}}</a></h3>
                            <p class="time">{{date('d/m/Y', $item->news_created)}}</p>
                            <p>{!!Utility::cutWord($item->news_intro, 30, '...')!!}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@stop