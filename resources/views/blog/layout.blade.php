<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta property="fb:app_id" content="933735970113789" />
	<link rel="icon" href="{{ asset('/images/chotsale_favicon.png') }}">
	<meta name="robots" content="all" />
	@yield('metaExtra')
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('blogg/css/main.css') }}">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
</head>
<body>
<div id="fb-root"></div>
<div class="page">
	<div class="header">
		<div class="bg-header">
			
		</div>
	</div>
	<div class="navig">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h3 class="titleNav">
						CHUYÊN MỤC
					</h3>
				</div>
				<input type="checkbox" id="checkboxToggle">
				<div class="navBox">
					@foreach($categories as $cate)
					<div class="col-sm-4">
						<div class="navItem">
							<a href="{{ asset($cate->slugTxt()) }}" title="{{ $cate->cate_name }}" alt="{{ $cate->cate_name }}" class="lnNav txtNav">{{ $cate->cate_name }}</a>
						</div>
						<ul class="menu-sub">
							@foreach($cate->childs()->get() as $child)
							<li>
								<i class="fa fa-circle" aria-hidden="true"></i>
								<a href="{{ asset($child->slugTxt()) }}" title="{{ $child->cate_name }}" alt="{{ $child->cate_name }}" class="navItem lnMenuSb txtNav">{{ $child->cate_name }}</a>
							</li>
							@endforeach
						</ul>
					</div>
					@endforeach
				</div>
				<div class="col-lg-12 text-center">
					<div class="underNav">
						<label for="checkboxToggle">
							<i class="fa fa-caret-down btSlideUpNav"></i>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="bodyPage">
		<div class="container">
			<div class="row">
				<div class="col-lg-9">
					@yield('content')
				</div>
				<div class="col-lg-3">
					<div class="slideBar">
						<div class="searchBlock">
							<form method="get" action="{{ route('blog.home') }}" class="searchBox">
						  		<input type="text" name="keyword" value="{{ isset($keyword) ? $keyword : '' }}" class="textbox" placeholder="Search">
						  		<input title="Search" value="" type="submit" class="button">
							</form>
						</div>
						<div class="socialBlock">
							<div class="fb-page" data-href="https://www.facebook.com/chotsale.com.vn/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/chotsale.com.vn/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/chotsale.com.vn/">Chốt Sale - Quản lý Fanpage miễn phí</a></blockquote></div>
						</div>
						<div class="mostBlock">
							<h3 class="ttBlock">
								Tin xem nhiều
							</h3>
							<div class="lsMost">
								@foreach($postMostView as $post)
								<div class="itemMost">
									<div class="imgMost">
										<a href="{{ asset($post->slugTxt()) }}" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
											<img class="thumbSmall imageThumb" title="{{ $post->post_title }}" alt="{{ $post->post_title }}" src="{{ asset($post->post_thumb) }}">
										</a>
									</div>
									<div class="infoMost">
										<h3 class="ttPostMost titlePostIt" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
											<a href="{{ asset($post->slugTxt()) }}" title="{{ $post->post_title }}" alt="{{ $post->post_title }}">
												{{ $post->post_title }}
											</a>
										</h3>
										<p class="desMost">
											{{ date('d/m/Y H:i', strtotime($post->post_time_schedule)) }}
										</p>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<div class="taskbar">
			<div class="innerTsBar">
				<h1 class="ttTaskBar">
					Quản lý Fanpage <span class="ttUper">miễn phí trọn đời</span>
				</h1>
				<a href="https://chotsale.com.vn/" title="Đi đến ứng dụng" class="btJoin">
					Vào ứng dụng
				</a>
			</div>
		</div>
		<div class="contactBlock">
			<p class="addressRow">
				Liên hệ với chúng tôi
			</p>
			<p class="addressRow">
				Trụ sở chính: số 102, phố Thái Thịnh, phường Trung Liệt, quận Đống Đa, Hà Nội
			</p>
			<p class="addressRow">
				Hotline: (024)73 050 105 - 01658 299 244
			</p>
		</div>
	</div>
</div>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115626442-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-115626442-1');
</script>
<script type="text/javascript" src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('blogg/js/main.js') }}"></script>
<script type="text/javascript" src="{{ asset('blogg/js/post.js') }}"></script>
{{-- vchat --}}
<script lang="javascript">(function() {var pname = ( (document.title !='')? document.title : ((document.querySelector('h1') != null)? document.querySelector('h1').innerHTML : '') );var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async=1; ga.src = '//live.vnpgroup.net/js/web_client_box.php?hash=f2e746dc7471f4784de4c16042d19bf8&data=eyJzc29faWQiOjUzNDEyMjUsImhhc2giOiJmNjk0MDY5MDJlYTQwMjdkOGEwZjc2OGMxMjA2NzYzYiJ9&pname='+pname;var s = document.getElementsByTagName('script');s[0].parentNode.insertBefore(ga, s[0]);})();</script>
</body>
</html>