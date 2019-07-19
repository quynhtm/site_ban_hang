@extends('site.layout.html')
@section('header')
	@include('site.block.header')
@stop
@section('footer')
	@include('site.block.footer')
@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="main-box">
                    @if(isset($error))
                        <p class="error-log" id="error-change-info">@if(isset($error)){!! $error !!}@endif</p>
                    @endif
                    @if(isset($messages) && $messages != '')
                        {!! $messages !!}
                    @endif
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-5">
                            <div class="boxLinkLeft">
                                <div class="tt"><i class="fa fa-user"></i>@if(isset($member) && isset($member['member_full_name']) && $member['member_full_name'] != ''){{$member['member_full_name']}} @else {{$member['member_mail']}} @endif</div>
                                <ul>
                                    <li><a href="{{URL::route('member.pageChageInfo')}}" title="Thay đổi thông tin">Thay đổi thông tin</a></li>
                                    <li><a href="{{URL::route('member.pageChagePass')}}" title="Thay đổi mật khẩu">Thay đổi mật khẩu</a></li>
                                    <li><a href="{{URL::route('member.pageHistoryOrder')}}" title="Lịch sử mua hàng">Lịch sử mua hàng</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7">
                            <div class="line-solid text-left">
                                <h1><a title="Thay đổi thông tin cá nhân" href="{{URL::route('member.pageChageInfo')}}">Thay đổi thông tin cá nhân</a></h1>
                            </div>
                            <form id="frmChangeInfo" method="POST" class="frmForm" name="frmChangeInfo">
                                <div class="classic-popup-input">
                                    <div>
                                        <input type="text" placeholder="Email đăng nhập" id="sys_change_email" name="sys_change_email" readonly="readonly" @if(isset($member['member_mail']))value="{{$member['member_mail']}}" @endif>
                                    </div>
                                    <div>
                                        <input type="text" placeholder="Họ tên" id="sys_change_full_name" name="sys_change_full_name" @if(isset($member['member_full_name']))value="{{$member['member_full_name']}}" @endif>
                                    </div>
                                    <div>
                                        <input type="text" placeholder="Số điện thoại" id="sys_change_phone" name="sys_change_phone" @if(isset($member['member_phone']))value="{{$member['member_phone']}}" @endif>
                                    </div>
                                    <div>
                                        <input type="text" placeholder="Địa chỉ" id="sys_change_address" name="sys_change_address" @if(isset($member['member_address']))value="{{$member['member_address']}}" @endif>
                                    </div>
                                </div>
                                <div class="action-popup-button">
                                    <button type="submit" class="btn btn-primary" id="btnChangeInfo">Thay đổi thông tin</button>
                                </div>
                                {!! csrf_field() !!}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop