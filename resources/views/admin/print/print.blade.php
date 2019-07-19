<?php
    use App\Library\PHPDev\FuncLib;
?>
<div class="text-center mgt50">{{$data->order_code_post}}</div>
<div class="line-print-box">
	<div class="line-print-txt-user-send">{!!stripslashes($info_user_send)!!}</div>
	@if($url_barcode != '')
	<div class="line-print-barcode">
		<img src="{{$url_barcode}}" />
	</div>
	@endif
	<div class="line-print-txt-user-get">
		<div class="line-print-txt-total">
			<?php $total = (int)$data->order_total_lst;?>
			<strong>COD:</strong> <b>{{FuncLib::numberFormat($total)}}<sup>đ</sup></b>
		</div>
		<strong>NGƯỜI NHẬN:</strong> {{$data->order_title}}<br>
		<strong>Điện thoại:</strong> {{$data->order_phone}}<br>
		<strong>Địa chỉ:</strong> {{$data->order_address}}({{$ward.' - '.$dictrict.' - '.$provice}})<br>
		<strong>Sản phẩm:</strong>
		@if(isset($data->order_list_code))
			<?php 
			$order_list_code = array();
			$order_list_code = @unserialize($data->order_list_code);
			?>
			@if(is_array($order_list_code) && !empty($order_list_code))
			    @foreach($order_list_code as $code)
			    	<b>@if(isset($code['pcode'])){{stripslashes($code['pcode'])}}@endif</b>(@if(isset($code['pnum'])){{(int)$code['pnum']}}@endif)
			    @endforeach
	        @endif
	        <br>
		@endif
		@if($data->order_gift != '')
			<em>Quà tặng:</em>
			<div>{{stripslashes($data->order_gift)}}</div>
		@endif
	</div>
</div>
<div class="line-print-txt-button" onclick="window.print()" >In Phiếu</div>
<style>
	.text-center{
		text-align: center;
	}
	.mgt50{
		margin: 250px auto 0;
		width: 310px;
	}
	.line-print-box {
	    border: 1px dotted #000;
	    margin: 2px auto 0;
	    padding: 5px;
	    width: 310px;
	}
	.line-print-txt-user-send{
		clear: both;
	    display: inline-block;
	    width: 100%;
		font-size: 14px;
	}
	.line-print-txt-total{
		clear: both;
	    display: inline-block;
	    width: 100%;
		text-align:left;
		margin:5px 0px;
	}
	.line-print-txt-user-get{
		clear: both;
	    display: inline-block;
	    width: 100%;
		font-size: 14px;
		margin-top: 5px;
	}
	.line-print-txt-button {
	    margin: 2px 0;
	    text-align: center;
		clear: both;
	    display: inline-block;
	    width: 100%;
		cursor: pointer
	}
	.line-print-txt-user-send p{
	    margin: 2px 0;
	}
	.line-print-barcode{
		margin-top:10px;
		text-align: center;
	}
</style>