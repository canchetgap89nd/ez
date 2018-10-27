@extends('login.layout')
@section('title', 'Xác nhận phân quyền')
@section('titleHead', "Xác nhận phân quyền trên Chốt Sale")
@section('content')
<div class="container">
	<h3>Chào mừng đến với ứng dụng quản lý Fanpage Chốt Sale!</h3>
	<p class="help-block">Bạn đã nhận {{ count($accounts) > 1 ? 'các' : '' }} được đề nghị làm nhân viên:</p>
	<div class="row">
		@foreach($accounts as $acc)
		<div class="col-sm-6" class="boxItemInvite" id="box_{{ $acc['pages'][0]->user_parent }}">
			<div class="boxRoles">
				<div>
					<h3>Yêu cầu của: {{ $acc['pages'][0]->user_name_parent }}</h3>
					<p>Bạn sẽ làm <strong>{{ $acc['role'] }}</strong> cho các Fanpage dưới đây: </p>
				</div>
				<ul class="list-group list-group-flush">
					@foreach($acc['pages'] as $page)
				  	<li class="list-group-item">
				  		<img src="https://graph.facebook.com/{{ $page->fb_page_id }}/picture?height=40&width=40">
				  		{{ $page->page_name }}
				  	</li>
				  	@endforeach
				</ul>
				<div class="btn_conf_role">
					<button class="btn btn-success btn-sm pull-right" onclick="confirmWithRole({{ $acc['pages'][0]->user_parent }})">Xác nhận</button>
					<button style="margin-right: 10px;" class="btn btn-warning btn-sm pull-right" onclick="denyWithRole({{ $acc['pages'][0]->user_parent }})">Từ chối</button>
				</div>
			</div>
		</div>
		@endforeach
	</div>
	<p class="help-block">
		Click vào nút "Xác nhận" để tiếp nhận vai trò của mình hoặc "Từ chối" yêu cầu
	</p>
	<div id="loadding_conf_role" class="hidden">
		<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
		<span class="sr-only">Loading...</span>
	</div>
	<div class="btn_conf_role">
		<button class="btn btn-warning" onclick="denyAllInvite()">Từ chối tất cả</button>
	</div>
</div>
@endsection