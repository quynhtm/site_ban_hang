<?php
use App\Library\PHPDev\FuncLib;
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
					<li class="active">Quản lý sản phẩm</li>
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
											<input type="text" class="form-control input-sm" name="product_title" @if(isset($search['product_title']) && $search['product_title'] !='')value="{{$search['product_title']}}"@endif>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Danh mục</label>
										<div>
											<select name="product_catid" class="form-control input-sm">
												{!! $strCategoryProduct !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">NCC</label>
										<div>
											<select name="product_supplier" class="form-control input-sm">
												{!! $optionSupplier !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Nổi bật</label>
										<div>
											<select name="product_focus" class="form-control input-sm">
												{!! $optionFocus !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Tình trạng hàng</label>
										<div>
											<select name="product_sale" class="form-control input-sm">
												{!! $optionSale !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Khuyến mãi</label>
										<div>
											<select name="product_khuyenmai" class="form-control input-sm">
												{!! $optionKhuyenMai !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Giảm giá</label>
										<div>
											<select name="product_giamgia" class="form-control input-sm">
												{!! $optionGiamGia !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Mới</label>
										<div>
											<select name="product_moi" class="form-control input-sm">
												{!! $optionMoi !!}
											</select>
										</div>
									</div>
									<div class="form-group col-lg-2">
										<label class="control-label">Trạng thái</label>
										<div>
											<select name="product_status" class="form-control input-sm">
												{!! $optionStatus !!}
											</select>
										</div>
									</div>
								</div>
								<div class="panel-footer text-right">
									<span class="chage-product-sale pull-left">
										<select class="form-control input-sm" name="product_sale" id="product_sale">
											{!! $optionSale !!}
										</select>
									</span>
									<a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/product/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
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
							<form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/product/delete" class="formListItem" name="txtForm">
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
										<th width="15%">Tiêu đề</th>
										<th width="10%">Tổng SL</th>
										<th width="8%">Mã</th>
										<th width="10%">Mã nhà sản xuất</th>
										<th width="10%">Giá bán</th>
										<th width="10%">TT Khác</th>
										<th width="5%">Nổi bật</th>
										<th width="8%">Khuyến mãi</th>
										<th width="6%">Giảm giá</th>
										<th width="3%">Mới</th>
										<th width="6%">Tình trạng</th>
										<th width="6%">Trạng thái</th>
										<th width="5%">Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach($data as $k=>$item)
										<tr class="@if($item['product_focus'] == CGlobal::status_show) bg_d6f6f6 @endif @if($item['product_sale'] == CGlobal::product_sale_off) bg_f5f5f5 @endif">
											<td>{{$k+1}}</td>
											<td>
												<label class="pos-rel">
													<input class="ace checkItem" name="checkItem[]" value="{{$item['product_id']}}" type="checkbox">
													<span class="lbl"></span>
												</label>
											</td>
											<td>
												<a target="_blank" href="{{FuncLib::buildLinkDetailProduct($item->product_id, $item->product_title)}}">{{$item['product_title']}}</a><br/>
												<i>Danh mục:</i> @if(isset($arrCate[$item['product_catid']])) {{ $arrCate[$item['product_catid']] }} @endif
											</td>
											<td>
												<?php $size_no = ($item['product_size_no'] != '') ? unserialize($item['product_size_no']) : array(); $total = 0; ?>
												@foreach($size_no as $k=>$v)
													{{isset($v['size']) ? 'Cỡ: '.$v['size'] : '' }} -- {{isset($v['no']) ? 'SL: '.$v['no'] : '' }}<br/>
													<?php
													if(isset($v['no'])){
														$total += (int)$v['no'];
													}
													?>
												@endforeach
												<b>Tổng: {{$total}}</b>
											</td>
											<td>{{$item['product_code']}}</td>
											<td>{{$item['product_code_factory']}}</td>
											<td>
												@if(isset($item['product_price_input']) && $item['product_price_input'] > 0)
													Giá nhập: <span class="green">{{FuncLib::numberFormat($item['product_price_input'])}}</span><sup>đ</sup><br/>
												@endif
												@if(isset($item['product_price_normal']) && $item['product_price_normal'] > 0)
													Thị Trường: <span class="red">{{FuncLib::numberFormat($item['product_price_normal'])}}</span><sup>đ</sup><br/>
												@endif
												@if(isset($item['product_price']) && $item['product_price'] > 0)
													Giá bán: <span>{{FuncLib::numberFormat($item['product_price'])}}</span><sup>đ</sup>
												@endif
											</td>
											<td>
												<span>Nhà CC:</span> @if(isset($arrSupplier[$item['product_supplier']])) {{ $arrSupplier[$item['product_supplier']] }} @endif<br/>
												Ngày Tạo: {{date('d/m/Y', $item['product_created'])}}
											</td>
											<td>
												@if($item['product_focus'] == CGlobal::status_show)
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['product_khuyenmai'] == CGlobal::status_show)
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['product_giamgia'] == CGlobal::status_show)
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if($item['product_moi'] == CGlobal::status_show)
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												@if(isset($arrSale[$item['product_sale']])) {{ $arrSale[$item['product_sale']] }} @endif
											</td>
											<td>
												@if($item['product_status'] == CGlobal::status_show)
													<i class="fa fa-check fa-admin green"></i>
												@else
													<i class="fa fa-remove fa-admin red"></i>
												@endif
											</td>
											<td>
												<a href="{{FuncLib::getBaseUrl()}}admin/product/edit/{{$item['product_id']}}" title="Cập nhật">
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
