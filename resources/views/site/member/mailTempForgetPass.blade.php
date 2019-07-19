<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div style=" border: 1px solid #166ead;margin: 0 auto;min-height: 100%;width: 98%; display:inline-block; background:#166ead">
   			<div style="height: 50px;margin: 0 auto;width: 100%; margin-bottom: 2px; display: inline-block; color: #fff;">
  				 <div style="float: left;margin: 0 auto;width: 25%;">
			        <div style="padding-top: 10px;padding-left: 15px;">
			           <a href="{{URL::route('site.index')}}"><img style="margin-top:5px; max-height: 30px; height:30px" id="logo" src="{{Config::get('config.BASE_URL')}}assets/frontend/img/logo-mail.png" /></a>
				    </div>
				 </div>
    			<div style="display:inline-block;float:right;color:#fff; line-height:50px;padding-right:20px; font-style: italic;">{{$data['phone_support']}}</div>
	    	</div>
	    	<div style="background: #fff;margin: 0 auto;min-height: 200px;padding: 3% 2%;width: 88%;">
				<b>Liên kết thay đổi mật khẩu của bạn trên website {{ucwords($data['domain'])}}</b><br/><br/>
				<a href="{{URL::route('member.pageGetForgetPass')}}?k={{$data['key_secret']}}">Nhấn vào liên kết</a><br/><br/>
				Ghi chú: Bạn nhấn vào liên kết để nhận hướng dẫn thay đổi mật khẩu cá nhân trên hệ thống cho lần đăng nhập sau.
	    	</div>
	  		<div style="max-height: 34px; height:34px; width: 100%;">
		        <div style="margin: 0 auto;width: 100%;">
		            <i style="color:#fff; padding-right: 15px;float: right; padding-top: 10px;">&copy; <a style="text-decoration: none; color:#fff;" href="{{URL::route('site.index')}}">{{ucwords($data['domain'])}}</a> 2015-{{date('Y')}}.</i>
		        </div>
    		</div>
	  	</div>
	</body>
</html>
