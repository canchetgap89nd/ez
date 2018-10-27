@extends('admin.layout')
@section('title', 'Kích hoạt gói tài khoản')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/iCheck/all.css') }}">
@endsection
@section('content')
<section class="content">
	<div class="row">
		@if ($errors->any())
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible">
              	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              	<h5><i class="icon fa fa-ban"></i> Cảnh báo!</h5>
	          	<ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
	        	</ul>
            </div>
		</div>
		@endif
	</div>
    <form role="form" action="{{ route('user-package.store') }}" method="POST">
    	<input type="hidden" name="payment_id" value="{{ $payment->id }}">
		<div class="row">
			<div class="col-md-6">
				<div class="card card-primary">
				    <div class="card-header">
				        <h3 class="card-title">Kích hoạt gói tài khoản</h3>
				    </div>
				    <!-- /.card-header -->
				    <!-- form start -->
				    	{{csrf_field()}}
				        <div class="card-body">
				        	<div class="form-group">
			                    <label for="display_name">Tên người dùng: </label>
			                    <span>{{ $payment->user->name }}</span>
		                  	</div>
		                  	<div class="form-group">
			                    <label for="display_name">ID người dùng: </label>
			                    <span>{{ $payment->user->id }}</span>
		                  	</div>
		                  	<div class="form-group">
			                    <label for="name">Tên gói: </label>
			                    <span>{{ $payment->package->display_name }}</span>
		                  	</div>
		                  	@if($userPack)
		                  	<div class="form-group">
			                    <label for="name">Thời gian hết hạn gói hiện tại: </label>
			                    <span>{{ date('d-m-Y', strtotime($userPack->expire_at)) }}</span>
		                  	</div>
		                  	@endif
				            <div class="form-group">
			                    <label for="name">Thời hạn (tháng): </label>
			                    @php
			                    $totalMonth = $payment->duration + $payment->duration_bonus;
			                    @endphp
			                    <span>{{ $totalMonth }}</span>
			                    @if($userPack)
			                    <p>Hết hạn vào ngày: {{ date('d-m-Y', strtotime('+ ' . $totalMonth . ' months', strtotime($userPack->expire_at))) }}</p>
			                    @else
			                    <p>Hết hạn vào ngày: {{ date('d-m-Y', strtotime('+ ' . $totalMonth . ' months', time())) }}</p>
			                    @endif
		                  	</div>
				        </div>
				        <!-- /.card-body -->

				        <div class="card-footer">
				            <button type="submit" class="btn btn-primary">Kích hoạt</button>
				        </div>
				</div>
			</div>
		</div>
    </form>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('admins/plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript">
	window.onbeforeunload = function () {
 		return 'Any string';
	};
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      	checkboxClass: 'icheckbox_flat-green',
      	radioClass   : 'iradio_flat-green'
    })
</script>
@endsection