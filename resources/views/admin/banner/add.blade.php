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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif banner quảng cáo</li>
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
                                <input type="text" class="form-control input-sm" name="banner_title" value="@if(isset($data['banner_title'])){{$data['banner_title']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Tiêu đề hiển thị<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="banner_title_show" value="@if(isset($data['banner_title_show'])){{$data['banner_title_show']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Liên kết</label>
                                <input type="text" class="form-control input-sm" name="banner_link" value="@if(isset($data['banner_link'])){{$data['banner_link']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Mô tả</label>
                                <textarea class="form-control input-sm" name="banner_intro">@if(isset($data['banner_intro'])){{$data['banner_intro']}}@endif</textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Thời gian chạy quảng cáo</label>
                                <select class="form-control input-sm" name="banner_is_run_time">
                                    {!! $optionRunTime !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ngày bắt đầu</label>
                                <input type="text" class="form-control input-sm date" name="banner_start_time" value="@if(isset($data['banner_start_time']) && $data['banner_start_time'] > 0 ){{date('d-m-Y',$data['banner_start_time'])}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ngày kết thúc</label>
                                <input type="text" class="form-control input-sm date" name="banner_end_time" value="@if(isset($data['banner_end_time']) && $data['banner_end_time'] > 0){{date('d-m-Y',$data['banner_end_time'])}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Thứ tự</label>
                                <input type="text" class="form-control input-sm" name="banner_order_no" value="@if(isset($data['banner_order_no'])){{$data['banner_order_no']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Vị trí</label>
                                <select class="form-control input-sm" name="banner_type">
                                    {!! $optionType !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Target</label>
                                <select class="form-control input-sm" name="banner_is_target">
                                    {!! $optionTarget !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Rel</label>
                                <select class="form-control input-sm" name="banner_is_rel">
                                    {!! $optionRel !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <select class="form-control input-sm" name="banner_status">
                                    {!! $optionStatus !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ảnh banner<span>*</span></label>
                                <div class="controls">
                                    <a href="javascript:;"class="btn btn-primary link-button" onclick="UploadAdmin.uploadBannerAdvanced(1);">Upload ảnh quảng cáo</a>
                                    <div id="sys_show_image_banner">
                                        @if(isset($data['banner_image']) && $data['banner_image'] !='')
                                            <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_BANNER, $data['banner_id'], $data['banner_image'], 300, 0, '', true, true, false)}}"/>
                                        @endif
                                    </div>
                                    <input name="img" type="hidden" id="img" @if(isset($data['banner_image']))value="{{$data['banner_image']}}"@endif>
                                    <input name="img_old" type="hidden" id="img_old" @if(isset($data['banner_image']))value="{{$data['banner_image']}}"@endif>
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

<script type="text/javascript">
    jQuery(document).ready(function($){
        jQuery('.date').datetimepicker({
            timepicker:false,
            format:'d-m-Y',
            lang:'vi'
        });
    });
</script>
@stop
