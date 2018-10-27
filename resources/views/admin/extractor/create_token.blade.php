@extends('admin.layout')
@section('title', 'Cập nhật Token Admin')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/iCheck/all.css') }}">
@endsection
@section('content')
<section class="content">
	<div class="row">
		@if(session('message'))
	    <div class="col-12">
	        <div class="alert alert-dismissible 
	        @if (session('status') == 'success')
	        alert-success
	        @else
	        alert-alert
	        @endif
	        ">
	            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	            <h5><i class="icon fa fa-ban"></i> Cảnh báo!</h5>
	            {{ session('message') }}
	        </div>
	    </div>
	    @endif
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
    <form role="form" action="{{ route('admin.extractor.storeTokenLord') }}" method="POST">
		<div class="row">
			<div class="col-md-6">
				<div class="card card-primary">
				    <div class="card-header">
				        <h3 class="card-title">Cập nhật Access Token Admin</h3>
				    </div>
				    <!-- /.card-header -->
				    <!-- form start -->
				    	{{csrf_field()}}
			        <div class="card-body">
	                  	<div class="form-group">
		                    <label for="access_token">Access Token: </label>
		                    <textarea class="form-control" rows="5" placeholder="Nhập access token" required name="access_token"></textarea>
	                  	</div>
			        </div>
			        <!-- /.card-body -->
			        <div class="card-footer">
			            <button type="submit" class="btn btn-primary">Xác nhận</button>
			        </div>
				</div>
			</div>
		</div>
    </form>
</section>
@endsection
@section('scripts')

@endsection