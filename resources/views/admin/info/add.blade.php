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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif thông tin chung</li>
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
                                    <input type="text" class="form-control input-sm" name="info_title" value="@if(isset($data['info_title'])){{$data['info_title']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Từ khóa</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="info_keyword" @if(isset($data['info_keyword']))value="{{$data['info_keyword']}}" @if($id > 0) readonly="readonly" @endif @endif>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Mô tả</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" name="info_intro">@if(isset($data['info_intro'])){{stripslashes($data['info_intro'])}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Nội dung</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" name="info_content">@if(isset($data['info_content'])){{stripslashes($data['info_content'])}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Thứ tự</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="info_order_no" value="@if(isset($data['info_order_no'])){{$data['info_order_no']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <div class="controls">
                                    <select class="form-control input-sm" name="info_status">
                                        {!! $optionStatus !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ảnh</label>
                                <div class="controls">
                                    <a href="javascript:;"class="btn btn-primary link-button" onclick="UploadAdmin.uploadBannerAdvanced(5);">Upload ảnh</a>
                                    <div id="sys_show_image_banner">
                                        @if(isset($data['info_img']) && $data['info_img'] !='')
                                            <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_INFO, $data['info_id'], $data['info_img'], 300, 0, '', true, true)}}"/>
                                        @endif
                                    </div>
                                    <input name="img" type="hidden" id="img" @if(isset($data['info_img']))value="{{$data['info_img']}}"@endif>
                                    <input name="img_old" type="hidden" id="img_old" @if(isset($data['info_img']))value="{{$data['info_img']}}"@endif>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta title</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="meta_title" value="@if(isset($data['meta_title'])){{$data['meta_title']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta keyword</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" name="meta_keywords">@if(isset($data['meta_keywords'])){{$data['meta_keywords']}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta description</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" name="meta_description">@if(isset($data['meta_description'])){{$data['meta_description']}}@endif</textarea>
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
<!--Popup Upload Img-->
<div class="modal fade" id="sys_PopupUploadImgOtherPro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload ảnh</h4>
            </div>
            <div class="modal-body">
                <form name="uploadImage" method="post" action="#" enctype="multipart/form-data">
                    <div class="form_group">
                        <div id="sys_show_button_upload">
                            <div id="sys_mulitplefileuploader" class="btn btn-primary">Upload ảnh</div>
                        </div>
                        <div id="status"></div>

                        <div class="clearfix"></div>
                        <div class="clearfix" style='margin: 5px 10px; width:100%;'>
                            <div id="div_image"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Popup Upload Img-->
<script>
    CKEDITOR.replace('info_content');
</script>
@stop
