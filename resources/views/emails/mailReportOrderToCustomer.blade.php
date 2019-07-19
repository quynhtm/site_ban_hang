<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div style="border: 1px solid #166ead;margin: 0 auto;min-height: 100%;width: 98%; display:inline-block; background:#166ead">
   			<div style="height: 50px;margin: 0 auto;width: 100%; margin-bottom: 2px; display: inline-block; color: #fff;">
  				 <div style="float: left;margin: 0 auto;width: 25%;">
			        <div style="padding-top: 10px;padding-left: 15px;">
			           <a href="{{URL::route('site.index')}}"><img style="margin-top:5px; max-height: 30px; height:30px" id="logo" src="{{URL::route('site.index')}}/assets/frontend/img/logo-mail.png" /></a>
				    </div>
				 </div>
    			<div style="display:inline-block;float:right;color:#fff; line-height:50px;padding-right:20px; font-style: italic;">{{CGlobal::phoneSupport}}</div>
	    	</div>
	    	<div style="background: #fff;margin: 0 auto;min-height: 200px;padding: 3% 2%;width: 88%;">
				<b>Thông tin đơn hàng từ website {{ucwords(CGlobal::domain)}}</b><br/><br/>
				<p><b>Thông tin của bạn:</b></p>
				<p>1.Họ tên: <b>{{$data['order_title']}}</b></p>
				<p>2.SĐT: <b>{{$data['order_phone']}}</b></p>
				<p>3.Địa chỉ: <b>{{$data['order_address']}}</b></p>
				<p>4.Ghi chú: <br>{{$data['order_note']}}</p><br/>
				
				<p><b>Thông tin đơn hàng:</b></p>
				<?php 
				$order_content = array();
				if(isset($data['order_list_code']) && $data['order_list_code'] != ''){
					$order_content = unserialize($data['order_list_code']);
				}
				?>
				@if(is_array($order_content) && !empty($order_content))
					<table style="border-collapse: collapse; border-spacing: 0;">
						<tr>
							<th width="1%" style="border: 1px solid #ddd;  padding: 5px;">Mã[ID]</th>
                    		<th width="50%" style="border: 1px solid #ddd;  padding: 5px;">Tên Sản phẩm</th>
                    		<th width="5%" style="border: 1px solid #ddd;  padding: 5px;">Cỡ</th>
                    		<th width="5%" style="border: 1px solid #ddd;  padding: 5px;">SL</th>
                    		<th width="10%" style="border: 1px solid #ddd;  padding: 5px;">Giá</th>
						  </tr>
						@foreach($order_content as $item)
						<tr>
							<td style="border: 1px solid #ddd;  padding: 5px;">{{$item['product_code']}}</td>
                    		<td style="border: 1px solid #ddd;  padding: 5px;"><a href="{{FuncLib::buildLinkDetailProduct($item['product_id'], $item['product_title'])}}" target="_blank">{{$item['product_title']}}</a></td>
                    		<td style="border: 1px solid #ddd;  padding: 5px;text-align: center;">{{$item['product_size']}}</td>
                    		<td style="border: 1px solid #ddd;  padding: 5px;text-align: center;">{{$item['product_num']}}</td>
                    		<td style="border: 1px solid #ddd;  padding: 5px;text-align: center;">{{FuncLib::numberFormat((int)$item['product_price'])}}<sup>đ</sup></td>
						  </tr>
						@endforeach
						<tr>
				            <td colspan="4" style="border: 1px solid #ddd;  padding: 5px;"><b>Tổng số tiền mua hàng:</b><p>(Chưa bao gồm phí vận chuyển)</p></td>
				            <td colspan="1" style="border: 1px solid #ddd;  padding: 5px;"><b>
								{{FuncLib::numberFormat((int)$data['order_total'])}}</b><sup>đ</sup>
							</td>
				        </tr>
					</table>
				@endif
	    	</div>
	  		<div style="max-height: 34px; height:34px; width: 100%;">
		        <div style="margin: 0 auto;width: 100%;">
		            <i style="color:#fff; padding-right: 15px;float: right; padding-top: 10px;">&copy; <a style="text-decoration: none; color:#fff;" href="{{URL::route('site.index')}}">{{ucwords(CGlobal::domain)}}</a> 2015-{{date('Y')}}.</i>
		        </div>
    		</div>
	  	</div>
	</body>
</html>
