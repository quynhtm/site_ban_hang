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
				<li class="active">Quản lý comment sản phẩm</li>
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
										<input type="text" class="form-control input-sm" name="comment_username" @if(isset($search['comment_username']) && $search['comment_username'] !='')value="{{$search['comment_username']}}"@endif>
									</div>
								</div>
								<div class="col-lg-2">
									<label class="control-label">Điện thoại</label>
									<div>
										<input type="text" class="form-control input-sm" name="comment_phone" @if(isset($search['comment_phone']) && $search['comment_phone'] !='')value="{{$search['comment_phone']}}"@endif>
									</div>
								</div>
								<div class="col-lg-2">
									<label class="control-label">Email</label>
									<div>
										<input type="text" class="form-control input-sm" name="comment_mail" @if(isset($search['comment_mail']) && $search['comment_mail'] !='')value="{{$search['comment_mail']}}"@endif>
									</div>
								</div>
								<div class="col-lg-2">
									<label class="control-label">PID</label>
									<div>
										<input type="text" class="form-control input-sm" name="comment_pid" @if(isset($search['comment_pid']) && $search['comment_pid'] !='')value="{{$search['comment_pid']}}"@endif>
									</div>
								</div>
								<div class="col-lg-2">
									<label class="control-label">Trạng thái</label>
									<div>
										<select name="comment_status" class="form-control input-sm">
											{!! $optionStatus !!}
										</select>
									</div>
								</div>
							</div>
							<div class="panel-footer text-right">
								<a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/comment-product/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
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
						<form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/comment-product/delete" class="formListItem" name="txtForm">
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
									<th width="10%">Điện thoại</th>
									<th width="5%">Email</th>
									<th width="3%">PID</th>
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
												<input class="ace checkItem" name="checkItem[]" value="{{$item['comment_id']}}" type="checkbox">
												<span class="lbl"></span>
											</label>
										</td>
										<td>{{$item['comment_phone']}}</td>
										<td>{{$item['comment_mail']}}</td>
										<td>{{$item['comment_pid']}}</td>
										<td>{{date('d/m/Y', $item['comment_created'])}}</td>
										<td>
											@if($item['comment_status'] == '1')
												<i class="fa fa-check fa-admin green"></i>
											@else
												<i class="fa fa-remove fa-admin red"></i>
											@endif
										</td>
										<td>
											<a href="{{FuncLib::getBaseUrl()}}admin/comment-product/edit/{{$item['comment_id']}}" title="Cập nhật">
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