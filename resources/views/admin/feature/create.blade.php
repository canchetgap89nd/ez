@extends('admin.layout')
@section('title', 'Tạo tính năng mới')
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
		<div class="col-md-12">
			<div class="card card-primary">
			    <div class="card-header">
			        <h3 class="card-title">Thông tin tính năng</h3>
			    </div>
			    <!-- /.card-header -->
			    <!-- form start -->
			    <form role="form" action="{{ route('feature.store') }}" method="POST">
			    	{{csrf_field()}}
			        <div class="card-body">
			        	<div class="form-group">
		                    <label for="name">Tên</label>
		                    <input type="text" class="form-control" id="name" name="display_name" value="{{ old('display_name') }}" placeholder="Nhập tên tính năng ...">
	                  	</div>
	                  	<div class="form-group">
		                    <label for="name">Name Code</label>
		                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nhập Name code ...">
	                  	</div>
			        </div>
			        <!-- /.card-body -->

			        <div class="card-footer">
			            <button type="submit" class="btn btn-primary">Xác nhận</button>
			        </div>
			    </form>
			</div>
		</div>
	</div>
</section>
@endsection