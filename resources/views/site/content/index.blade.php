<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\Utility;
use App\Library\PHPDev\ThumbImg;
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
        @if(sizeof($dataBanner) > 0)
        <div class="line-post line-banner">
            <div class="_sliders">
                <?php $rands = array('cube', 'cubeRandom', 'block', 'cubeStop', 'showBars', 'horizontal', 'fadeFour', 'paralell', 'blind', 'directionTop', 'directionBottom', 'directionRight'); ?>
                <div class="skitter skitter-large with-dots">
                    <ul>
                        @foreach($dataBanner as $k=>$item)
                        <?php $rand_item = $rands[array_rand($rands, 1)]; ?>
                        <li>
                            <a {{$item['target']}} {{$item['rel']}} href="@if($item['banner_link'] != '') {{$item['banner_link']}} @else javascript:void(0) @endif" title="{{$item['banner_title_show']}}">
                                <img class="{{$rand_item}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_BANNER, $item['banner_id'], $item['banner_image'], 960, 300, '', true, true, false)}}" alt="{{$item['banner_title_show']}}" />
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <script>
                    jQuery(document).ready(function(){
                        SITE.skitterLarge();
                    });
                </script>
            </div>
        </div>
        @endif
        @if(sizeof($dataBannerDuoiSlider) > 0)
        <div class="line-post">
            <div class="row">
                @foreach($dataBannerDuoiSlider as $k=>$item)
                <div class="col-lg-4 col-md-4 col-sm-12 item-trans">
                   <div class="line-center trans">
                       <a {{$item['target']}} {{$item['rel']}} href="@if($item['banner_link'] != '') {{$item['[banner_link']}} @else javascript:void(0) @endif" title="{{$item['banner_title_show']}}">
                           <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_BANNER, $item['banner_id'], $item['banner_image'], 500, 300, '', true, true, false)}}" alt="{{$item['banner_title_show']}}" />
                       </a>
                   </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if(sizeof($dataBannerTrans) > 0)
        <div class="line-post mgt10">
            <div class="row">
                @foreach($dataBannerTrans as $k=>$item)
                <div class="col-lg-4 col-md-4 col-sm-12 item-trans">
                    <div class="line-center trans">
                        <a {{$item['target']}} {{$item['rel']}} href="@if($item['banner_link'] != '') {{$item['[banner_link']}} @else javascript:void(0) @endif" title="{{$item['banner_title_show']}}">
                            <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_BANNER, $item['banner_id'], $item['banner_image'], 800, 450, '', true, true, false)}}" alt="{{$item['banner_title_show']}}" />
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if(sizeof($dataProduct) > 0)
        <div class="line-post index">
            <div class="line-dotted"><b>HÀNG MỚI</b></div>
            <div class="line-content-prod">
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
                        </p>
                    </div>
                </div>
                @endforeach
                <div class="show-box-paging">{!! $paging !!}</div>
            </div>
        </div>
        @endif
        @if(sizeof($arrNews) > 0)
        <div class="line-post">
            <a class="title" title="Tin tức" href="{{FuncLib::buildLinkCategory(CGlobal::catIDNews, 'Tin tức')}}">TIN TỨC</a>
        </div>
        <div class="box-slider-post">
            <div class="owl-carousel owl-theme">
                @foreach($arrNews as $item)
                <div class="item">
                    <a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
                        @if($item->news_image != '')
                            <img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
                        @endif
                        <h4>{{$item->news_title}}</h4>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        <script>
            $(document).ready(function() {
                var owl = $('.owl-carousel');
                owl.owlCarousel({
                    loop: false,
                    nav: true,
                    margin: 20,
                    responsive: {
                        0:{items: 1},
                        600:{items: 2},
                        960:{items: 3},
                        1200:{items: 3}
                    }
                });
                owl.on('mousewheel', '.owl-stage', function(e) {
                    if (e.deltaY > 0) {owl.trigger('next.owl');}else{owl.trigger('prev.owl');}
                    e.preventDefault();
                });
            })
        </script>
        @endif
    </div>
@stop
