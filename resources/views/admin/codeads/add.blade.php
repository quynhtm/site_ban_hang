<?php use App\Library\PHPDev\FuncLib; ?>
@extends('admin.layout.html')
@section('header')
    @include('admin.block.header')
@stop
@section('left')
    @include('admin.block.left')
@stop
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{URL::route('admin.dashboard')}}">Trang chủ</a>
                </li>
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif nhà cung cấp</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="col-xs-12">
                <div class="row">
                    @if($error != '')
                        <div class="alert-admin alert alert-danger">{!! $error !!}</div>
                    @endif
                    <form class="form-horizontal paddingTop30" name="txtForm" action="" method="post" enctype="multipart/form-data">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Mã<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="code_ads_title" value="@if(isset($data['code_ads_title'])){{$data['code_ads_title']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Tổng tiền chạy<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="code_ads_price" value="@if(isset($data['code_ads_price'])){{$data['code_ads_price']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ghi chú</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" name="code_ads_content">@if(isset($data['code_ads_content'])){{stripslashes($data['code_ads_content'])}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <div class="controls">
                                    <select class="form-control input-sm" name="code_ads_status">
                                        {!! $optionStatus !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! csrf_field() !!}
                                <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                                <button type="submit" name="txtSubmit" id="buttonSubmit" class="btn btn-primary">Lưu lại</button>
                                <button type="reset" class="btn">Bỏ qua</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	CKEDITOR.replace('code_ads_content');
</script>
@stop