@extends('blog.layout')
@section('title', $cate->cate_name)
@section('metaExtra')
<meta name="description" content="{{ $cate->cate_desc ? $cate->cate_desc : 'Phần mềm quản lý Fanpage miễn phí, hỗ trợ quản lý bán hàng, chăm sóc khách hàng tốt nhất. Quản lý số lượng lớn fanpage facebook một cách chuyên nghiệp. Giúp chốt sale nhanh nhất có thể đem lại doanh thu lớn cho người bán hàng' }}">
<meta name="keywords" content="{{ $cate->cate_keyword ? $cate->cate_keyword : 'phần mềm bán hàng, phần mềm chốt sale, chốt sale, chotsale, hỗ trợ bán hàng, bán hàng trên facebook, phần mềm quản lý Fanpage miễn phí' }}">
@endsection
@section('content')
<div class="breadcrumb-block">
	<ul class="breadcrumb">
  		<li>
  			<a href="{{ asset('/') }}" title="Đi tới trang ứng dụng Chốt Sale">
				Trang chủ
			</a>
  		</li>
  		<li><a href="{{ asset('blog') }}" title="Blog Chốt sale" alt="Blog Chốt Sale">Blog</a></li>
  		@if($parent)
  		<li><a href="{{ asset($parent->slugTxt()) }}" title="{{ $parent->cate_name }}" alt="{{ $parent->cate_name }}">{{ $parent->cate_name }}</a></li>
  		@endif
  		<li><a href="javascript:;" title="{{ $cate->cate_name }}" alt="{{ $cate->cate_name }}">{{ $cate->cate_name }}</a></li>
	</ul>
</div>
<h3 class="titleBlock">
	Chuyên mục: <strong>{{ $cate->cate_name }}</strong>
</h3>
<div class="row">
	@foreach($posts as $post)
	<div class="col-sm-6">
		<div class="postItem">
			<div class="headPostIt">
				<a href="{{ asset($post->slugTxt()) }}" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
					<img class="imageThumb thumbBig" title="{{ $post->post_title }}" alt="{{ $post->post_title }}" src="{{ asset($post->post_thumb) }}">
				</a>
				<div class="wrappLabel">
					<div class="catLabel">
						<span>{{ $post->cate2()->first() ? $post->cate2()->first()->cate_name : $post->cate1()->first()->cate_name }}</span>
					</div>
				</div>
			</div>
			<div class="bodyPostIt">
				<h3 class="titlePostIt titlePost" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
					<a href="{{ asset($post->slugTxt()) }}" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
						{{$post->post_title}}
					</a>
				</h3>
				<div class="descPostIt">
					<div class="lsIt">
						<i class="fa fa-circle" aria-hidden="true"></i>
						<span class="txtLsPostIt">{{ date('d-m-Y H:i:s', strtotime($post->post_time_schedule)) }}</span>
					</div>
					<div class="lsIt">
						<i class="fa fa-circle" aria-hidden="true"></i>
						<span class="txtLsPostIt">{{$post->views}} lượt xem</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
	<div class="col-xs-12">
		<div class="paginateBox">
			{{ $posts->links() }}
		</div>
	</div>
</div>
@endsection