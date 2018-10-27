@extends('admin.layout')
@section('title', 'Cập nhật gói tài khoản '. $package->display_name)
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
    <form role="form" action="{{ route('package.update', $package->id) }}" method="POST">
    	<input type="hidden" name="id" value="{{ $package->id }}">
    	<div class="row">
			<div class="col-md-6">
				<div class="card card-primary">
				    <div class="card-header">
				        <h3 class="card-title">Thông tin gói tài khoản</h3>
				    </div>
				    <!-- /.card-header -->
				    <!-- form start -->
				    	{{csrf_field()}}
				    	{{ method_field('PUT') }}
				        <div class="card-body">
				        	<div class="form-group">
			                    <label for="display_name">Tên</label>
			                    <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $package->display_name }}" placeholder="Nhập tên gói ...">
		                  	</div>
		                  	<div class="form-group">
			                    <label for="name">Name Code</label>
			                    <input type="text" class="form-control" id="name" name="name" value="{{ $package->name }}" placeholder="Nhập name code ...">
		                  	</div>
				            <div class="form-group">
				                <label for="price">Giá (đơn vị: đồng)</label>
			                    <input type="number" class="form-control" id="price" name="price" value="{{ $package->price }}" placeholder="Nhập giá gói ...">
				            </div>
				            <div class="form-group">
				                <label for="page_limit">Số Fanpage</label>
			                    <select class="form-control" name="page_limit" id="page_limit">
			                    	@for($i = 1;$i <= 50;$i++)
			                    	<option value="{{ $i }}" 
									@if($package->page_limit == $i)
									selected 
									@endif
			                    	>{{ $i }}</option>
			                    	@endfor
			                    </select>
				            </div>
				            <div class="form-group">
				                <label for="staff_limit">Số nhân viên</label>
			                    <select class="form-control" name="staff_limit" id="staff_limit">
			                    	@for($i = 1;$i <= 50;$i++)
			                    	<option value="{{ $i }}"
									@if($package->staff_limit == $i)
									selected 
									@endif
			                    	>{{ $i }}</option>
			                    	@endfor
			                    </select>
				            </div>
				        </div>
				        <!-- /.card-body -->
				        <div class="card-footer">
				            <button type="submit" class="btn btn-primary">Xác nhận</button>
				        </div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card card-primary">
					<div class="card-header">
				        <h3 class="card-title">Thông tin chức năng</h3>
				    </div>
				    <div class="card card-body">
				    	<div class="form-group">
			            	<label>Các chức năng: </label>
			            </div>
			            <div class="form-group">
			            	@foreach($features as $feature)
			            	<label>
			                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" class="flat-red"
								@if(in_array($feature->id, $featurePackage))
								checked
								@endif
			                    >
			                    {{ $feature->display_name }}
		                  	</label>
		                  	@endforeach
			            </div>
					    <div class="form-group">
			                <label for="Catedesc">Mô tả</label>
			                <textarea id="Catedesc" name="description" class="form-control" placeholder="Nhập vào mô tả ...">{{ $package->description }}</textarea>
			            </div>
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