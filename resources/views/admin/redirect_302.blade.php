@extends('admin.layout')
@section('title', 'Cài đặt chuyển hướng')
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
	                    <h3 class="card-title">Danh sách chuyển hướng</h3>
	                </div>
	                <!-- /.card-header -->
	                <div class="card-body table-responsive p-0">
	                	@php
                        	$index = 0;
                        @endphp
	                    <table class="table table-hover">
	                        <tr>
	                            <th style="width: 5%">STT</th>
	                            <th style="width: 50%" class="one-row">Link cần chuyển hướng</th>
	                            <th style="width: 50%" class="one-row">Link chuyển hướng</th>
	                            <th style="width: 15%">Tác vụ</th>
	                        </tr>
	                        @foreach ($links as $link)
	                        @php
	                        	$index = $index + 1;
	                        @endphp
	                        <tr>
	                            <td style="width: 5%">
	                            	{{ $index }}
	                            </td>
	                            <td style="width: 50%" class="one-row">
	                            	{{ base64_decode($link->from_link) }}
	                            </td>
	                            <td style="width: 50%" class="one-row">
									{{ base64_decode($link->to_link) }}
	                            </td>
	                            <td style="width: 15%">
	                            	<a href="{{ route('editRedirect302', $link->id) }}">
	                            		<span class="badge bg-primary">
		                            		<i class="fa fa-edit"></i>
		                            	</span>
	                            	</a>
	                            	<a href="javascript:;" onclick="destroyLink({{ $link->id }})" data-router="{{ route('destroyRedirect302', $link->id) }}">
		                            	<span class="badge bg-danger">
		                            		<i class="fa fa-close"></i>
		                            	</span>
		                            	<form id="destroy_{{ $link->id }}" action="{{ route('destroyRedirect302', $link->id) }}" method="post">
		                            		<input type="hidden" name="_method" value="delete" />
		                            		{{ csrf_field() }}
		                            	</form>
	                            	</a>
	                            </td>
	                        </tr>
	                        @endforeach
	                    </table>
	                </div>
	                <!-- /.card-body -->
	                <div class="card-footer clearfix">
	                    {{ $links->links() }}
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
	function destroyLink(id)
	{
		if (confirm('Bạn có chắc muốn xóa chuyển hướng này')) {
			$('#destroy_' + id).submit()
		}
	}
</script>
@endsection