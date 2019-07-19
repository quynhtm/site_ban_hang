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
        <div class="line-solid">
            <h1><a title="{{stripslashes($data->statics_title)}}" href="{{FuncLib::buildLinkDetailStatic($data['statics_id'], $data['statics_title'])}}">{{stripslashes($data['statics_title'])}}</a></h1>
        </div>
        <div class="line-view">
            {!!stripslashes($data['statics_content'])!!}
        </div>
    </div>
@stop
