<?php
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CDate;
?>
<div id="header">
    <div class="link-top-head">
        <div class="container">
            <div class="time"><i class="fa fa-clock-o" aria-hidden="true"></i> {{CDate::date_vietname(date('D', time()))}}, ngày {{date('d/m/Y', time())}} -- <span id="clock">{{date('H : i : s', time())}}</span></div>
            <div class="box-right-head">
                <span class="link-normal"><i class="fa fa-phone"></i><span> Bán hàng online: </span>
                <a href="tel:{{$textHotline}}"><strong>{{$textHotline}}</strong></a></span>
                <a href="{{URL::route('site.pageGuide')}}" class="link-normal"><i class="glyphicon glyphicon-paste"></i> Hướng dẫn mua hàng</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="line-top">
            @if(Route::currentRouteName() == 'site.index')
            <h1 class="logo">
                <a href="{{URL::route('site.index')}}" title="{{CGlobal::nameSite}}">
                    <img src="{{URL::route('site.index')}}/assets/frontend/img/logo.png" alt="{{CGlobal::nameSite}}">
                </a>
            </h1>
            @else
                <div class="logo">
                    <a href="{{URL::route('site.index')}}" title="{{CGlobal::nameSite}}">
                        <img src="{{URL::route('site.index')}}/assets/frontend/img/logo.png" alt="{{CGlobal::nameSite}}">
                    </a>
                </div>
            @endif
            <h2 style="display:none">Jeans nam xuất khẩu, thời trang nam, quần áo bò nam, quần áo bò phong cách</h2>
            <button type="button" class="mbButtonMenu navbar-toggle pull-right">
                <i class="fa fa-bars fa-2x"></i>
            </button>
            <div class="memberLine">
                <div class="line">
                    @if(isset($member ) && sizeof($member) == 0)
                        <a rel="nofollow" href="javascript:void(0)" id="clickLogin">ĐĂNG NHẬP</a>
                        <span class="vline"></span>
                        <a rel="nofollow" href="javascript:void(0)" id="clickRegister">ĐĂNG KÝ</a>
                        <span class="vline"></span>
                    @else
                        <a rel="nofollow" href="{{URL::route('member.pageMember')}}" class="hiMember">Xin chào: @if(isset($member) && isset($member['member_full_name']) && $member['member_full_name'] != ''){{$member['member_full_name']}} @else {{$member['member_mail']}} @endif <span class="icomment"></span></a>
                        <span class="vline"></span>
                        <a rel="nofollow" href="{{URL::route('member.logout')}}" class="red">Đăng xuất</a>
                        <span class="vline"></span>
                    @endif
                    <a rel="nofollow" class="shopcart" href="{{URL::route('site.pageOrderCart')}}">GIỎ HÀNG @if(isset($numCart) && $numCart > 0)<span>{{$numCart}}</span>@endif</a>
                </div>
                <div class="line">
                    <div class="s">
                        <form id="frmSearch" action="{{URL::route('site.pageProductSearch')}}" method="get">
                            <input id="txtsearch" name="keyword" @if(isset($keyword) && $keyword != '')value="{{$keyword}}"@endif>
                            <a id="clickSearch" class="btn" href="javascript:void(0)">Tìm</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="menuTop">
            <ul>
                <li><a title="Hàng mới về" href="{{URL::route('site.pageProductNew')}}">Hàng mới về</a><span class="v"></span></li>
                @if(!empty($arrCategory))
                    @foreach($arrCategory as $cat)
                        @if($cat->category_menu == CGlobal::status_show && $cat->category_parent_id == 0)
                            <?php $i=0; ?>
                            @foreach($arrCategory as $sub)
                                @if($sub->category_parent_id == $cat->category_id && $sub->category_menu == CGlobal::status_show)
                                    <?php $i++; ?>
                                @endif
                            @endforeach
                            <li>
                                <a title="{{$cat->category_title}}" href="{{FuncLib::buildLinkCategory($cat->category_id, $cat->category_title)}}">{{$cat->category_title}}</a>
                                <span class="v"></span>
                                @if($i > 0)
                                <ul>
                                    @foreach($arrCategory as $sub)
                                        @if($sub->category_menu == CGlobal::status_show && $sub->category_parent_id == $cat->category_id)
                                            <li><a title="{{$sub->category_title}}" href="{{FuncLib::buildLinkCategory($sub->category_id, $sub->category_title)}}">{{$sub->category_title}}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                @endif
                <li @if(Route::currentRouteName() == 'site.pageContact') class="act" @endif><a title="Liên hệ" href="{{URL::route('site.pageContact')}}">Liên hệ</a></li>
            </ul>
            @if(isset($textaddress) && $textaddress != '')
            <div class="col-lg-12 col-md-12 col-sm-12 addressMenu">
                {!! $textaddress !!}
            </div>
            @endif
            <button type="button" class="mbButtonMenuL navbar-toggle pull-right">
                <i class="fa-4x">&times;</i>
            </button>
        </div>
        <div class="overlay-mb-bg"></div>
    </div>
</div>