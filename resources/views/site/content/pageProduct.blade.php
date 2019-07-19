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
            <div class="main-box">
                @if(isset($dataCate) && sizeof($dataCate) > 0 && $dataCate->category_image != '')
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="line postCatImg">
                        <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_CATEGORY, $dataCate->category_id, $dataCate->category_image, 960, 300, '', true, true, false)}}" alt="{{$dataCate->category_title}}" />
                        @if($dataCate->category_intro != '')
                        <div class="desc">
                            {{$dataCate->category_intro}}
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <div class="col-lg-3 col-md-3 col-sm-4">
                    <div class="titleHeadOther postViewHead">Danh mục Sản phẩm</div>
                    <ul class="menuLeft">
                        @if(!empty($arrCategory))
                            @foreach($arrCategory as $cat)
                                @if($cat->category_menu_left == CGlobal::status_show && $cat->category_parent_id == 0)
                                    <?php $i=0; ?>
                                    @foreach($arrCategory as $sub)
                                        @if($sub->category_parent_id == $cat->category_id && $sub->category_menu_left == CGlobal::status_show)
                                            <?php $i++; ?>
                                        @endif
                                    @endforeach
                                    <li @if($cat->category_id == $catid) class="act" @endif>
                                        <a title="{{$cat->category_title}}" href="{{FuncLib::buildLinkCategory($cat->category_id, $cat->category_title)}}">{{$cat->category_title}}</a>
                                        @if($i > 0)
                                            <ul>
                                                @foreach($arrCategory as $sub)
                                                    @if($sub->category_menu_left == CGlobal::status_show && $sub->category_parent_id == $cat->category_id)
                                                        <li @if($sub->category_id == $catid) class="act" @endif><a title="{{$sub->category_title}}" href="{{FuncLib::buildLinkCategory($sub->category_id, $sub->category_title)}}">{{$sub->category_title}}</a></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                            <script type="text/javascript">
                                jQuery(document).ready(function(){
                                    $('.menuLeft > li > ul').hide();
                                    $('.menuLeft > li').each(function(){
                                        if($(this).is('.act')){
                                            $('.menuLeft > li > ul').hide();
                                            $(this).find('ul').show();
                                        }
                                    });
                                    $('.menuLeft > li > ul > li').each(function(){
                                        if($(this).is('.act')){
                                            $('.menuLeft > li > ul').hide();
                                            $(this).parent('ul').show();
                                        }
                                    });
                                });
                            </script>
                        @endif
                    </ul>
                    @if(sizeof($arrNews) > 0)
                    <div class="titleHeadOther postViewHead">Tin tức</div>
                    <div class="sliderLeftPostNews">
                        <ul id="sliderPostNews">
                            @foreach($arrNews as $item)
                            <li class="line item-post-news">
                                <a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
                                    @if($item->news_image != '')
                                        <img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
                                    @endif
                                </a>
                                <a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{stripcslashes($item->news_title)}}</a>
                            </li>
                            @endforeach
                        </ul>
                        <span class="selectSliderPostNews">Xem thêm &gt;&gt;</span>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function() {
                            jQuery('#sliderPostNews').bxSlider({
                                nextText: '',
                                prevText: '',
                                mode: 'vertical',
                                auto: true,
                                pager: true,
                                minSlides:5,
                                maxSlides:5
                            });
                        });
                    </script>
                    @endif
                    @if(CGlobal::is_dev == 0)
                        <div class="line-share-facebook line">
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.10&appId=685975718241032";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));</script>
                            <div class="fb-page" data-href="{{CGlobal::link_social_facebook}}" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="{{CGlobal::link_social_facebook}}" class="fb-xfbml-parse-ignore"><a href="{{CGlobal::link_social_facebook}}">Fanpage</a></blockquote></div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-9 col-md-9 col-sm-8">
                    <div class="line postViewHead headCate">
                        @if(sizeof($dataCate) != 0)
                            <h1>
                                <span class="block">&nbsp;</span><a title="{{$dataCate->category_title}}" href="{{FuncLib::buildLinkCategory($dataCate->category_id, $dataCate->category_title)}}">{{$dataCate->category_title}}</a>
                                <div class="type-view">
                                    <div class="type-view-item type-view-row">
                                        <span class="ico"></span>
                                    </div>
                                    <div class="type-view-item type-view-col active">
                                        <span class="ico"></span>
                                    </div>
                                </div>
                            </h1>
                        @endif
                    </div>
                    <div class="line index">
                        <div class="line-content-prod cate view-grid">
                            @foreach($dataProduct as $item)
                                <div class="product">
                                    <a href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" title="{{stripcslashes($item->product_title)}}" class="linkprod">
                                        @if($item->product_image != '')
                                            <img class="thumbNormal" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true, false)}}">
                                        @endif
                                        @if($item->product_image != '')
                                            <img class="thumbHover" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true, false)}}">
                                        @endif
                                        @if($item->product_giamgia > 0)
                                            <span class="sale"></span>
                                        @endif
                                    </a>
                                    <div class="grid">
                                        <div class="line-center">
                                            <h3 class="inline">
                                                <a href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" title="{{stripcslashes($item->product_title)}}">{{stripcslashes($item->product_title)}}</a>
                                            </h3>
                                            @if($item->product_moi > 0)
                                                <span class="icon icon-new"></span>
                                            @endif
                                        </div>
                                        <p class="line-center">
                                            @if($item->product_price_normal > 0)
                                                <b class="product-price-th">{{FuncLib::numberFormat($item->product_price_normal)}} VNĐ</b>
                                            @endif
                                            @if($item->product_price > 0)
                                                <b class="product-price-km">{{FuncLib::numberFormat($item->product_price)}} VNĐ</b>
                                            @else
                                                <b class="product-price-km">Liên hệ</b>
                                            @endif
                                            <span class="icon icon-add viewFast" title="Click đây để xem nhanh sản phẩm" data-id="{{$item->product_id}}"></span>
                                            @if($item->product_intro != '')
                                            <div class="product-intro-row mgt15">
                                               {!! stripslashes($item->product_intro) !!}
                                            </div>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="show-box-paging">{!! $paging !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop