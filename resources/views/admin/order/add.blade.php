<?php
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
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
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif nick support</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="col-xs-12">
                <div class="row">
                    @if($error != '')
                        <div class="alert-admin alert alert-danger">{!! $error !!}</div>
                    @endif
                    <form class="form-horizontal paddingTop30" name="txtForm" action="" method="post" enctype="multipart/form-data">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="row">
                                <div class="title-box">Người nhận( khách hàng)</div>
                            </div>
                            <div class="form-group">
                                <label class="control-label ">Tên khách hàng<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_title" value="@if(isset($data['order_title'])){{$data['order_title']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">SĐT<span>*<i class="clickLoadOrderCustomer">(Click lấy thông tin)</i></span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_phone" value="@if(isset($data['order_phone'])){{$data['order_phone']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Địa chỉ<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_address" value="@if(isset($data['order_address'])){{$data['order_address']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_email" value="@if(isset($data['order_email'])){{$data['order_email']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="line">
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">Tỉnh/Thành</label>
                                            <div class="controls">
                                                <select style="width: 90%;" name="order_provice_id" id="listProviceId">
                                                    {!! $optionProvice !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">Quận/Huyện</label>
                                            <div class="controls">
                                                <select style="width: 90%;" name="order_dictrict_id" id="listDictrictId" data="@if(isset($data['order_dictrict_id'])){{stripslashes($data['order_dictrict_id'])}}@endif">
                                                    {!! $optionDictrict !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">Phường/Xã</label>
                                            <div class="controls">
                                                <select style="width: 90%;" name="order_ward_id" id="listWardId" data="@if(isset($data['order_ward_id'])){{stripslashes($data['order_ward_id'])}}@endif">
                                                    {!! $optionWard !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="title-box">Thông tin khác</div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Tên FB</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_name_facebook" value="@if(isset($data['order_name_facebook'])){{$data['order_name_facebook']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Link nick FB</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_nick_facebook" value="@if(isset($data['order_nick_facebook'])){{$data['order_nick_facebook']}}@endif">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Link comment</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm" name="order_link_comment_facebook" value="@if(isset($data['order_link_comment_facebook'])){{$data['order_link_comment_facebook']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="title-box">Thông tin đơn hàng</div>
                            <div class="line">
                                <label class="control-label">Mã Sản phẩm(<span id="click-add-pcode">Click để thêm mã</span>)</label>
                                <div id="list-pcode">
                                    <?php $order_list_code = (isset($data['order_list_code']) && $data['order_list_code'] != '') ? unserialize($data['order_list_code']): array(); ?>
                                    @if(is_array($order_list_code) && sizeof($order_list_code) > 0)
                                        @foreach($order_list_code as $item)
                                            <div class="item-product">
                                                <input type="hidden" name="pid[]" @if(isset($item['pid'])) value="{{(int)($item['pid'])}}" @endif  autocomplete="off"/>
                                                Mã<span class="red">*</span> <input type="text" name="pcode[]" @if(isset($item['pcode'])) value="{{stripslashes($item['pcode'])}}" @endif  autocomplete="off"/>
                                                Size<span class="red">*</span> <input type="text" name="psize[]" @if(isset($item['psize'])) value="{{stripslashes($item['psize'])}}" @endif  autocomplete="off"/>
                                                SL<span class="red">*</span> <input type="text" name="pnum[]" @if(isset($item['pnum'])) value="{{(int)($item['pnum'])}}" @endif  autocomplete="off"/> <span class="del-pcode" title="Xóa">X</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="item-product">
                                            <input type="hidden" name="pid[]"  autocomplete="off"/>
                                            Mã<span class="red">*</span> <input type="text" name="pcode[]"  autocomplete="off"/>
                                            Size<span class="red">*</span> <input type="text" name="psize[]" autocomplete="off"/>
                                            SL<span class="red">*</span> <input type="text" name="pnum[]"  autocomplete="off"/> <span class="del-pcode" title="Xóa">X</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Mã quảng cáo</label>
                                <div class="controls">
                                    <select name="order_ads_id">
                                        {!! $optionCodeAds !!}
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5 col-md-5 col-sm-12">
                                    <label class="control-label">Mã vận đơn</label>
                                    <div class="controls">
                                        <input type="text" class="form-control input-sm" name="order_code_post" value="@if(isset($data['order_code_post'])){{$data['order_code_post']}}@endif" @if(isset($data['order_code_post']) && $data['order_code_post'] != '' && $rid != CGlobal::rid_admin) readonly @endif>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label class="control-label">Phí vận chuyển</label>
                                    <div class="controls">
                                        <input type="text" class="form-control input-sm formatMoney" name="order_price_post" value="@if(isset($data['order_price_post'])){{$data['order_price_post']}}@endif" data-v-max="999999999999999" data-v-min="0" data-a-sep="." data-a-dec="," data-a-sign=" đ" data-p-sign="s" >
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Quà tặng kèm</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" rows="2" name="order_gift">@if(isset($data['order_gift'])){{stripslashes($data['order_gift'])}}@endif</textarea>
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Ghi chú</label>
                                <div class="controls">
                                    <textarea class="form-control input-sm" rows="2" name="order_note">@if(isset($data['order_note'])){{stripslashes($data['order_note'])}}@endif</textarea>
                                </div>
                            </div>
                            <div class="title-box mgt10">Trạng thái đơn hàng</div>
                            <div class="line">
                                <div class="col-lg-5 col-md-5 col-sm-12 mgr10">
                                    <div class="row">
                                        <label class="control-label">Đơn vị vận chuyển</label>
                                        <div class="controls">
                                            <select name="order_partner">
                                                {!! $optionPartner !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mgr10">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="control-label">Trạng thái</label>
                                            <div class="controls">
                                                <select class="form-control input-sm" name="order_status">
                                                    {!! $optionStatus !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="title-box">COD & Phí</div>
                            <div class="line">
                                <label class="control-label">Thời gian gửi hàng</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm date" name="order_time_send" value="@if(isset($data['order_time_send']) && $data['order_time_send'] > 0){{date('d-m-Y',$data['order_time_send'])}}@endif">
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Thời gian phát hàng thành công @if(isset($data['order_status']) && $data['order_status'] == CGlobal::phat_da_thanh_cong) <span>*</span> @endif</label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm date" name="order_time_finish" value="@if(isset($data['order_time_finish']) && $data['order_time_finish'] > 0){{date('d-m-Y',$data['order_time_finish'])}}@endif">
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Tổng tiền thu hộ COD(Người nhận phải trả)<span>*</span></label>
                                <div class="controls">
                                    <input type="text" class="form-control input-sm text-bold formatMoney" name="order_total_lst" @if(isset($data['order_total_lst']))value="{{$data['order_total_lst']}}" @else value="{{CGlobal::total_price_default}}" @endif data-v-max="999999999999999" data-v-min="0" data-a-sep="." data-a-dec="," data-a-sign=" đ" data-p-sign="s" >
                                </div>
                            </div>
                            <div class="line">
                                <label class="control-label">Nhân viên chốt đơn</label>
                                <select class="form-control input-sm" name="order_user_id_confirm">
                                    {!! $optionUserConfirm !!}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="row">
                                {!! csrf_field() !!}
                                <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                                <button type="submit" name="txtSubmit" id="buttonSubmit" class="btn btn-primary">Lưu lại</button>
                                <button type="reset" class="btn">Bỏ qua</button>
                            </div>
                        </div>
                    </form>
                    <div class="post-note box-order">
                        <div class="comment-order">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row mgt10">
                                    <div class="form-group">
                                        <label class="control-label">Báo cáo</label>
                                        <div class="controls">
                                            <div class="list-comment">
                                                <ul>
                                                    @if($comment != '')
                                                        {!! $comment !!}
                                                    @endif
                                                </ul>
                                            </div>
                                            <textarea id="frmcomment" class="form-control input-sm" name="order_comment"></textarea>
                                            <div class="txtclicknote">Click để gửi báo cáo</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        var dateToday = new Date();
        jQuery('.date').datetimepicker({
            timepicker:false,
            format:'d-m-Y',
            lang:'vi',
        });

        jQuery('.formatMoney').autoNumeric('init');
    });
</script>
@stop