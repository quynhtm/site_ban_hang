<?php
use App\Library\PHPDev\FuncLib;
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
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="main-box">
				<div class="line-solid">
					<h1 class="text-center"><a title="Lịch sử mua hàng" href="{{URL::route('member.pageHistoryOrder')}}">Lịch sử mua hàng</a></h1>
				</div>
				<div class="line-view">
						<table width="100%" class="list-shop-cart-item">
					      <tbody>
					         <tr class="first">
					            <th width="2%">STT</th>
					            <th width="20%">Họ tên</th>
					            <th width="8%">Điện thoại</th>
					            <th width="8%" style="text-align:center">Số lượng</th>
					            <th width="8%" style="text-align:center">Tổng tiền</th>
					            <th width="8%">Ngày mua</th>
					            <th width="10%" style="text-align:center">Trạng thái</th>
					            <th width="8%" style="text-align:center">Thao tác</th>
					         </tr>
					         @if(!empty($data))							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         								         							         								         									         										         										         										         <tr>
					         @foreach($data as $k=>$item)
					         <tr>  
					            <td>{{$k+1}}</td>
					            <td>{{$item['order_title']}}</td>
					            <td>{{$item['order_phone']}}</td>
					            <td style="text-align:center">{{$item['order_num']}}</td>
					            <td style="text-align:center">
						            @if($item['order_total_lst'] > 0)
										{{FuncLib::numberFormat((int)$item['order_total_lst'])}}<sup>đ</sup>
									@else
										Liên hệ
									@endif
								</td>
					            <td>{{date('d/m/Y', $item['order_created'])}}</td>
					            <td style="text-align:center">@if(isset($arrStatus[$item['order_status']])){{$arrStatus[$item['order_status']]}}@endif</td>
					         	<td style="text-align:center">
					         		<a class="viewOrder" title="Xem lại" href="javascript:void(0)" data="{{$item['order_id']}}">
										<i class="fa fa-eye fa-20px"></i>
									</a>
					         	</td>
					         </tr>
					         @endforeach
					         @endif
					      </tbody>
					   </table>
					   @if(isset($paging) && $paging != '')
					   <div class="list-item-page">
							<div class="showListPage">{{$paging}}</div>
						</div>
						@endif
					</div>
			</div>
		</div>
	</div>	
</div>
{!! csrf_field() !!}
<div id="sys-popup-view-order" class="content-popup-show content-popup-show-order" style="display:none">
	<div class="modal-dialog modal-dialog-order">
        <div class="modal-content">
            <div class="modal-title-classic">Thông tin đơn hàng <span class="btn-close" data-dismiss="modal">X</span></div>
            <div class="content-popup-body">
               <div class="content-item"></div>
            </div>
        </div>
    </div>
</div>
@stop