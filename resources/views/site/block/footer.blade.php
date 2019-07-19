<?php
use App\Http\Models\Statics;
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
?>
<div id="footer">
    <div class="container">
        <div class="boxLinkFooter">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 address">
                    @if(isset($textaddress) && $textaddress != '')
                    {!! $textaddress !!}
                    @endif
                </div>
                @if(isset($arrCateStatic) && sizeof($arrCateStatic) > 0)
                    @foreach($arrCateStatic as $key=>$cat)
                    <?php $arrItem = Statics::searchByConditionCatid($cat->category_id, 10); ?>
                    <div class="col-lg-3 col-md-3 col-sm-12 link">
                        <div class="tt">{!! $cat->category_title !!}</div>
                        @if(sizeof($arrItem) > 0)
                        <ul>
                            @foreach($arrItem as $item)
                            <li><a href="{{FuncLib::buildLinkDetailStatic($item['statics_id'], $item['statics_title'])}}" title="{{$item['statics_title']}}">{{$item['statics_title']}}</a></li>
                            @endforeach
                        </ul>
                        @endif
                        @if($key == 0 && CGlobal::is_dev == 0)
                        <div class="line-share-facebook">
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.9&appId=685975718241032";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));</script>
                            <div class="fb-like" data-href="{{CGlobal::link_social_facebook}}" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="false"></div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                @endif
                <div class="col-lg-3 col-md-3 col-sm-12 link-trans">
                    <div class="tt">Thanh toán</div>
                    <div class="lineimg">
                        <a href="javascript:void(0)" rel="nofollow" title="Ngân lượng"><img alt="Ngân Lượng" src="{{URL::route('site.index')}}/assets/frontend/img/nganluong.png"></a>
                        <a href="javascript:void(0)" rel="nofollow" title="Vietinbank"><img alt="Vietinbank" src="{{URL::route('site.index')}}/assets/frontend/img/vietinbank.png"></a>
                    </div>
                    <div class="tt mgt15">Vận chuyển</div>
                    <div class="lineimg">
                        <a href="javascript:void(0)" rel="nofollow" title="ems.com.vn"><img alt="VNPost" src="{{URL::route('site.index')}}/assets/frontend/img/vnpost.jpg"></a>
                        <a href="javascript:void(0)" rel="nofollow" title="viettelpost.com.vn"><img alt="VietTel" src="{{URL::route('site.index')}}/assets/frontend/img/viettel.jpg"></a>
                    </div>
                    <div class="tt mgt15">Kết nối với chúng tôi</div>
                    <div class="lineimg">
                        @if(CGlobal::is_dev == 0)
                        <div class="line-share-google">
                            <a target="_blank" rel="nofollow" href="{{CGlobal::link_social_google_plus}}" class="iconGoogle">Google</a>
                            <a target="_blank" rel="nofollow" href="{{CGlobal::link_social_facebook}}" class="iconFacebook">Facebook</a>
                       </div>
                       @endif
                    </div>
                </div>
            </div>
        </div>
        @if(isset($textlink) && $textlink != '')
        <div class="nolist clearfix">
            {!!$textlink!!}
        </div>
        @endif
        @if(isset($copyright) && $copyright != '')
            <div class="copyright">
                {!!$copyright!!}
            </div>
        @endif
    </div>
</div>