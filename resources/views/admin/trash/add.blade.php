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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif thùng rác</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="col-xs-12">
                <div class="row">
                    @if($error != '')
                        <div class="alert-admin alert alert-danger">{!! $error !!}</div>
                    @endif
                    <form id="formListItem" class="form-horizontal paddingTop30 trash" name="txtForm" action="{{FuncLib::getBaseUrl()}}admin/trash/delete" method="post" enctype="multipart/form-data">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-5 pull-left text-left">
                                        <input class="checkItem trash" name="checkItem[]" value="{{$id}}" type="checkbox">
                                    </div>
                                    <div class="col-md-5 pull-right text-right">
                                        <a href="javascript:void(0)" title="Khôi phục" id="restoreMoreItem" class="fa fa-reply fa-admin green"></a>
                                        <a href="javascript:void(0)" title="Xóa item" id="deleteMoreItem" class="fa fa-trash fa-admin red"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Lớp: <b>@if(isset($data['trash_class'])){{$data['trash_class']}}@endif</b></label>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Thư mục: <b>@if(isset($data['trash_folder'])){{$data['trash_folder']}}@endif</b></label>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Nội dung:</label>
                                <div class="controls content-trash">
                                    <?php
                                    $trash_content = array();
                                    if(isset($data['trash_content'])){
                                        $trash_content = unserialize($data['trash_content']);
                                        foreach($arrField as $field){
                                            if(isset($trash_content[$field])){
                                                echo '<div class="line"><b>'.$field.':</b> '.$trash_content[$field].'</div>';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! csrf_field() !!}
                                <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
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