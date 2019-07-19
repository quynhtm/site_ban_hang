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
            <div class="main-box" id="pageProduct">
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <div class="row">
                        @if(sizeof($dataBannerTrans) > 0)
                            <div class="line-post mgt15">
                                @foreach($dataBannerTrans as $k=>$slider)
                                    <?php
                                    if($slider->banner_is_rel == 0){
                                        $rel = 'rel="nofollow"';
                                    }else{
                                        $rel = '';
                                    }
                                    if($slider->banner_is_target == 0){
                                        $target = 'target="_blank"';
                                    }else{
                                        $target = '';
                                    }

                                    $banner_is_run_time = 1;
                                    if($slider->banner_is_run_time == CGlobal::status_hide){
                                        $banner_is_run_time = 1;
                                    }else{
                                        $banner_start_time = $slider->banner_start_time;
                                        $banner_end_time = $slider->banner_end_time;
                                        $date_current = time();

                                        if($banner_start_time > 0 && $banner_end_time > 0 && $banner_start_time <= $banner_end_time){
                                            if($banner_start_time <= $date_current && $date_current <= $banner_end_time){
                                                $banner_is_run_time = 1;
                                            }
                                        }else{
                                            $banner_is_run_time = 0;
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-4 col-md-4 col-sm-12 item-trans">
                                        <div class="line-center trans">
                                            <a {{$target}} {{$rel}} href="@if($slider->banner_link != '') {{$slider->banner_link}} @else javascript:void(0) @endif" title="{{$slider->banner_title_show}}">
                                                <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_BANNER, $slider->banner_id, $slider->banner_image, 1000, 450, '', true, true, false)}}" alt="{{$slider->banner_title_show}}" />
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="line mgt10">
                            <ul class="breadcrumb ext">
                                <li> <a href="{{URL::route('site.index')}}" title="{{CGlobal::nameSite}}">Trang chủ</a></li>
                                @if(sizeof($data) != 0)
                                    <li><a title="{{$data->product_cat_name}}" href="{{FuncLib::buildLinkCategory($data->product_catid, $data->product_cat_name)}}">{{$data->product_cat_name}}</a></li>
                                @endif
                            </ul>
                        </div>
                        @if(sizeof($data) != 0)
                        <div class="line mgt10">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <?php $product_image_other = ($data->product_image_other != '') ? unserialize($data->product_image_other) : array(); ?>
                                <div class="iThumb">
                                    <div class="view">
                                        @if(!empty($product_image_other))
                                            @foreach($product_image_other as $img)
                                                <div style="margin-bottom:5px">
                                                    <a href="javascript:void(0)" title="{{stripcslashes($data->product_title)}}" data="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $data->product_id, $img, 800, 800, '', true, true)}}">
                                                        <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $data->product_id, $img, 400, 400, '', true, true)}}" title="{{stripcslashes($data->product_title)}}" alt="{{stripcslashes($data->product_title)}}">
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            SITE.ZoomX();
                                            SITE.iThumbClick();
                                            SITE.iThumbSlick();
                                        });
                                    </script>
                                <div class="iMain">
                                    <a class="jqzoom" rel="nofollow" title="{{stripcslashes($data->product_title)}}" href="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $data->product_id, $data->product_image, 800, 800, '', true, true)}}">
                                        @if($data->product_image != '')
                                            <img class="imgShow" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $data->product_id, $data->product_image, 800, 800, '', true, true)}}" alt="{{stripcslashes($data->product_title)}}">
                                        @endif
                                    </a>

                                    @if(CGlobal::is_dev == 0)
                                    <div class="line mgt15">
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
                                                <div class="fb-like" data-href="{{FuncLib::buildLinkDetailProduct($data->product_id, $data->product_title)}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                                            </div>
                                            <div class="div-share google">
                                                <script src="https://apis.google.com/js/platform.js" async defer></script>
                                                <g:plus action="share" annotation="bubble"></g:plus>
                                                <div class="g-plusone" data-size="medium"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="pInfo">
                                    <h1 class="pro-title">{{stripcslashes($data->product_title)}}</h1>
                                    @if($data->product_price_normal > 0)
                                    <b class="product-price-th">Giá: {{FuncLib::numberFormat($data->product_price_normal)}} VNĐ</b>
                                    @endif
                                    @if($data->product_price_normal > 0)
                                    <b class="product-price-km">KM: {{FuncLib::numberFormat($data->product_price)}} VNĐ</b>
                                    @endif
                                    <?php $product_size_no = ($data->product_size_no != '') ? unserialize($data->product_size_no) : array(); ?>
                                    @if(sizeof($product_size_no) > 0)
                                        <div class="line mgt10">
                                            <span class="normal">Size:</span>
                                            <span>
                                                <input type="hidden" name="pid" value="{{$data->product_id}}">
                                                <select name="psize" id="productSize">
                                                    <option value="0">Chọn Size</option>
                                                    @foreach($product_size_no as $size_no)
                                                    <option value="{!! $size_no['size'] !!}">{!! $size_no['size'] !!}</option>
                                                    @endforeach
                                                </select>
                                                <span class="tchoise">(Click để chọn size)</span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="line mgt15">
                                        <button id="submitBuy" class="submitBuy" data-pid="{{$data->product_id}}">Mua hàng ngay</button>
                                        <button id="submitSave" class="submitSave" data-pid="{{$data->product_id}}">Lưu sản phẩm</button>
                                        <input type="hidden" id="productNum" name="productNum" value="1">
                                        {!! csrf_field() !!}
                                    </div>
                                    <div class="line mgt15">
                                        <div class="ttabs">
                                            <div class="tabNormal act" data="t1">Mô tả sản phẩm</div>
                                            <div class="tabNormal" data="t2">Hướng dẫn bảo quản</div>
                                        </div>
                                        <div class="product-tabs">
                                            <div class="ictabs t1 act">
                                                @if($data->product_intro != '')
                                                    {!! stripslashes($data->product_intro) !!}
                                                @endif
                                            </div>
                                            <div class="ictabs t2">
                                                @if($strHuongDan != '')
                                                    {!! $strHuongDan !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if(CGlobal::is_dev == 0)
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="title-same-comment">Mời bạn bình luận, hỏi đáp về: {{stripcslashes($data->product_title)}}</div>
                            <div class="box-comment">
                                <div class="line">
                                    <div class="box-left-cmt">
                                        <div class="icon-img-cmt">
                                            <img src="{{URL::route('site.index')}}/assets/frontend/img/ic-cmt.png" alt="icon cmt">
                                        </div>
                                    </div>
                                    <div class="box-right-cmt">
                                        <div class="line">
                                            <input type="text" class="form-control rqMailPhone" placeholder="Nhập số điện thoại hoặc email để chúng tôi có thể liên lạc với bạn...">
                                        </div>
                                        <div class="line mgt10">
                                            <textarea class="form-control rqContent" placeholder="Nhập nội dung" spellcheck="false"></textarea>
                                        </div>
                                        <div class="line mgt10">
                                            <input type="button" value="Bình luận" id="btnSubmitComment">
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function(){
                                        SITE.ajaxGetCommentInProduct();
                                        SITE.btnSubmitComment();
                                    });
                                </script>
                                <div class="line box-comment-show mgt10" id="box-comment-show"></div>
                            </div>
                        </div>
                    </div>
                    @endif
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
            </div>
        </div>
    </div>
@stop