<?php use App\Library\PHPDev\FuncLib; ?>
<div id="sidebar" class="sidebar sidebar-fixed responsive sidebar-scroll" data-sidebar="true" data-sidebar-scroll="true" data-sidebar-hover="true">
	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<a href="{{URL::route('admin.dashboard')}}" class="btn btn-success">
				<i class="ace-icon fa fa-signal"></i>
			</a>
			<a href="{{URL::route('admin.role')}}" class="btn btn-info">
				<i class="ace-icon fa fa-pencil"></i>
			</a>
			<a href="{{URL::route('admin.user')}}" class="btn btn-warning">
				<i class="ace-icon fa fa-users"></i>
			</a>
			<a href="{{URL::route('admin.info')}}" class="btn btn-danger">
				<i class="ace-icon fa fa-cogs"></i>
			</a>
		</div>
		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span>
			<span class="btn btn-info"></span>
			<span class="btn btn-warning"></span>
			<span class="btn btn-danger"></span>
		</div>
	</div>
	<ul class="nav nav-list">
		<li class="@if(Route::currentRouteName() == 'admin.dashboard') active @endif">
			<a href="{{URL::route('admin.dashboard')}}">
				<i class="menu-icon fa fa-tachometer"></i>
				<span class="menu-text"> Bảng điều khiển</span>
			</a>
			<b class="arrow"></b>
		</li>
		@if(isset($menu) && sizeof($menu) > 0)
			@foreach($menu as $item)
				<li class="@if(isset($item['arr_link_sub']) && in_array(Route::currentRouteName(), $item['arr_link_sub'])) open @endif">
					<a href="{{ $item['link'] }}" @if(isset($item['sub']) && !empty($item['sub']))class="dropdown-toggle"@endif>
						<i class="menu-icon {{ $item['icon'] }}"></i>
						<span class="menu-text">{{ $item['name'] }}</span>
						@if(isset($item['sub']) && !empty($item['sub']))
						<b class="arrow fa fa-angle-down"></b>
						@endif
					</a>
					<b class="arrow"></b>
					@if(isset($item['sub']) && !empty($item['sub']))
					<ul class="submenu">
						@foreach($item['sub'] as $sub)
							@if((isset($sub['permission']) && in_array($sub['permission'],$aryPermission)))
							<li class="@if(isset($sub['router_name']) && Route::currentRouteName() == $sub['router_name']) active @endif">
								<a href="{{ $sub['link'] }}">
									<i class="menu-icon fa fa-caret-right"></i>
									{{ $sub['name'] }}
								</a>
								<b class="arrow"></b>
							</li>
							@endif
						@endforeach
					</ul>
					@endif
				</li>
			@endforeach
		@endif
	</ul>
	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i id="sidebar-toggle-icon"
		   class="ace-icon fa fa-angle-double-left ace-save-state"
		   data-icon1="ace-icon fa fa-angle-double-left"
		   data-icon2="ace-icon fa fa-angle-double-right">
		</i>
	</div>
</div>