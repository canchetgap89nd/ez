@extends('login.layout')
@section("title", "Lựa chọn Fanpage để quản lý")
@section("titleHead", "Lựa chọn Fanpage để Chốt Sale quản lý")
@section('content')
@if(session('message'))
<div class="row">
	<div class="col-md-12">
	    
	</div>
</div>
@endif
<form action="{{ route('create.user.info') }}" method="post">
	{{ csrf_field() }}
	<div class="cl-mid col-md-5 col-sm-6">
		<div class="cl-ls">
			<h3 class="title-cl">Chọn Fanpage để quản lý</h3>
			<ul class="list-item list-style-none" id="list-fanpage-user">
				@isset($pages)
				@foreach($pages as $page)
				<li>
					<div class="md-checkbox">
						<input type="checkbox" id="page_{{ $page['id'] }}" value="{{ $page['id'] }}" name="pages[]" {{ $page['active'] ? 'checked' : '' }}
						@if(old('pages') && in_array($page['id'], old('pages')))
							checked
						@endif
						>
						<label for="page_{{ $page['id'] }}">
							<img src="https://graph.facebook.com/{{ $page['fb_page_id'] }}/picture?height=40&width=40"> 
							<span>{{ $page['page_name'] }}</span>
						</label>
					</div>
				</li>
				@endforeach
				@endisset
			</ul>
		</div>
	</div>
	<div class="cl-left col-md-4">
		<div class="form-info">
			@if(session('message'))
			<div>
				<div class="alert alert-dismissible 
			    @if (session('status') == 'warning')
			    alert-warning
			    @elseif(session('status') == 'danger')
			    alert-danger
			    @elseif(session('status') == 'success')
			    alert-success
			    @endif
			    ">
			        <strong><i class="icon fa fa-ban"></i> Cảnh báo!</strong>
			        {{ session('message') }}
			    </div>
			    <p class="help-block">
			    	<strong>Lưu ý:</strong> Để nâng cấp tài khoản vui lòng vào tab <a href="{{ asset('setting') }}">cài đặt</a> => ở mục <strong>Tài khoản</strong> bạn click <strong>Nâng cấp TK</strong>
			    </p>
			    <p>
			    	<strong>
			    		<a href="{{ route('info.prices') }}" target="_blank">Xem bảng giá</a>
			    	</strong>
			    </p>
			</div>
			@endif
			<h3>Thông tin tài khoản</h3>
			<div class="form-group">
				<input type="text" value="{{ isset($user) && $user->name ? $user->name : old('name') }}" class="form-control" placeholder="Họ và tên" name="name">
				<p class="error__control">{{ $errors->first('name') }}</p>
			</div>
			<div class="form-group">
				<input type="email" value="{{ isset($user) && $user->user_email ? $user->user_email : old('email') }}" class="form-control" placeholder="Email" name="email">
				<p class="error__control">{{ $errors->first('email') }}</p>
			</div>
			<div class="form-group">
				<input type="text" value="{{ isset($user) && $user->user_phone ? $user->user_phone : old('phone') }}" class="form-control" placeholder="Số điện thoại" name="phone">
				<p class="error__control">{{ $errors->first('phone') }}</p>
			</div>
			<div class="form-group">
				<p class="text-red">{{ $errors->first('pages') }}</p>
			</div>
			<button type="submit" class="btn btn-success sbProfile">Xác nhận</button>
			<!-- <a href="{{ route('logout') }}">
				<button type="button" class="btn btn-danger">Hủy</button>
			</a> -->
		</div>
	</div>
	<div class="col-md-3">
		<div class="cl-guide">
			<h3 class="title-cl">Hướng dẫn</h3>
			<div class="form-group">
				<div class="boxHelper">
					<p class="help-block">
						<strong>Bước 1:</strong> Chọn Fanpage bạn muốn quản lý
					</p>
					<p class="help-block">
						<strong>Bước 2:</strong> Điền thông tin tài khoản
					</p>
					<p class="help-block">
						<strong>Bước 3:</strong> Click xác nhận
					</p>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection