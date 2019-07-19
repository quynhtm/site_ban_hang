<?php
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Http\Models\Order;
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
				<li class="active">Quản lý đơn hàng</li>
			</ul>
		</div>
		<div class="page-content">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-info">
						<form id="frmSearch" method="GET" action="" class="frmSearch" name="frmSearch">
							<div class="panel-body">
								<div class="form-group col-lg-2">
									<label class="control-label">SĐT hoặc Mã vận đơn</label>
									<div>
										<input type="text" class="form-control input-sm" name="order_title" @if(isset($search['order_title']) && $search['order_title'] !='')value="{{$search['order_title']}}"@endif>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Ngày gửi</label>
									<div>
										<input type="text" class="form-control input-sm date" name="order_time_send" @if(isset($search['order_time_send']) && $search['order_time_send'] !='')value="{{$search['order_time_send']}}"@endif>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Ngày phát thành công</label>
									<div>
										<input type="text" class="form-control input-sm date" name="order_time_finish" @if(isset($search['order_time_finish']) && $search['order_time_finish'] !='')value="{{$search['order_time_finish']}}"@endif>
									</div>
								</div>
								<div class="form-group col-lg-2">
									<label class="control-label">Trạng thái</label>
									<div>
										<select name="order_status" class="form-control input-sm">
											{!! $optionStatus !!}
										</select>
									</div>
								</div>


							</div>
							<div class="panel-footer text-right">
								<a class="btn btn-danger btn-sm" href="{{FuncLib::getBaseUrl()}}admin/order/edit"><i class="ace-icon fa fa-plus-circle"></i>Thêm mới</a>
								<button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i class="fa fa-search"></i> Tìm kiếm</button>
								@if($user['user_rid'] == CGlobal::rid_admin || $user['user_rid'] == CGlobal::rid_manager)
								<a href="javascript:void(0)" title="Xóa item" id="deleteMoreItem" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Xóa</a>
								@endif
							</div>
						</form>
					</div>
					@if(isset($messages) && $messages != '')
						{!! $messages !!}
					@endif
					<div class="list-status-box">
						<div class="row">
							@foreach($arrStatus as $_k => $_s)
								@if($_k != -1)
									<a @if($_k == $search['order_status']) class="act" @endif href="{{URL::route('admin.order')}}?order_status={{$_k}}" title="{{$_s}}">{{$_s}}<span class="count">({{Order::countOrderStatus($_k, $uid)}})</span></a>
								@endif
							@endforeach
						</div>
					</div>
					<div class="line">
						<i class="cl-ff0000">Ghi chú: Đơn HN màu xanh, đơn đã in màu vàng, trạng thái khác không màu.</i>
					</div>
					<div class="line mgt10">
						<div class="row">
							<span class="menu-tool">
								<div class="col-lg-5">
									<button class="btn btn-primary btn-sm link-color-white" id="btnOrderPrint">In</button>
									<button class="btn btn-primary btn-sm link-color-white" id="btnConfirmOrderPrint">Xác nhận in</button>
								</div>
								<div class="col-lg-4">
									<select name="order_status_change_fast" class="form-control">
										{!! $optionChangeStatusFast !!}
									</select>
								</div>
								<div class="col-lg-3">
									<button class="btn btn-primary btn-sm link-color-white" id="btnChangeOrderStatusFast">Chuyển trang thái</button>
								</div>
							</span>
						</div>
					</div>
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
						<form id="formListItem" method="POST" action="{{FuncLib::getBaseUrl()}}admin/order/delete" class="formListItem order" name="txtForm">
							<table class="table table-bordered">
								<thead class="thin-border-bottom">
								<tr>
									<th width="2%" class="text-center">STT</th>
									<th width="1%">
										<label class="pos-rel">
											<input id="checkAll" class="ace" type="checkbox">
											<span class="lbl"></span>
										</label>
									</th>
									<th width="10%">Tên KH</th>
									<th width="5%">SĐT</th>
									<th width="10%">Địa chỉ</th>
									<th width="10%">Sản phẩm</th>
									<th width="5%">COD</th>
									<th width="5%">Phí VC</th>
									<th width="8%">Mã Đơn hàng</th>
									<th width="10%">Thông tin khác</th>
									<th width="8%">Mã nhân Viên</th>
									<th width="6%">Loại đơn</th>
									<th width="8%">Action</th>
								</tr>
								</thead>
								<tbody>
								@foreach($data as $k=>$item)
									<tr class="@if(in_array($item->order_dictrict_id, array_keys(CGlobal::$arrNoiThanhHN)) && $item->order_status == CGlobal::cho_gui) bg_589fdc @endif
												@if($item->order_confirm_print == CGlobal::status_show) bg_fff64d @endif ">
										<td class="text-center">{{$k+1}}</td>
										<td>
											<label class="pos-rel">
												<input class="ace checkItem" name="checkItem[]" value="{{$item['order_id']}}" type="checkbox">
												<span class="lbl"></span>
											</label>
										</td>
										<td>
											{{$item->order_title}}
											@if($item->order_user_buy > 0)
												<p class="datetxt cl-ff0000">Đơn hàng mua từ web: Kiểm tra lại giá và thông tin</p>
											@endif
										</td>
										<td>{{$item->order_phone}}</td>
										<td>{{$item->order_address}}</td>
										<td>
											<?php $order_list_code = (isset($item->order_list_code) && $item->order_list_code != '') ? @unserialize($item->order_list_code): array(); ?>
											@if(is_array($order_list_code) && sizeof($order_list_code) > 0)
												<ul>
													@foreach($order_list_code as $_item)
														<li class="item-product">
															Mã: @if(isset($_item['pcode'])) {{stripslashes($_item['pcode'])}} @endif
															Size: @if(isset($_item['psize'])) {{stripslashes($_item['psize'])}} @endif
															SL: @if(isset($_item['pnum'])) {{(int)($_item['pnum'])}} @endif
														</li>
													@endforeach
												</ul>
											@endif
										</td>
										<td>{{FuncLib::numberFormat((int)$item->order_total_lst)}}đ</td>
										<td>{{FuncLib::numberFormat((int)$item->order_price_post)}}đ</td>
										<td>
											@if($item->order_code_post != '')
												{{$item->order_code_post}}<br/>
											@endif
											@if(isset($arrPartner[$item->order_partner]))
												{{$arrPartner[$item->order_partner] }}
											@endif
										</td>
										<td>
											<ul class="list-date">
												<li class="datetxt">Ngày tạo: {{date('d/m/Y H:i', $item->order_created)}}</li>
												@if($item->order_time_send > 0)
													<li class="datetxt">Ngày gửi: {{date('d/m/Y H:i', $item->order_time_send)}}</li>
												@endif
												@if($item->order_time_finish > 0)
													<li class="datetxt">Ngày phát thành công: {{date('d/m/y H:i', $item->order_time_finish)}}</li>
												@endif
												@if($item->order_note != '')
													<li><b>Ghi chú:</b> <span class="datetxt">{{$item->order_note}}</span></li>
												@endif
											</ul>
										</td>
										<td>
											<ul class="list-date">
												@if(isset($arrUser[$item->order_user_id_created]))
													<li class="datetxt">Người lên đơn:	#{{$item->order_user_id_created}} - {{ucfirst($arrUser[$item->order_user_id_created])}}</li>
												@endif
												@if(isset($arrUser[$item->order_user_id_confirm]))
													<li class="datetxt">Người chốt đơn:	#{{$item->order_user_id_confirm}} - {{ucfirst($arrUser[$item->order_user_id_confirm])}}</li>
												@endif
											</ul>
										</td>
										<td>
											@if($item->order_provice_id == CGlobal::provice_hanoi_id)
												<div>Hà Nội</div>
											@else
												<div>Đơn tỉnh</div>
											@endif

											@if($item['order_confirm_print'] == CGlobal::status_show)
												<div><i class="bgConfirmOrderPrint">Đã in</i></div>
											@endif

										</td>
										<td>
											<a href="{{Config::get('config.BASE_URL')}}admin/order/edit/{{$item['order_id']}}?page={{$pageNo}}&status={{$search['order_status']}}" title="Sửa">
												<i class="fa fa-edit fa-admin"></i>
											</a>
											<a class="item-comment" href="javascript:void(0)" rel="{{$item['order_id']}}" title="Báo cáo">
												<i class="fa fa-align-left fa-admin"></i>
											</a>
											<a class="item-print" href="javascript:void(0)" rel="{{$item['order_id']}}" title="In phiếu">
												<i class="fa fa-print fa-admin"></i>
											</a>
											@if($item['order_link_comment_facebook'] != '')
												<a target="blank" href="{{$item['order_link_comment_facebook']}}" rel="" title="Link comment Facebook">
													<i class="fa fa-facebook-square fa-admin"></i>
												</a>
											@endif
											@if($item['order_confirm_print'] == CGlobal::status_show)
												<div><a href="javascript:void" class="btnDestroyConfirmOrderPrint cursor" dataid="{{$item['order_id']}}">Click hủy in</a></div>
											@endif
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
						<div class="row">
							<div class="alert">
								Không có dữ liệu
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
<!--Popup Comment-->
<div class="modal fade" id="sys_PopupCommentOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-comment">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Báo cáo đơn hàng <span class="btn-close" data-dismiss="modal">X</span></h4>
            </div>
            <div class="modal-body">
               <span class="OrderIdComment" data="0"></span>
               <div class="col-lg-7 col-md-7">
               		<div class="detail-once-order"></div>
               </div>
                <div class="col-lg-5 col-md-5">
                	<div class="list-comment">
						<ul></ul>
					 </div>
					 <textarea id="frmcomment" class="form-control input-sm" name="order_comment"></textarea>
					<div class="txtclickcomment">Gửi báo cáo</div>
               </div>
            </div>
        </div>
    </div>
</div>
<!--Popup Comment-->
<script type="text/javascript">
	jQuery(document).ready(function($){
		var dateToday = new Date();
        jQuery('.date').datetimepicker({
            timepicker:false,
            format:'d-m-Y',
            lang:'vi',
        });
    });
</script>
@stop