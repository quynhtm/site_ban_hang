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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="line-solid">
                        <div class="line-bg">
                            <div class="line-step"></div>
                            <table>
                                <tr>
                                    <td><span>1</span>Giỏ hàng</td>
                                    <td><span class="active">2</span>Thông tin và thanh toán</td>
                                    <td><span>3</span>Xác nhận thành công</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <form id="txtFormPaymentCart" method="POST" class="txtFormPaymentCart" name="txtFormPaymentCarts">
                            <div class="">
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="title-order">1.Thông tin khách hàng</div>
                                    <div class="form-group">
                                        <label>Họ và tên<span>(*)</span></label>
                                        <input type="text" id="txtName" class="form-control" name="txtName" value="@if(isset($member['member_full_name'])){{$member['member_full_name']}}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Số điện thoại<span>(*)</span></label>
                                        <input type="text" id="txtMobile" class="form-control" name="txtMobile" value="@if(isset($member['member_phone'])){{$member['member_phone']}}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input type="text" id="txtEmail" class="form-control" name="txtEmail" value="@if(isset($member['member_mail'])){{$member['member_mail']}}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Địa chỉ<span>(*)</span></label>
                                        <input type="text" id="txtAddress" class="form-control" name="txtAddress" value="@if(isset($member['member_address'])){{$member['member_address']}}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label>Ghi chú</label>
                                        <textarea  id="txtMessage" class="form-control" rows="3" name="txtMessage"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4s col-sm-12 bd-center">
                                    <div class="title-order">2.Phương thức thanh toán</div>
                                    <div class="txt-thantoan">
                                        {!! $strThanhToan !!}
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    @if(sizeof($dataItem) != 0)
                                       <div class="content-post-cart">
                                           <div class="title-order">3.Chi tiết đơn hàng</div>

                                           <table class="list-pay-order" style="width: 100%; margin-bottom: 10px">
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
                                                           <tr class="bsd">
                                                               <td>
                                                                   @if($item->product_image != '')
                                                                   <img alt="{{stripcslashes($item->product_title)}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_PRODUCT, $item->product_id, $item->product_image, 200, 200, '', true, true)}}" width="63">
                                                                   @endif
                                                               </td>
                                                               <td class="st">
                                                                   <table>
                                                                       <tbody>
                                                                       <tr>
                                                                           <td class="lb lb1" style="vertical-align:top;">SP:</td>
                                                                           <td>
                                                                               <b>
                                                                                   <a title="{{stripcslashes($item->product_title)}}" href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}" target="_blank">{{stripcslashes($item->product_title)}}</a>
                                                                               </b>
                                                                           </td>
                                                                           <td></td>
                                                                       </tr>
                                                                       <tr>
                                                                           <td class="lb">Cỡ:</td>
                                                                           <td>{{$size}}</td>
                                                                           <td></td>
                                                                       </tr>
                                                                       <tr>
                                                                           <td class="lb">SL:</td>
                                                                           <td>{{$num}}</td>
                                                                           <td></td>
                                                                       </tr>
                                                                       <tr>
                                                                           <td class="lb">Giá:</td>
                                                                           <td><b>@if($item->product_price > 0) <b>{{FuncLib::numberFormat((int)$item->product_price * $num)}}</b><sup>đ</sup> @else Liên hệ @endif</b></td>
                                                                           <td></td>
                                                                       </tr>
                                                                       </tbody>
                                                                   </table>
                                                               </td>
                                                           </tr>
                                                           <tr>
                                                               <td>&nbsp;</td>
                                                               <td></td>
                                                           </tr>
                                                           @endforeach
                                                       @endif
                                                   @endforeach
                                               @endforeach
                                               <tr>
                                                   <td class="nowrap" width="10%"><b>Thành tiền:</b></td>
                                                   <td class="text-right">
                                                       <b>{{FuncLib::numberFormat((int)$total)}}</b><sup>đ</sup>
                                                        <span class="txtNoteOrder">(Chưa bao gồm phí vận chuyển)</span>
                                                   </td>
                                               </tr>
                                               </tbody>
                                           </table>
                                       </div>
                                    @endif
                                </div>
                            </div>
                            <div class="list-btn-control">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="line-bt"></div>
                                    {!! csrf_field() !!}
                                    <a id="backSell" class="btndefault btn-primary" href="{{URL::route('site.pageOrderCart')}}">Quay lại</a>
                                    <button type="submit" id="submitPaymentOrder" class="btndefault btn-primary act">Hoàn thành</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop