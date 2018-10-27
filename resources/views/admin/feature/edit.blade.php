@extends('admin.layout')
@section('title', 'Cập nhật chức năng ' . $feature->display_name)
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
			        <h3 class="card-title">Cập nhật gói tài khoản {{ $feature->display_name }}</h3>
			    </div>
			    <!-- /.card-header -->
			    <!-- form start -->
			    <form role="form" action="{{ route('feature.update', $feature->id) }}" method="POST">
			    	<input type="hidden" name="id" value="{{ $feature->id }}">
			    	{{csrf_field()}}
			    	{{ method_field('PUT') }}
			        <div class="card-body">
			        	<div class="form-group">
		                    <label for="display_name">Tên</label>
		                    <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $feature->display_name }}" placeholder="Nhập tên chức năng ...">
	                  	</div>
	                  	<div class="form-group">
		                    <label for="name">Name Code</label>
		                    <input type="text" class="form-control" id="name" name="name" value="{{ $feature->name }}" placeholder="Nhập name code ...">
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