<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\CGlobal;
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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="line-solid">
                        <div class="line-bg">
                            <div class="line-step"></div>
                            <table>
                                <tr>
                                    <td><span class="active">1</span>Giỏ hàng</td>
                                    <td><span>2</span>Thông tin và thanh toán</td>
                                    <td><span>3</span>Xác nhận thành công</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <form id="txtFormShopCart" method="POST" class="txtFormShopCart" name="txtFormShopCart">
                        <div class="grid-shop-cart">
                            <table class="list-shop-cart-item" width="100%">
                                <thead>
                                    <tr class="first">
                                        <th>Sản phẩm</th>
                                        <th>Mô tả</th>
                                        <th class="text-center">Kích thước</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Giá / 1 sản phẩm</th>
                                        <th class="text-center">Thành tiền</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0;?>
                                    @foreach($dataItem as $key => $item)
                                        @foreach($dataCart as $k=>$v)
                                            @if($item->product_id == $k)
                                                @foreach($v as $size=>$num)
                                                    <?php
                                                    if($item->product_price > 0){
                                                        $total += (int)$item->product_price * $num;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            @if($item->product_image != '')
                                                                <img class="thumbNormalCart" alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 600, 600, '', true, true)}}">
                                                            @endif
                                                        </td>
                                                        <td><a title="{{stripcslashes($item->product_title)}}" href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" target="_blank">{{stripcslashes($item->product_title)}}</a></td>
                                                        <td class="text-center">{{$size}}</td>
                                                        <td class="text-center"><input class="num-item-in-one-product" value="{{$num}}" name="listCart[{{$item->product_id}}][{{$size}}]" type="text"></td>
                                                        <td class="text-center">
                                                            @if($item->product_price > 0)
                                                                {{FuncLib::numberFormat((int)$item->product_price)}}<sup>đ</sup>
                                                            @else
                                                                Liên hệ
                                                            @endif
                                                        </td>
                                                        <td class="text-center">@if($item->product_price > 0) {{FuncLib::numberFormat((int)$item->product_price * $num)}} @else Liên hệ @endif<sup>đ</sup></td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" class="delOneItemCart" data="{{$item->product_id}}" data-size="{{$size}}">Xóa</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endforeach
                                    <tr class="last">
                                        <td colspan="6"><b>Tổng số tiền: {{FuncLib::numberFormat((int)$total)}}</b><sup>đ</sup></td>
                                        <td colspan="1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                    <div class="list-btn-control">
                        <a id="backSell" class="btndefault btn-primary" href="{{URL::route('site.index')}}">Tiếp tục mua hàng</a>
                        @if(sizeof($dataItem) != 0)
                            <a id="updateCart" class="btndefault btn-primary" href="javascript:void(0)">Cập nhật đơn hàng</a>
                            <a id="payCart" class="btndefault btn-primary act" href="{{URL::route('site.pageSendCart')}}">Thanh toán</a>
                        @endif
                        <div class="page-order-cart">{{$paging}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop