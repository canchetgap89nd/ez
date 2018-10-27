@extends('admin.layout')
@section('title', 'Danh mục Blog')
@section('content')
<section class="content">
	<div class="container-fluid">
	    <div class="row">
	    	@if(session('message'))
			<div class="col-md-12">
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
	        <div class="col-12">
	            <div class="card">
	                <div class="card-header">
	                    <h3 class="card-title">Thống kê danh mục</h3>

	                    <div class="card-tools">
	                    	<form action="{{ route('blog.all.category') }}" method="get">
		                        <div class="input-group input-group-sm" style="width: 150px;">
		                            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control float-right" placeholder="Search">

		                            <div class="input-group-append">
		                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
		                            </div>
		                        </div>
	                    	</form>
	                    </div>
	                </div>
	                <!-- /.card-header -->
	                <div class="card-body table-responsive p-0">
	                    <table class="table table-hover">
	                        <tr>
	                            <th>Tên danh mục</th>
	                            <th>Bài viết</th>
	                            <th>Mô tả danh mục</th>
	                            <th>Danh mục cha</th>
	                            <th>Đường dẫn</th>
	                            <th>Thứ tự ưu tiên</th>
	                            <th>Trạng thái</th>
	                            <th>Tác vụ</th>
	                        </tr>
	                        @foreach ($cates as $cate)
	                        <tr>
	                            <td>{{$cate->cate_name}}</td>
	                            <td>{{$cate->posts()->count()}}</td>
	                            <td>{{$cate->cate_desc}}</td>
	                            <td>{{$cate->parent()->first() ? $cate->parent()->first()->cate_name : ''}}</td>
	                            <td>{{$cate->slug()->first() ? $cate->slug()->first()->slug : ''}}</td>
	                            <td>{{ $cate->cate_order }}</td>
	                            <td>
	                            	@if($cate->cate_active)
	                                <span class="badge bg-success">Active</span>
	                                @else
	                                <span class="badge bg-default">Not Active</span>
	                                @endif
	                            </td>
	                            <td>
	                                <button onclick="redirectUrl('{{ route('blog.edit.category', $cate->id) }}')" type="button" class="btn btn-block btn-outline-primary btn-sm">
	                                	Sửa
	                                </button>
	                                <button type="button" onclick="destroy('{{ route('blog.delete.category', $cate->id) }}')" class="btn btn-block btn-outline-danger btn-sm">Xóa</button>
	                            </td>
	                        </tr>
	                        @endforeach
	                    </table>
	                </div>
	                <!-- /.card-body -->
	                <div class="card-footer clearfix">
	                    {{ $cates->links() }}
	                </div>
	                <div class="card-footer clearfix"> 
                        Tổng {{ $cates->total() }}
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('admins/plugins/fastclick/fastclick.js') }}"></script>
<script type="text/javascript">
	function redirectUrl(url)
	{
		window.location.replace(url);
	}

	function destroy(url)
	{
		if (confirm('Bạn có chắc muốn xóa danh mục')) {
			redirectUrl(url)
		}
	}
</script>
@endsection
