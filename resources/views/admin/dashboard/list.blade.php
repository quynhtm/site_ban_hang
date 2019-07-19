@extends('admin.layout.html')
@section('header')
    @include('admin.block.header')
@stop
@section('left')
	@include('admin.block.left')
@stop
@section('content')
<div class="main-content">
	<div class="notification-global">Quản trị nội dung website</div>
	<div class="content-global">
		@if($messages != '')
			<div class="col-lg-12 messages-dash">
				{!! $messages !!}
			</div>
		@endif
		@if(!empty($menu))
			@foreach($menu as $item)
				@if(isset($item['sub']) && !empty($item['sub']))
					@foreach($item['sub'] as $sub)
						@if((isset($sub['permission']) && in_array($sub['permission'],$aryPermission)))
							@if(isset($sub['showcontent']) && $sub['showcontent'] == 1)
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
									<a href="{{ $sub['link'] }}">
										<div class="boder-item padding10 text-center">
											<i class="{{ $sub['icon'] }}"></i><br><span>{{ $sub['name'] }}</span>
										</div>
									</a>
								</div>
							@endif
						@endif
					@endforeach
				@endif
			@endforeach
		@endif
	</div>
</div>
@stop
