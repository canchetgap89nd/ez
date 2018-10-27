@extends('blog.layout')
@section('title', $post->post_title)
@section('metaExtra')
<meta name="description" content="{{ $post->post_desc ? $post->post_desc : 'Phần mềm quản lý Fanpage miễn phí, hỗ trợ quản lý bán hàng, chăm sóc khách hàng tốt nhất. Quản lý số lượng lớn fanpage facebook một cách chuyên nghiệp. Giúp chốt sale nhanh nhất có thể đem lại doanh thu lớn cho người bán hàng' }}">
<meta name="keywords" content="{{ $post->post_keyword ? $post->post_keyword : 'phần mềm bán hàng, phần mềm chốt sale, chốt sale, chotsale, hỗ trợ bán hàng, bán hàng trên facebook, phần mềm quản lý Fanpage miễn phí' }}">
<meta property="og:url" content="{{ asset($post->slugTxt()) }}" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $post->post_title }}" />
<meta property="og:description" content="{{ $post->post_desc }}" />
<meta property="og:image" content="{{ asset($post->post_thumb) }}"/>
@endsection
@section('content')
<div class="breadcrumb-block">
	<ul class="breadcrumb">
  		<li><a href="{{ asset('/') }}" title="Đi tới trang chủ ứng dụng" alt="Đi tới trang ứng dụng Chốt Sale">Trang chủ</a></li>
  		<li><a href="{{ asset('blog') }}" title="Blog Chốt Sale" alt="Blog Chốt Sale">Blog</a></li>
  		<li><a href="{{ asset($cate1->slugTxt()) }}" title="{{ $cate1->cate_name }}" alt="{{ $cate1->cate_name }}">{{ $cate1->cate_name }}</a></li>
  		@if($cate2)
  		<li><a href="{{ asset($cate2->slugTxt()) }}" title="{{ $cate2->cate_name }}" alt="{{ $cate2->cate_name }}">{{ $cate2->cate_name }}</a></li>
  		@endif
	</ul>
</div>
<div class="contentBlock">
	<h1 class="ttPost">
		{{$post->post_title}}
	</h1>
	<div class="underTitle">
		<div class="acRight">
			<span class="txtUnderTt pull-left date-block">
				Đã đăng vào ngày {{ date('d/m/Y H:i', strtotime($post->post_time_schedule)) }}
			</span>
			<span class="txtUnderTt pull-left eye-block">
				<i class="fa fa-eye"></i>
				{{$post->views}} lượt xem
			</span>
		</div>
		<div class="acLeft">
			<a href="https://twitter.com/share?url={{ asset($post->slugTxt()) }}" target="_blank" class="shareOval pull-right">
				<i class="fa fa-twitter" aria-hidden="true"></i>
			</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u={{ asset($post->slugTxt()) }}" target="_blank" class="shareOval pull-right">
				<i class="fa fa-facebook" aria-hidden="true"></i>
			</a>
			<span class="txtUnderTt pull-right share-block">
				Chia sẻ
			</span>
		</div>
	</div>
	<div class="contentPost">
		<p>
			{!! $post->post_content !!}
		</p>
	</div>
	<div class="underContent">
		<div class="socialShareUnder">
			<div class="fb-like" data-href="{{ asset($post->slugTxt()) }}" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
		</div>
		<div class="socialComment">
			<div class="fb-comments" data-href="{{ asset($post->slugTxt()) }}" data-numposts="2"></div>
		</div>
	</div>
	<h3 class="titleBlock">
		Bài viết liên quan
	</h3>
	<div class="relateBlock">
		<div class="row">
			@foreach($relatePosts as $post)
			<div class="col-sm-4">
				<div class="postItem">
					<div class="headPostIt">
						<a href="/{{ $post->slugTxt() }}" alt="{{ $post->post_title }}" title="{{ $post->post_title }}">
							<img class="imageThumb thumbMedium" src="{{ asset($post->post_thumb) }}" alt="{{ $post->post_title }}" title="{{ $post->post_title }}">
						</a>
					</div>
					<div class="bodyPostIt">
						<h3 class="titlePostIt titlePost" alt="{{ $post->post_title }}" title="{{ $post->post_title }}">
							<a href="/{{ $post->slugTxt() }}" alt="{{ $post->post_title }}" title="{{ $post->post_title }}">
								{{ $post->post_title }}
							</a>
						</h3>
						<div class="descPostIt">
							<div class="lsIt">
								<i class="fa fa-circle" aria-hidden="true"></i>
								<span class="txtLsPostIt">{{ date('d/m/Y H:i', strtotime($post->post_time_schedule)) }}</span>
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
		</div>
	</div>
	<input type="hidden" id="routerUrl" value="{{ route('upViewsBlog', $post->id) }}">
</div>
@endsection