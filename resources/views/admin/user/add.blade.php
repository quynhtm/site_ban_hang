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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif người dùng</li>
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
                                <label class="control-label">Họ và tên</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="user_full_name" value="@if(isset($data['user_full_name'])){{$data['user_full_name']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Tên đăng nhập<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="user_name" @if(isset($data['user_name']))value="{{$data['user_name']}}" @if($id > 0) readonly="readonly" @endif @endif>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Mật khẩu</label>
                                <div class="controls">
                                    <input type="password" class="form-control input-sm" name="user_pass">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Nhập lại mật khẩu</label>
                                <div class="controls">
                                    <input type="password" class="form-control input-sm" name="re_user_pass">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Số điện thoại</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="user_phone" value="@if(isset($data['user_phone'])){{$data['user_phone']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="user_mail" value="@if(isset($data['user_mail'])){{$data['user_mail']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <div class="controls">
                                    <select class="form-control input-sm" name="user_status">
                                        {!! $optionStatus !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Thuộc nhóm quyền</label>
                                <div class="controls">
                                    <?php $data['user_rid'] = (isset($data['user_rid'] ) && $data['user_rid'] != '') ? explode(',', $data['user_rid']) : array(); ?>
                                    @foreach($arrRoleUser as $val)
                                        <div class="form-group col-lg-2 col-md-3 col-sm-4 item-permission">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="user_rid[]" id="user_rid_{{$val->role_id}}" value="{{$val->role_id}}" @if(isset($data['user_rid']) && in_array($val->role_id, $data['user_rid'])) checked="checked" @endif > {{$val->role_title}}
                                                </label>
                                            </div>
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
