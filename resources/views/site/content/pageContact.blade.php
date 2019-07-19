<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\Loader;
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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="line-solid">
                    <h1><a title="Liên hệ" href="{{URL::route('site.pageContact')}}">Liên hệ</a></h1>
                </div>
                <div class="main-box">
                    <div class="c-box-intro">
                          @if(sizeof($arrJoin) > 0)
                              {!! stripcslashes($arrJoin->info_content) !!}
                          @endif
                      </div>
                      @if($messages != '')
                          {!! $messages !!}
                      @endif
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="tile-box-head">{{CGlobal::nameSite}} Được Hỗ Trợ Quý Khách</div>
                              <form id="formSendContact" method="POST" class="formSendContact" name="txtForm">
                                  <div class="form-group">
                                      <label class="control-label">Họ và tên<span>(*)</span></label>
                                      <input id="txtName" name="txtName" class="form-control" type="text">
                                  </div>
                                  <div class="form-group">
                                      <label class="control-label">Số điện thoại<span>(*)</span></label>
                                      <input id="txtMobile" name="txtMobile" class="form-control" type="text">
                                  </div>
                                  <div class="form-group">
                                      <label class="control-label">Địa chỉ<span>(*)</span></label>
                                      <input id="txtAddress" name="txtAddress" class="form-control" type="text">
                                  </div>
                                  <div class="form-group">
                                      <label class="control-label">Nội dung<span>(*)</span></label>
                                      <textarea id="txtMessage" name="txtMessage" class="form-control" rows="3"></textarea>
                                  </div>
                                  {!! csrf_field() !!}
                                  <button type="submit" id="submitContact" class="btn btn-primary">Gửi đi</button>
                              </form>
                        </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="tile-box-head">Địa chỉ và sơ đồ đường đi</div>
                        @if(sizeof($arrContact) > 0)
                              <div class="address-contact">
                                  {!! stripcslashes($arrContact->info_content) !!}
                              </div>
                              <div class="address-contact">
                                  @if($arrContact->info_img == '')
                                      <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $arrContact->info_id, $arrContact->info_img, 800, 0, '', true, true)}}"/>
                                  @else
                                      <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.exp&sensor=false&libraries=geometry&key=AIzaSyA-WIHdfuGBuWUCglOx2-yUB9oU_0498PU&language=vi"></script>
                                      {!! Loader::loadJS('libs/map/maps.js', CGlobal::$postEnd) !!}
                                      <div id="mapCanvas" style="width:500px; height:250px; overflow: hidden; border-radius:3px;"></div>
                                  @endif
                              </div>
                        @endif
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
@stop