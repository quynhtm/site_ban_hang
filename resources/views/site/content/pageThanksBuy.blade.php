<?php
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
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="row">
			<div class="view-content-static">
				<div class="title-thanks-order">ĐƠN HÀNG CỦA BẠN ĐÃ ĐĂNG KÝ THÀNH CÔNG</div>
				<div class="content-thanks-order">
					{{CGlobal::nameSite}} sẽ liên hệ với bạn để xác thực thông tin. Sản phẩm sẽ được chuyển tới bạn sau 2-4 ngày. Cảm ơn quý khách!
				</div>
				<div class="note-thanks-order">
					<a href="{{URL::route('site.pageContact')}}">Góp ý để nâng cao dịch vụ của chúng tôi</a>
				</div>
			</div>
		</div>
	</div>
</div>
@stop