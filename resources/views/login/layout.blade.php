<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="{{ asset('client/css/login.css') }}">
	@yield('head')
</head>
<body>
	<div>
		<div class="head-div">
			<div class="inner-head text-center">
				<h3>@yield('titleHead')</h3>
				<div class="boxLogout">
					<a href="{{ route('logout') }}" class="hr-logout" title="Đăng xuất">
						<i class="fa fa-sign-out" aria-hidden="true"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="body-div">
			@yield('content')
		</div>
	</div>

	<script type="text/javascript" src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('plugins/notifyjs/notify.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('client/js/login.js') }}"></script>

@yield('scripts')

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115626442-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-115626442-1');
	</script>
</body>
</html>