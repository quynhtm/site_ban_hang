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
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <div class="line postViewHead">
                        @if(sizeof($dataCate) != 0)
                            <h1><span class="block">&nbsp;</span><a title="{{$dataCate->category_title}}" href="{{FuncLib::buildLinkCategory($dataCate->category_id, $dataCate->category_title)}}">{{$dataCate->category_title}}</a></h1>
                        @endif
                    </div>
                    <div class="row">
                       @if(count($data) >= 6)
                       <div class="line">
                           <div class="col-lg-7 col-md-7 col-sm-7">
                               <div class="box-slider-post box-main-post">
                                   @foreach($data as $k=>$item)
                                       @if($k == 0)
                                       <div class="item">
                                           <a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
                                               @if($item->news_image != '')
                                                   <img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
                                               @endif
                                               <div class="overx">
                                                   <h3>{{$item->news_title}}</h3>
                                                   <div class="main-intro">{!!Utility::cutWord(stripcslashes($item->news_intro), 30, '...')!!}</div>
                                               </div>
                                           </a>
                                       </div>
                                       @endif
                                   @endforeach
                               </div>
                           </div>
                           <div class="col-lg-5 col-md-5 col-sm-5 box-sub-post">
                               @foreach($data as $k=>$item)
                               @if($k > 0 && $k <= 3)
                               <div class="line item-post-news">
                                   <a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
                                       @if($item->news_image != '')
                                           <img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
                                       @endif
                                   </a>
                                   <h3><a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{$item->news_title}}</a></h3>
                                   <p class="time">{{date('d/m/Y', $item->news_created)}}</p>
                                   <p class="sub-intro">{!!Utility::cutWord($item->news_intro, 30, '...')!!}</p>
                               </div>
                               @endif
                               @endforeach
                           </div>
                       </div>
                       @endif
                       <div class="line mgt15">
                           @if(count($data) <= 5 )
                               @foreach($data as $k=>$item)
                                    @if($k <= 4)
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
                                   @endif
                               @endforeach
                           @else
                               @foreach($data as $k=>$item)
                                   @if($k >= 4)
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
                                   @endif
                               @endforeach
                           @endif
                       </div>
                       <div class="show-box-paging">{!! $paging !!}</div>
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
                                                    <img class="thumbNormal" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true)}}">
                                                @endif
                                                @if($item->product_image != '')
                                                    <img class="thumbHover" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true)}}">
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
            </div>
        </div>
    </div>
@stop