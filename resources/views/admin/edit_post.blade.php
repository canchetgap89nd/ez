@extends('admin.layout')
@section('title', 'Chỉnh sửa bài viết')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/fancybox/jquery.fancybox.min.css') }}" type="text/css"  media="screen">
<script src="{{ asset('plugins/fancybox/jquery.fancybox.min.js') }}"></script>
@endsection
@section('content')
<section class="content">
	<div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cập nhật bài viết</h1>
                </div>
            </div>
        </div>
    </div>
	<form id="formPostInfo" action="{{ route('blog.update.post', $post->id) }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" name="typeSave" id="typeSave" value="1">
		<input type="hidden" name="id" value="{{ $post->id }}">
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
	        <div class="col-md-8">
	            <div class="card card-info card-outline">
	                <div class="card-header">
	                    <h3 class="card-title">
	                		Chi tiết bài viết
	              		</h3>
	                    <!-- tools box -->
	                    <div class="card-tools">
	                        <button type="button" class="btn btn-tool btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                            <i class="fa fa-minus"></i>
	                        </button>
	                    </div>
	                    <!-- /. tools -->
	                </div>
	                <!-- /.card-header -->
	                <div class="card-body">
						<div class="mb-3">
							<div class="form-group has-error">
							    <label class="control-label" for="titlePost">
							    	Tiêu đề bài viết
							    </label>
							    <input type="text" name="postTitle" class="form-control" id="titlePost" placeholder="Nhập tiêu đề ..." value="{{ $post->post_title }}">
							</div>
						</div>
						<div class="mb-3">
							<div class="input-group mb-3">
							    <div class="input-group-prepend">
							        <span class="input-group-text">https://chotsale.com.vn/</span>
							    </div>
							    <input type="text" id="slugCate" name="postSlug" class="form-control" placeholder="Đường dẫn tĩnh" value="{{ $post->slug()->first()->slug }}">
							</div>
						</div>
						<div class="mb-3">
							<div class="form-group has-error">
							    <label class="control-label" for="titlePost">
							    	Mô tả ngắn
							    </label>
							    <textarea rows="2" class="form-control" name="postDesc" placeholder="Nhập vào mô tả ngắn ...">{{ $post->post_desc }}</textarea>
							</div>
						</div>
						<div class="mb-3">
							<div class="form-group has-error">
							    <label class="control-label" for="post_keyword">
							    	Từ khóa
							    </label>
							    <textarea rows="2" class="form-control" id="post_keyword" name="post_keyword" placeholder="Nhập vào keyword của bài viết cách nhau bởi dấu phẩy">{{ $post->post_keyword }}</textarea>
							</div>
						</div>
	                    <div class="mb-3">
	                    	<label class="control-label" for="editor1">
						    	Nội dung
						    </label>
	                        <textarea id="editor1" name="postContent" style="width: 100%">
	                        	{{ $post->post_content }}
	                        </textarea>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-4">
	            <div class="card card-info card-outline">
	                <div class="card-header">
	                    <h3 class="card-title">
	                		Thông tin bài viết
	              		</h3>
	                    <!-- tools box -->
	                    <div class="card-tools">
	                        <button type="button" class="btn btn-tool btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                            <i class="fa fa-minus"></i>
	                        </button>
	                    </div>
	                    <!-- /. tools -->
	                </div>
	                <!-- /.card-header -->
	                <div class="card-body">
						<div class="mb-3">
							<div class="form-group">
							    <label>Danh mục</label>
							    <select class="form-control select2" name="postCate" style="width: 100%;">
							        @php
							        $cate2 = $post->cate2()->first();
							        $cate1 = $post->cate1()->first();
							        if ($cate2) {
							        	show_cates($cates, $cate2->id);
							        } else 
							        if ($cate1) {
							        	show_cates($cates, $cate1->id);
							        } else {
							        	show_cates($cates);
							        }
							        @endphp
							    </select>
							</div>
						</div>
	                    <div class="mb-3">
							<div class="form-group">
			              		<label>Ảnh đại diện</label>
			              		<div class="text-center boxImgShow">
									<img id="imagePreview" src="{{ $post->post_thumb ? asset($post->post_thumb) : asset('images/no-image.png') }}" alt="Chọn ảnh đại diện" />
			              		</div>
			              		<div class="text-left">
			              			<a href="{{ asset('plugins/tinymce/filemanager/dialog.php?type=1&field_id=thumb_article') }}" class="btn_add_pic">Thêm ảnh</a>
			              		</div>
			              		<input type="hidden" value="{{ $post->post_thumb }}" name="postThumb" id="thumb_article">
			              	</div>
						</div>
						<div class="mb-3">
							<div class="form-group">
							    <label>Đặt giờ:</label>
								<div class="input-group">
				                  	<input type="text" value="{{ date('d/m/Y H:i:s', strtotime($post->post_time_schedule)) }}" class="form-control" id="reservation">
				                  	<div class="input-group-append">
				                    	<span class="input-group-text"><i class="fa fa-calendar"></i></span>
				                  	</div>
				                  	<input type="hidden" value="{{ $post->post_time_schedule }}" name="postSchedule" id="timeSchedule">
				                </div>
							</div>
						</div>
						<div class="mb-3">
							<div class="form-group">
								<button type="submit" class="btn btn-primary">Cập nhật</button>
							</div>
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
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
	tinymce.init({
	    selector: "#editor1",theme: "modern", height: 400,
	    plugins: [
	         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
	         "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
	         "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
	   	],
	   	toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect | sizeselect | fontselect |  fontsizeselect",
	   	toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
	   	image_advtab: true,
	   
	   	external_filemanager_path:"{{ asset('plugins/tinymce/filemanager') }}/",
	   	filemanager_title:"Thư viện" ,
	   	external_plugins: { "filemanager" : "{{ asset('plugins/tinymce') }}/filemanager/plugin.min.js"},
	   	relative_urls : false,
		remove_script_host : false,
		fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
		font_formats: 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats'
 	});
</script>
<script type="text/javascript">

	window.onbeforeunload = function () {
 		return 'Any string';
	};

	function responsive_filemanager_callback(field_id)
	{
		var url=jQuery('#'+field_id).val();
		$('#imagePreview').attr('src', url);
		$("#imagePreview").show();
		$(".noImage").hide();
		parent.jQuery.fancybox.getInstance().close();
	}

	function generateSlug()
	{
		let str = $("#titlePost").val()
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
				type: 2
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
		$('.btn_add_pic').fancybox({
			'type'		: 'iframe'
	    });
	    $('#reservation').daterangepicker({
		    singleDatePicker: true,
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
		    var time = moment(start).format('YYYY-MM-DD HH:mm:ss')
		    $("#timeSchedule").val(time)
	  	});
    });
</script>
@endsection