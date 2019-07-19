<?php
use App\Library\PHPDev\CGlobal;
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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif comment sản phẩm</li>
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
                                <input type="text" class="form-control input-sm" name="comment_username" value="@if(isset($data['comment_username'])){{$data['comment_username']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Điện thoại</label>
                                <input type="text" class="form-control input-sm" name="comment_phone" value="@if(isset($data['comment_phone'])){{$data['comment_phone']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input type="text" class="form-control input-sm" name="comment_mail" value="@if(isset($data['comment_mail'])){{$data['comment_mail']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">PID</label>
                                <input type="text" class="form-control input-sm" name="comment_pid" value="@if(isset($data['comment_pid'])){{$data['comment_pid']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Nội dung</label>
                                <textarea class="form-control input-sm" name="comment_content">@if(isset($data['comment_content'])){{stripslashes($data['comment_content'])}}@endif</textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <select class="form-control input-sm" name="comment_status">
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
                    <div class="clearfix"></div>
                    <div class="col-sm-12">
                        @if($id > 0)
                        <div class="post-note box-order">
                            <div class="comment-order">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="control-group">
                                        <label class="control-label">Trả lời comment:</label>
                                        <div class="controls">
                                            <div class="list-comment">
                                                <ul>
                                                    @if($comment != '')
                                                        {!! $comment !!}
                                                    @endif
                                                </ul>
                                            </div>
                                            <textarea id="frmcomment" class="form-control input-sm" name="order_comment"></textarea>
                                            <div class="txtclicknoteProduct">Click để trả lời</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
CKEDITOR.replace('comment_content');
</script>
@stop