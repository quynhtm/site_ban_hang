<?php
use App\Library\PHPDev\FuncLib;
use App\Library\PHPDev\CGlobal;
use App\Library\PHPDev\ThumbImg;
use App\Library\PHPDev\Utility;
?>
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
						<div class="col-lg-9 col-md-9 col-sm-7">
							<div class="line-solid text-left">
								@if(sizeof($dataCate) != 0)
									<h1><a title="{{$dataCate->category_title}}" href="{{FuncLib::buildLinkCategory($dataCate->category_id, $dataCate->category_title)}}">{{$dataCate->category_title}}</a></h1>
								@else
									<h1><a href="{{URL::route('member.pageMember')}}" title="Thông tin thành viên">Thông tin thành viên</a></h1>
								@endif
							</div>
							<div class="row">
								@if(count($data) >= 6)
									<div class="line">
										<div class="col-lg-7 col-md-7 col-sm-7">
											<div class="box-slider-post box-main-post">
												@foreach($data as $k=>$item)
													@if($k == 0)
														<div class="item">
															<a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
																@if($item->news_image != '')
																	<img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
																@endif
																<div class="overx">
																	<h3>{{$item->news_title}}</h3>
																	<div class="main-intro">{!!Utility::cutWord($item->news_intro, 30, '...')!!}</div>
																</div>
															</a>
														</div>
													@endif
												@endforeach
											</div>
										</div>
										<div class="col-lg-5 col-md-5 col-sm-5 box-sub-post">
											@foreach($data as $k=>$item)
												@if($k > 0 && $k <= 3)
													<div class="line item-post-news">
														<a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
															@if($item->news_image != '')
																<img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
															@endif
														</a>
														<h3><a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{$item->news_title}}</a></h3>
														<p class="time">{{date('d/m/Y', $item->news_created)}}</p>
														<p class="sub-intro">{!!Utility::cutWord($item->news_intro, 30, '...')!!}</p>
													</div>
												@endif
											@endforeach
										</div>
									</div>
								@endif
								<div class="line mgt15">
									@if(count($data) <= 5 )
										@foreach($data as $k=>$item)
											@if($k <= 4)
												<div class="col-lg-6 col-md-6 col-sm-12 item-post-news">
													<a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
														@if($item->news_image != '')
															<img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
														@endif
													</a>
													<h3><a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{$item->news_title}}</a></h3>
													<p class="time">{{date('d/m/Y', $item->news_created)}}</p>
													<p>{!!Utility::cutWord($item->news_intro, 30, '...')!!}</p>
												</div>
											@endif
										@endforeach
									@else
										@foreach($data as $k=>$item)
											@if($k >= 4)
												<div class="col-lg-6 col-md-6 col-sm-12 item-post-news">
													<a class="iThumbPost" title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">
														@if($item->news_image != '')
															<img alt="{{$item->news_title}}" src="{{ThumbImg::thumbBaseNormal(CGlobal::FOLDER_NEWS, $item->news_id, $item->news_image, 600, 600, '', true, true)}}">
														@endif
													</a>
													<h3><a title="{{$item->news_title}}" href="{{FuncLib::buildLinkDetailNews($item->news_id, $item->news_title)}}">{{$item->news_title}}</a></h3>
													<p class="time">{{date('d/m/Y', $item->news_created)}}</p>
													<p>{!!Utility::cutWord($item->news_intro, 30, '...')!!}</p>
												</div>
											@endif
										@endforeach
									@endif
								</div>
								<div class="show-box-paging">{!! $paging !!}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop