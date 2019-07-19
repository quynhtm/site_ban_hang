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
				<li class="active">Quản lý danh mục</li>
			</ul>
		</div>
		<div class="page-content">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-info">
						<form id="frmSearch" method="GET" action="" class="frmSearch" name="frmSearch">
							<div class="panel-body">
								<div class="form-group col-sm-2">
									<label class="control-label">Tên danh mục</label>
									<div>
										<input  type="text" class="form-control input-sm" name="category_title" @if(isset($search['category_title']) && $search['category_title'] !='')value="{{$search['category_title']}}"@endif>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Kiểu danh mục</label>
									<div>
										<select class="form-control input-sm" name="category_type_id">
											{!! $optionType !!}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Menu ngang</label>
									<div>
										<select class="form-control input-sm" name="category_menu">
											{!! $optionMenu !!}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Menu trái</label>
									<div>
										<select class="form-control input-sm" name="category_menu_left">
											{!! $optionMenuLeft !!}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Content trang chủ</label>
									<div><select class="form-control input-sm" name="category_menu_content">
											{!! $optionContent !!}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Menu chân trang</label>
									<div><select class="form-control input-sm" name="category_menu_footer">
											{!! $optionFooter !!}
										</select>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Trạng thái</label>
									<div><select class="form-control input-sm" name="category_status">
											{!! $optionStatus !!}
										</select>
									</div>
								</div>
							</div>
							<div class="panel-footer text-right">
								<a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/category/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
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
						<form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/category/delete" class="formListItem" name="txtForm">
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
									<th width="10%">Tiêu đề</th>
									<th width="8%">Kiểu danh mục</th>
									<th width="8%">Menu ngang</th>
									<th width="5%">Menu trái</th>
									<th width="5%">Trang chủ</th>
									<th width="6%">Chân trang</th>
									<th width="5%">Thứ tự</th>
									<th width="5%">Trạng thái</th>
									<th width="3%">Ngày tạo</th>
									<th width="3%">Action</th>
								</tr>
								</thead>
								<tbody>
								@foreach($data as $k=>$item)
									@if(!empty($item['parent']))
										<tr>
											<td><b>{{$k}}</b></td>
											<td>
												<label class="pos-rel">
													<input class="ace checkItem" name="checkItem[]" value="{{$item['parent']['category_id']}}" type="checkbox">
													<span class="lbl"></span>
												</label>
											</td>
											<td>{{$item['parent']['category_title']}}</td>
											<td>
												<?php $category_type_id = $item['parent']['category_type_id'] ?>
												@if(isset($arrType[$category_type_id])) {{ $arrType[$category_type_id] }} @endif
											</td>
											<td>
												@if($item['parent']['category_menu'] == '1')
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['parent']['category_menu_left'] == '1')
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['parent']['category_menu_content'] == '1')
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['parent']['category_menu_footer'] == '1')
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>{{$item['parent']['category_order_no']}}</td>
											<td>
												@if($item['parent']['category_status'] == '1')
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>{{date('d/m/Y', $item['parent']['category_created'])}}</td>
											<td>
												<a href="{{FuncLib::getBaseUrl()}}admin/category/edit/{{$item['parent']['category_id']}}" title="Cập nhật">
													<i class="fa fa-edit fa-admin"></i>
												</a>
											</td>
										</tr>
									@endif
									@if(!empty($item['sub']))
										@foreach($item['sub'] as $key=>$sub)
											<tr>
												<td>{{$sub['category_id']}}</td>
												<td>
													<label class="pos-rel">
														<input class="ace checkItem" name="checkItem[]" value="{{$sub['category_id']}}" type="checkbox">
														<span class="lbl"></span>
													</label>
												</td>
												<td>---{{$sub['category_title']}}</td>
												<td>
													<?php $sub_category_type_id = $sub['category_type_id'] ?>
													@if(isset($arrType[$sub_category_type_id])) {{ $arrType[$sub_category_type_id] }} @endif
												</td>
												<td>
													@if($sub['category_menu'] == '1')
														<i class="fa fa-check fa-admin green"></i>
													@else
														<i class="fa fa-remove fa-admin red"></i>
													@endif
												</td>
												<td>
													@if($sub['category_menu_left'] == '1')
														<i class="fa fa-check fa-admin green"></i>
													@else
														<i class="fa fa-remove fa-admin red"></i>
													@endif
												</td>
												<td>
													@if($sub['category_menu_content'] == '1')
														<i class="fa fa-check fa-admin green"></i>
													@else
														<i class="fa fa-remove fa-admin red"></i>
													@endif
												</td>
												<td>
													@if($sub['category_menu_footer'] == '1')
														<i class="fa fa-check fa-admin green"></i>
													@else
														<i class="fa fa-remove fa-admin red"></i>
													@endif
												</td>
												<td>{{$sub['category_order_no']}}</td>
												<td>
													@if($sub['category_status'] == '1')
														<i class="fa fa-check fa-admin green"></i>
													@else
														<i class="fa fa-remove fa-admin red"></i>
													@endif
												</td>
												<td>{{date('d/m/Y', $sub['category_created'])}}</td>
												<td>
													<a href="{{FuncLib::getBaseUrl()}}admin/category/edit/{{$sub['category_id']}}" title="Cập nhật">
														<i class="fa fa-edit fa-admin"></i>
													</a>
												</td>
											</tr>
										@endforeach
									@endif
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