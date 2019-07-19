<?php use App\Library\PHPDev\CGlobal; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
	{!! CGlobal::$extraMeta !!}
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="{{Config::get('config.BASE_URL')}}assets/frontend/img/favicon.ico" type="image/vnd.microsoft.icon">
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/libs/bootstrap/css/bootstrap.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/focus/css/reset.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/frontend/css/site.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/frontend/css/media.css')}}" />
    <script src="{{URL::asset('assets/focus/js/jquery.2.1.1.min.js')}}"></script>
    <script src="{{URL::asset('assets/libs/bootstrap/js/bootstrap.min.js')}}"></script>
	{!! CGlobal::$extraHeaderCSS !!}
	{!! CGlobal::$extraHeaderJS !!}
    <script type="text/javascript">var BASE_URL = "{{Config::get('config.BASE_URL')}}";</script>
	@if(CGlobal::is_dev == 0)
	 <meta name="google-site-verification" content="bloShB_k23-n1cFodDB1j5mmorz0m3wWcigRD-vcs6s" />
	 <!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109179778-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-109179778-1');
	</script>

	<script rel="nofollow" type="application/ld+json">
	{
	  "@context": "http://schema.org/",
	  "@type": "Review",
	  "itemReviewed": {
		"@type": "Thing",
		"name": "Super Book"
	  },
	  "author": {
		"@type": "Person",
		"name": "Google"
	  },
	  "reviewRating": {
		"@type": "Rating",
		"ratingValue": "9",
		"bestRating": "10"
	  },
	  "publisher": {
		"@type": "Organization",
		"name": "Washington Times"
	  }
	}
	</script>
	@endif
</head>
<body>
<div id="wrapper">
    @yield('header')
    @yield('content')
    @yield('footer')
</div>
@yield('popupHide')
{!! CGlobal::$extraFooterCSS !!}
{!! CGlobal::$extraFooterJS !!}
</body>
</html>
