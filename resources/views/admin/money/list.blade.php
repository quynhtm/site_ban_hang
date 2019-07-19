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
                <li class="active">Quản lý chi tiêu</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-info">
                        <form id="frmSearch" method="GET" action="" class="frmSearch" name="frmSearch">
                            <div class="panel-body">
                                <div class="form-group col-lg-2">
                                    <label class="control-label">Từ khóa</label>
                                    <div>
                                        <input type="text" class="form-control input-sm" name="money_title" @if(isset($search['money_title']) && $search['money_title'] !='')value="{{$search['money_title']}}"@endif>
                                    </div>
                                </div>
                                <div class="form-group col-lg-2">
                                    <label class="control-label">Kiểu xuất nhập</label>
                                    <div>
                                        <select name="money_type" class="form-control input-sm">
                                            {!! $optionType !!}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/money/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
                                <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i class="fa fa-search"></i> Tìm kiếm</button>
                                <a href="javascript:void(0)" title="Xóa item" id="deleteMoreItem" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Xóa</a>
                            </div>
                        </form>
                    </div>
                    @if(isset($messages) && $messages != '')
                        {!! $messages !!}
                    @endif
                    @if(sizeof($data) > 0)
                        @if($total>0)
                            <div class="show-bottom-info">
                                <div class="total-rows">Tổng số: <b>{{$total}}</b></div>
                                <div class="list-item-page">
                                    <div class="showListPage">{!! $paging !!}</div>
                                </div>
                            </div>
                        @endif
                        <br>
                        <form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/money/delete" class="formListItem" name="txtForm">
                            <table class="table table-bordered table-hover">
                                <thead class="thin-border-bottom">
                                <tr>
                                    <th width="2%" class="text-center">STT</th>
                                    <th width="1%">
                                        <label class="pos-rel">
                                            <input id="checkAll" class="ace" type="checkbox">
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                    <th width="20%">Tiêu đề</th>
                                    <th width="10%">Kiểu</th>
                                    <th width="10%" class="text-right">Số tiền</th>
                                    <th width="10%" class="text-right">Tiền còn lại</th>
                                    <th width="15%">Ghi chú</th>
                                    <th width="15%">Thời gian</th>
                                    <th width="5%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $k=>$item)
                                    <tr>
                                        <td class="text-center">{{$k+1}}</td>
                                        <td>
                                            <label class="pos-rel">
                                                <input class="ace checkItem" name="checkItem[]" value="{{$item['money_id']}}" type="checkbox">
                                                <span class="lbl"></span>
                                            </label>
                                        </td>
                                        <td>{{$item['money_title']}}</td>
                                        <td>@if(isset($arrType[$item['money_type']]))
                                                @if($item->money_type == 1)
                                                    <b class="green">{{ $arrType[$item['money_type']] }}</b>
                                                @else
                                                    <b class="red">{{ $arrType[$item['money_type']] }}</b>
                                                @endif
                                            @endif</td>
                                        <td class="text-right">
                                            @if($item['money_price'] > 0)
                                                <b>{{FuncLib::numberFormat($item['money_price'])}}đ</b>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($item['money_total_price'] > 0)
                                                @if($item->money_type == 1)
                                                    <b class="green">{{FuncLib::numberFormat($item['money_total_price'])}}đ</b>
                                                @else
                                                    <b class="red">{{FuncLib::numberFormat($item['money_total_price'])}}đ</b>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{!! $item['money_infor'] !!}</td>
                                        <td>
                                            Tạo: {{date('d/m/Y H:i', $item['money_created'])}} <br/>
                                            @if($item['money_updated'] > 0)
                                                Sửa: {{date('d/m/Y H:i', $item['money_updated'])}}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{FuncLib::getBaseUrl()}}admin/money/edit/{{$item['money_id']}}" title="Cập nhật">
                                                <i class="fa fa-edit fa-admin"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if($total>0)
                                <div class="show-bottom-info">
                                    <div class="total-rows">Tổng số: <b>{{$total}}</b></div>
                                    <div class="list-item-page">
                                        <div class="showListPage">{!! $paging !!}</div>
                                    </div>
                                </div>
                            @endif
                            {!! csrf_field() !!}
                        </form>
                    @else
                        <div class="alert">
                            Không có dữ liệu
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop