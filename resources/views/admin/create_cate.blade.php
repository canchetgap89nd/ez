@extends('admin.layout')
@section('title', 'Tạo danh mục')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
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
			        <h3 class="card-title">Thông tin danh mục</h3>
			    </div>
			    <!-- /.card-header -->
			    <!-- form start -->
			    <form role="form" action="{{ route('blog.create.category') }}" method="POST">
			    	{{csrf_field()}}
			        <div class="card-body">
			        	<div class="form-group">
		                    <label for="nameCate">Tên danh mục</label>
		                    <input type="text" class="form-control" onblur="generateSlug()" id="nameCate" name="cate_name" value="{{ old('cate_name') }}" placeholder="Nhập tên danh mục ...">
	                  	</div>
			            <div class="form-group">
			                <label for="slugCate">URL</label>
			                <div class="input-group mb-3">
							    <div class="input-group-prepend">
							        <span class="input-group-text">https://chotsale.com.vn/blog/category/</span>
							    </div>
							    <input id="slugCate" value="{{ old('cate_slug') }}" name="cate_slug" type="text" class="form-control" placeholder="Đường dẫn tĩnh">
							</div>
			            </div>
			            <div class="form-group">
						    <label>Danh mục cha</label>
						    <select class="form-control select2 col-md-4" name="cate_parent">
						    	<option value="">Không</option>
						        @foreach ($cates as $cate)
								<option value="{{ $cate->id }}"
								@if ($cate->id == old('cate_parent'))
								selected="" 
								@endif
								>{{ $cate->cate_name }}</option>
						        @endforeach
						    </select>
						</div>
			            <div class="form-group">
			                <label for="Catedesc">Mô tả</label>
			                <textarea id="Catedesc" name="cate_desc" class="form-control" placeholder="Nhập vào mô tả ...">{{ old('cate_desc') }}</textarea>
			            </div>
			            <div class="form-group">
			                <label for="cate_keyword">Từ khóa</label>
			                <textarea id="cate_keyword" name="cate_keyword" class="form-control" placeholder="Nhập vào các keyword của danh mục cách nhau bởi dấu phẩy">{{ old('cate_keyword') }}</textarea>
			            </div>
			            <div class="form-group">
		                    <div class="form-check">
		                      	<input class="form-check-input" name="cate_active" id="notShow" type="radio" value="0"
								@if(!old('cate_active'))
								checked 
								@endif
		                      	>
		                      	<label class="form-check-label" for="notShow">Không hiển thị</label>
		                    </div>
		                    <div class="form-check">
		                      	<input class="form-check-input" name="cate_active" id="showMenu" type="radio" value="1"
								@if (old('cate_active'))
								checked 
								@endif
		                      	>
		                      	<label class="form-check-label" for="showMenu">Hiển thị trên Menu</label>
		                    </div>
	                  	</div>
	                  	<div class="form-group">
		                    <label for="cateOrder">Thứ tự ưu tiên</label>
		                    <input type="text" class="form-control" value="{{ old('cate_order') }}" id="cateOrder" name="cate_order" placeholder="Nhập số thứ tự ưu tiên hiển thị ...">
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
<script type="text/javascript">
	window.onbeforeunload = function () {
 		return 'Any string';
	};
	function generateSlug() {
		let str = $("#nameCate").val()
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		})
		$.ajax({
			url: '/admin/blog/generate/slug',
			type: 'GET',
			data: {
				str: str,
				type: 1
			},
			success: function(res) {
				if (res.success) {
					$("#slugCate").val(res.slug);
				} else {
					$.notify(res.message, 'error');
				}
			}
		})
	}
	$(function () {
		$('.select2').select2()
    });
</script>
@endsection