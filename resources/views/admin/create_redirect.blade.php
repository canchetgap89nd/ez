@extends('admin.layout')
@section('title', 'Tạo chuyển hướng')
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
			        <h3 class="card-title">Thông tin chuyển hướng</h3>
			    </div>
			    <!-- /.card-header -->
			    <!-- form start -->
			    <form role="form" action="{{ route('createRedirect302') }}" method="POST">
			    	{{csrf_field()}}
			        <div class="card-body">
			        	<div class="form-group">
		                    <label for="nameCate">Link cần chuyển hướng</label>
		                    <input type="text" class="form-control" name="from_link" value="{{ old('from_link') }}" placeholder="Nhập link cần chuyển hướng ...">
	                  	</div>

			            <div class="form-group">
						    <label for="nameCate">Chuyển hướng tới</label>
		                    <input type="text" class="form-control" name="to_link" value="{{ old('to_link') }}" placeholder="Nhập link được chuyển hướng tới ...">
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