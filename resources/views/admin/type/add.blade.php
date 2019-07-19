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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif kiểu chuyên mục</li>
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
                                <label class="control-label">Tiêu đề<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="type_title" value="@if(isset($data['type_title'])){{$data['type_title']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Từ khóa<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="type_keyword" value="@if(isset($data['type_keyword'])){{$data['type_keyword']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Mô tả</label>
                                <textarea class="form-control input-sm" name="type_intro">@if(isset($data['type_intro'])){{$data['type_intro']}}@endif</textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Thứ tự</label>
                                <input type="text" class="form-control input-sm" name="type_order_no" value="@if(isset($data['type_order_no'])){{$data['type_order_no']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <select class="form-control input-sm" name="type_status">
                                    {!!$optionStatus!!}
                                </select>
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
@stop

