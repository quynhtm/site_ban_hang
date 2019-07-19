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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif danh mục</li>
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
                                <label class="control-label">Kiểu danh mục</label>
                                <select class="form-control input-sm" name="category_type_id">
                                    {!! $optionType !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Danh mục cha</label>
                                <select class="form-control input-sm" name="category_parent_id">
                                    {!! $strCategoryProduct !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Tiêu đề<span>*</span></label>
                                <input type="text" class="form-control input-sm" name="category_title" value="@if(isset($data['category_title'])){{$data['category_title']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Mô tả</label>
                                <textarea class="form-control input-sm" name="category_intro">@if(isset($data['category_intro'])){{$data['category_intro']}}@endif</textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Thứ tự</label>
                                <input type="text" class="form-control input-sm" name="category_order_no" value="@if(isset($data['category_order_no'])){{$data['category_order_no']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Hiện menu ngang</label>
                                <select class="form-control input-sm" name="category_menu">
                                    {!! $optionMenu !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Hiện menu dọc</label>
                                <select class="form-control input-sm" name="category_menu_left">
                                    {!! $optionMenuLeft !!}}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Hiện content</label>
                                <select class="form-control input-sm" name="category_menu_content">
                                    {!! $optionContent !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Hiện chân trang</label>
                                <select class="form-control input-sm" name="category_menu_footer">
                                    {!! $optionFooter !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Trạng thái</label>
                                <select class="form-control input-sm" name="category_status">
                                    {!! $optionStatus !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Ảnh danh mục</label>
                                <div class="controls">
                                    <a href="javascript:;"class="btn btn-primary link-button" onclick="UploadAdmin.uploadBannerAdvanced(4);">Upload danh mục</a>
                                    <div id="sys_show_image_banner">
                                        @if(isset($data['category_image']) && $data['category_image'] !='')
                                            <img src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_CATEGORY, $data['category_id'], $data['category_image'], 300, 0, '', true, true)}}"/>
                                        @endif
                                    </div>
                                    <input name="img" type="hidden" id="img" @if(isset($data['category_image']))value="{{$data['category_image']}}"@endif>
                                    <input name="img_old" type="hidden" id="img_old" @if(isset($data['category_image']))value="{{$data['category_image']}}"@endif>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta title</label>
                                <input type="text" class="form-control input-sm" name="meta_title" value="@if(isset($data['meta_title'])){{$data['meta_title']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta keywords</label>
                                <textarea class="form-control input-sm" name="meta_keywords">@if(isset($data['meta_keywords'])){{$data['meta_keywords']}}@endif</textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Meta description</label>
                                <textarea class="form-control input-sm" name="meta_description">@if(isset($data['meta_description'])){{$data['meta_description']}}@endif</textarea>
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
@stop