@extends('admin.layout')
@section('title', 'Bài viết Blog')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admins/plugins/iCheck/all.css') }}">
@endsection
@section('content')
<section class="content">
	<div class="container-fluid">
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
	    	<div class="col-12">
    			<div class="row">
	    			<div class="col-4">
		    			<div class="form-group">
		    				<label>Chọn danh mục</label>
		    				<select class="form-control" id="cateFilter">
		    					<option value="">Tất cả danh mục</option>
		    					@php
		    					if ($cate) {
		    						show_cates($cates, $cate);
		    					} else {
		    						show_cates($cates);
		    					}
		    					@endphp
		    				</select>
		    			</div>
	    			</div>
	    			<div class="col-4">
	    				<div class="form-group">
	    					<label>Chọn khoảng thời gian</label>
	    					<input type="text" class="form-control" id="timeRanger">
	    					
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	        <div class="col-12">
	            <div class="card">
	                <div class="card-header">
	                    <h3 class="card-title">Thống kê bài viết</h3>

	                    <div class="card-tools">
	                    	<form action="{{ route('blog.all.post') }}" id="formFilterPost" method="get">
	                    		<input type="hidden" id="timeFilter1" name="timeFrom">
	                    		<input type="hidden" id="timeFilter2" name="timeTo">
		                        <div class="input-group input-group-sm" style="width: 150px;">
		                        	<input type="hidden" name="cate" id="cateForFilter" value="">
		                            <input type="text" value="{{ $keyword }}" name="keyword" class="form-control float-right" placeholder="Search">

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
	                            <th>Tiêu đề</th>
	                            <th>Ảnh</th>
	                            <th>Danh mục</th>
	                            <th>Tác giả</th>
	                            <th>Thời gian (Publish)</th>
	                            <th>Trạng thái</th>
	                            <th>Active</th>
	                        </tr>
	                        @foreach ($posts as $post)
	                        <tr>
	                            <td>
	                            	{{$post->post_title}}
	                            	<div class="actionPostBox">
	                            		<ul class="lsAcBx">
	                            			<li>
	                            				<a href="{{ asset($post->slugTxt()) }}" target="_blank" class="itemAc">Xem</a>
	                            			</li>
	                            			<li>
	                            				<a href="javascript:;" onclick="redirectUrl('{{ route('blog.edit.post', $post->id) }}')" class="itemAc">Sửa</a>
	                            			</li>
	                            			<li>
	                            				<a href="javascript:;" onclick="destroyItem('{{ route('blog.delete.post', $post->id) }}')" class="itemAc">Xóa</a>
	                            			</li>
	                            		</ul>
	                            	</div>
	                            </td>
	                            <td style="width: 100px; display: inline-block;">
	                            	<img style="width: 100%" src="{{ asset($post->post_thumb) }}">
	                            </td>
	                            <td>
	                            	{{ $post->cate1()->first() ? $post->cate1()->first()->cate_name : '' }}
	                            	{{ $post->cate2()->first() ? ' >> ' . $post->cate2()->first()->cate_name : '' }}
	                            </td>
	                            <td>{{$post->author()->first() ? $post->author()->first()->email : ''}}</td>
	                            <td style="width: 100px; display: inline-block;">{{date('d-m-Y H:i:s', strtotime($post->post_time_schedule))}}</td>
	                            <td>
	                                @if($post->is_draft)
	                                <span class="badge bg-default">Draft</span>
	                                @elseif(strtotime($post->post_time_schedule) <= time())
	                                <span class="badge bg-success">Active</span>
	                                @else
	                                <span class="badge bg-warning">Schedule</span>
	                                @endif
	                            </td>
	                            <td class="text-center">
	                            	<label class="switch">
								  		<input type="checkbox"
										@if ($post->post_active)
										checked="" 
										@endif
										onchange="changeActivePost($(this), {{ $post->id }})" 
								  		>
								  		<span class="slider round"></span>
									</label>
	                            </td>
	                        </tr>
	                        @endforeach
	                    </table>
	                </div>
	                <!-- /.card-body -->
	                <div class="card-footer clearfix">
	                    {{ $posts->links() }}
	                </div>
	                <div class="card-footer clearfix"> 
                        Tổng {{ $posts->total() }}
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admins/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('admins/plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$("#cateFilter").select2();
	});
	$("#cateFilter").change(function() {
		let cate = $(this).val();
		$("#cateForFilter").val(cate);
		$("#formFilterPost").submit();
	})
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
  		checkboxClass: 'icheckbox_flat-green',
  		radioClass   : 'iradio_flat-green'
    })
	let timeFrom = "{{ $timeFrom }}";
	let timeTo = "{{ $timeTo }}";
	if (timeFrom && timeTo) {
		$('#timeRanger').daterangepicker({
		    showDropdowns: true,
		    timePicker24Hour: true,
		    timePickerSeconds: true,
		    timePicker: true,
		    startDate: moment(timeFrom).startOf('hour'),
		    endDate: moment(timeTo).startOf('hour'),
		    locale: {
				"format": "DD/MM/YYYY HH:mm:ss",
			    "separator": " - ",
			    "applyLabel": "Đồng ý",
			    "cancelLabel": "Hủy",
			    "fromLabel": "Từ",
			    "toLabel": "đến",
			    "customRangeLabel": "Tùy chỉnh",
			    "weekLabel": "Tuần",
			    "daysOfWeek": [
			        "T2",
			        "T3",
			        "T4",
			        "T5",
			        "T6",
			        "T7",
			        "CN"
			    ],
			    "monthNames": [
			        "Th1",
			        "Th2",
			        "Th3",
			        "Th4",
			        "Th5",
			        "Th6",
			        "Th7",
			        "Th8",
			        "Th9",
			        "Th10",
			        "Th11",
			        "Th12"
			    ],
			    "firstDay": 1
		    }
	  	}, function(start, end, label) {
		    var time1 = moment(start).format('YYYY-MM-DD HH:mm:ss')
		    var time2 = moment(end).format('YYYY-MM-DD HH:mm:ss')
		    $("#timeFilter1").val(time1)
		    $("#timeFilter2").val(time2)
		    $("#formFilterPost").submit()
	  	});
	} else {
		$('#timeRanger').daterangepicker({
		    showDropdowns: true,
		    timePicker24Hour: true,
		    timePickerSeconds: true,
		    timePicker: true,
		    locale: {
				"format": "DD/MM/YYYY HH:mm:ss",
			    "separator": " - ",
			    "applyLabel": "Đồng ý",
			    "cancelLabel": "Hủy",
			    "fromLabel": "Từ",
			    "toLabel": "đến",
			    "customRangeLabel": "Tùy chỉnh",
			    "weekLabel": "Tuần",
			    "daysOfWeek": [
			        "T2",
			        "T3",
			        "T4",
			        "T5",
			        "T6",
			        "T7",
			        "CN"
			    ],
			    "monthNames": [
			        "Th1",
			        "Th2",
			        "Th3",
			        "Th4",
			        "Th5",
			        "Th6",
			        "Th7",
			        "Th8",
			        "Th9",
			        "Th10",
			        "Th11",
			        "Th12"
			    ],
			    "firstDay": 1
		    }
	  	}, function(start, end, label) {
		    var time1 = moment(start).format('YYYY-MM-DD HH:mm:ss')
		    var time2 = moment(end).format('YYYY-MM-DD HH:mm:ss')
		    $("#timeFilter1").val(time1)
		    $("#timeFilter2").val(time2)
		    $("#formFilterPost").submit()
	  	});
	}
    

	function redirectUrl(url)
	{
		window.location.replace(url)
	}

	function destroyItem(url)
	{
		if (confirm('Bạn có chắc muốn xóa bài viết')) {
			redirectUrl(url)
		}
	}

	function changeActivePost(obj, id)
	{
		let idPost = parseInt(id)
		if (obj.is(":checked")) {
			activePost(idPost)
		} else {
			deactivate(idPost)
		}
	}

	function activePost(id)
	{
		let idPost = parseInt(id)
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		})
		$.ajax({
			url: '/admin/blog/post/active/' + idPost,
			type: 'POST',
			success: function(res) {
				if (res.success) {
					$.notify(res.message, 'success');
				} else {
					$.notify(res.message, 'error');
				}
			}
		})
	}

	function deactivate(id)
	{
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		})
		$.ajax({
			url: '/admin/blog/post/deactivate/' + id,
			type: 'POST',
			success: function(res) {
				if (res.success) {
					$.notify(res.message, 'success');
				} else {
					$.notify(res.message, 'error');
				}
			}
		})
	}
</script>
@endsection
