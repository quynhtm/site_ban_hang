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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif thành viên</li>
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
                                <label class="control-label">Email đăng nhập<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="member_mail" @if(isset($data['member_mail']))value="{{$data['member_mail']}}" @if($id > 0) readonly="readonly" @endif @endif>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Mật khẩu</label>
                                <input type="password" class="form-control input-sm" name="member_pass">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Nhập lại mật khẩu</label>
                                <input type="password" class="form-control input-sm" name="re_member_pass">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Tên hiển thị<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="member_full_name" value="@if(isset($data['member_full_name'])){{$data['member_full_name']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Số điện thoại</label>
                                <input type="text" class="form-control input-sm" name="member_phone" value="@if(isset($data['member_phone'])){{$data['member_phone']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Địa chỉ</label>
                                <input type="text" class="form-control input-sm" name="member_address" value="@if(isset($data['member_address'])){{$data['member_address']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <select class="form-control input-sm" name="member_status">
                                    {!! $optionStatus !!}
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

