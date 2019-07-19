@extends('site.layout.html')
@section('header')
	@include('site.block.header')
@stop
@section('footer')
	@include('site.block.footer')
@stop
@section('content')
<div class="container">
	<div class="page-access">{{$txt404}}</div>
</div>
@stop