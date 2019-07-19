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
                    <div class="line-solid">
                        <h1><a href="{{URL::route('member.pageChagePass')}}" title="Thay đổi mật khẩu">Thay đổi mật khẩu</a></h1>
                    </div>
                    @if(isset($error))
                        <p class="error-log" id="error-change-info">@if(isset($error)){!! $error !!}@endif</p>
                    @endif
                    @if(isset($messages) && $messages != '')
                        {!! $messages !!}
                    @endif
                    <div class="view-content-static">
                        <div class="wrapp-content-body">
                            <div class="classic-popup-subtitle">Bạn vui lòng nhập mật khẩu mới</div>
                            <p class="error-log" id="error-change-new-pass">@if(isset($error)){!! $error !!}@endif</p>
                            @if(isset($messages) && $messages != '')
                                {!! $messages !!}}
                            @endif
                            <form id="frmChangeNewPass" method="POST" class="frmForm" name="frmChangeNewPass">
                                <div class="classic-popup-input">
                                    <div>
                                        <input type="password" placeholder="Mật khẩu mới" id="sys_change_new_pass" name="sys_change_new_pass">
                                    </div>
                                    <div>
                                        <input type="password" placeholder="Nhập lại mật khẩu mới" id="sys_change_new_re_pass" name="sys_change_new_re_pass">
                                    </div>
                                </div>
                                <div class="action-popup-button">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-primary" id="btnChangeNewPass">Thay đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop