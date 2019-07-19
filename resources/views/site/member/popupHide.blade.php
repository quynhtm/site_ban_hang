<?php
use App\Library\PHPDev\CGlobal;
?>
<div id="sys-popup-register" class="content-popup-show fade" style="display:none">
	<div class="modal-dialog modal-dialog-comment">
        <div class="modal-content">
            <div class="modal-title-classic">Đăng ký thành viên <span class="btn-close" data-dismiss="modal">X</span></div>
            <div class="content-popup-body">
                <div class="classic-popup-subtitle">Bạn vui lòng nhập thông tin đăng ký</div>
                <p class="error-log" id="error-register"></p>
                <form id="frmRegister" method="POST" class="frmForm" name="frmRegister" action="{{URL::route('member.pageRegister')}}">
                    <div class="classic-popup-input">
                        <div>
                            <input type="text" placeholder="Email đăng nhập" id="sys_reg_email" name="sys_reg_email">
                        </div>
                        <div>
                            <input type="password" placeholder="Mật khẩu" id="sys_reg_pass" name="sys_reg_pass">
                        </div>
                        <div>
                            <input type="password" placeholder="Nhập lại mật khẩu" id="sys_reg_re_pass" name="sys_reg_re_pass">
                        </div>
                        <div>
                             <input type="text" placeholder="Họ tên" id="sys_reg_full_name" name="sys_reg_full_name">
                        </div>
                        <div>
                            <input type="text" placeholder="Số điện thoại" id="sys_reg_phone" name="sys_reg_phone">
                        </div>
                        <div>
                            <input type="text" placeholder="Địa chỉ" id="sys_reg_address" name="sys_reg_address">
                        </div>
                    </div>
                    <div class="classic-popup-bottomText">
                          Bằng việc bấm đăng ký bạn đã chấp nhận các
                          <a href="#" target="_blank"> điều khoản và quy định</a> của {{CGlobal::domain}}
                    </div>
                    <div class="action-popup-button">
                        <div class="btn btn-primary btn-ext" id="btnRegister" href="javascript:void(0)">Đăng kí ngay</div>
                    </div>
                    {!! csrf_field() !!}
				</form>
            </div>
        </div>
    </div>
</div>
<div id="sys-popup-login" class="content-popup-show fade" style="display:none">
	<div class="modal-dialog modal-dialog-comment">
        <div class="modal-content">
            <div class="modal-title-classic">Đăng nhập <span class="btn-close" data-dismiss="modal">X</span></div>
            <div class="content-popup-body">
                <div class="classic-popup-subtitle">Bạn vui lòng nhập thông tin đăng nhập</div>
                <p class="error-log" id="error-login"></p>
                <form id="frmLogin" method="POST" class="frmForm" name="frmLogin" action="{{URL::route('member.pageLogin')}}">
                    <div class="classic-popup-input">
                        <div>
                             <input type="text" placeholder="Nhập Email" name="sys_login_mail" id="sys_login_mail">
                        </div>
                        <div>
                            <input type="password" placeholder="Nhập mật khẩu" name="sys_login_pass" id="sys_login_pass">
                        </div>
                     </div>
                    <div class="action-popup-button">
                        <div class="btn btn-primary btn-ext" id="btnLogin" href="javascript:void(0)">Đăng nhập</div>
                        <div class="classic-popup-bottomText">
                          <a id="clickForgetPass" href="javascript:void(0)" rel="nofollow">Bạn quên mật khẩu</a>
                        </div>
                    </div>
                    <div class="action-popup-button">
                        <a href="javascript:void(0)" id="clickLoginFacebook" class="login-facebook"></a>
                        <a href="javascript:void(0)" id="clickLoginGoogle" class="login-google"></a>
                    </div>
                    {!! csrf_field() !!}
				</form>
            </div>
        </div>
    </div>
</div>
<div id="sys-popup-forgetpass" class="content-popup-show fade" style="display:none">
	<div class="modal-dialog modal-dialog-comment">
        <div class="modal-content">
            <div class="modal-title-classic">Quên mật khẩu <span class="btn-close" data-dismiss="modal">X</span></div>
            <div class="content-popup-body">
                <div class="classic-popup-subtitle">Nhập Email đã đăng kí để nhận thông tin hỗ trợ lấy lại mật khẩu</div>
                <p class="error-log" id="error-forgetpass"></p>
                <form id="frmForgetPass" method="POST" class="frmForm" name="frmForgetPass" action="{{URL::route('member.pageForgetPass')}}">
                    <div class="classic-popup-input">
                        <div>
                             <input type="text" placeholder="Nhập Email" name="sys_forget_mail" id="sys_forget_mail">
                        </div>
                     </div>
                    <div class="action-popup-button">
                        <div class="btn btn-primary btn-ext" id="btnForgetpass" href="javascript:void(0)">Gửi đi</div>
                    </div>
                    {!! csrf_field() !!}
				</form>
            </div>
        </div>
    </div>
</div>