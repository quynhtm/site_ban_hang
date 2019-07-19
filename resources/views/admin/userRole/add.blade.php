<?php
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\ThumbImg;
?>
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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif nhóm quyền</li>
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
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="role_title" value="@if(isset($data['role_title'])){{$data['role_title']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Thứ tự</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="role_order_no" value="@if(isset($data['role_order_no'])){{$data['role_order_no']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <div class="controls">
                                    <select class="form-control input-sm" name="role_status">
                                        {!! $optionStatus !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"><b>Danh sách quyền truy cập</b> <span class="red btnClickAllAction"><i>Click chọn tất cả</i></span></label>
                                <div class="controls">
                                    @foreach($arrPermissionByGroup as $key => $val)
                                        <h4 class="theader">@if($key || $key != ''){{$key}}@else Khac @endif</h4>
                                        <div class="row">
                                            @foreach($val as $k => $v)
                                                <label class="middle col-lg-2 col-md-3 col-sm-4 item-permission">
                                                    <input type="checkbox" name="permission_id[]" value="{{$v['permission_id']}}"
                                                           class="ace item_{{$v['permission_id']}}" @if(isset($data['strPermission'])) @if(in_array($v['permission_id'],$data['strPermission']))
                                                           checked @endif @endif>
                                                    <span class="lbl"> {{$v['permission_title']}}</span>
                                                </label>
                                            @endforeach
                                            <div class="clearfix"></div>
                                        </div>
                                    @endforeach
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
@stop
