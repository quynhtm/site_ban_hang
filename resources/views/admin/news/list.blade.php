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
				<li class="active">Quản lý bài viết</li>
			</ul>
		</div>
		<div class="page-content">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-info">
						<form id="frmSearch" method="GET" action="" class="frmSearch" name="frmSearch">
							<div class="panel-body">
								<div class="form-group col-sm-2">
									<label class="control-label">Từ khóa</label>
									<div>
										<input type="text" class="form-control input-sm" name="news_title" @if(isset($search['news_title']) && $search['news_title'] !='')value="{{$search['news_title']}}"@endif>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Danh mục</label>
									<div>
										<select name="news_catid" class="form-control input-sm">
											{!! $strCategoryProduct !!}}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Hot</label>
									<div>
										<select name="news_hot" class="form-control input-sm">
											{!! $optionHot !!}}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Nổi bật</label>
									<div>
										<select name="news_focus" class="form-control input-sm">
											{!! $optionFocus !!}}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Trạng thái</label>
									<div>
										<select name="news_status" class="form-control input-sm">
											{!! $optionStatus !!}}
										</select>
									</div>
								</div>
							</div>
							<div class="panel-footer text-right">
								<a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/news/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
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
						<form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/news/delete" class="formListItem" name="txtForm">
							<table class="table table-bordered table-hover">
								<thead class="thin-border-bottom">
								<tr>
									<th width="2%">STT</th>
									<th width="1%">
										<label class="pos-rel">
											<input id="checkAll" class="ace" type="checkbox">
											<span class="lbl"></span>
										</label>
									</th>
									<th width="20%">Tiêu đề</th>
									<th width="10%">Danh mục</th>
									<th width="5%">Hot</th>
									<th width="5%">Nổi bật</th>
									<th width="5%">Thứ tự</th>
									<th width="5%">Ngày tạo</th>
									<th width="5%">Trạng thái</th>
									<th width="5%">Action</th>
								</tr>
								</thead>
								<tbody>
								@foreach($data as $k=>$item)
									<tr>
										<td>{{$k+1}}</td>
										<td>
											<label class="pos-rel">
												<input class="ace checkItem" name="checkItem[]" value="{{$item['news_id']}}" type="checkbox">
												<span class="lbl"></span>
											</label>
										</td>
										<td>{{$item['news_title']}}</td>
										<td>
											@if(isset($arrCate[$item['news_catid']])) {{ $arrCate[$item['news_catid']] }} @endif
										</td>
										<td>
											@if($item['news_hot'] == '1')
												<i class="fa fa-check fa-admin green"></i>
											@else
												<i class="fa fa-remove fa-admin red"></i>
											@endif
										</td>
										<td>
											@if($item['news_focus'] == '1')
												<i class="fa fa-check fa-admin green"></i>
											@else
												<i class="fa fa-remove fa-admin red"></i>
											@endif
										</td>
										<td>{{$item['news_order_no']}}</td>
										<td>{{date('d/m/Y', $item['news_created'])}}</td>
										<td>
											@if($item['news_status'] == '1')
												<i class="fa fa-check fa-admin green"></i>
											@else
												<i class="fa fa-remove fa-admin red"></i>
											@endif
										</td>
										<td>
											<a href="{{FuncLib::getBaseUrl()}}admin/news/edit/{{$item['news_id']}}" title="Cập nhật">
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