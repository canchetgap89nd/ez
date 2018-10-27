<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex"/>
	<title>@yield('title')</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/OwlCarousel/owl.carousel.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/OwlCarousel/owl.theme.default.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('plugins/dropzone/dropzone.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('plugins/AwesomeNotify/style.css') }}" type="text/css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.6.2/slimselect.min.css" rel="stylesheet"></link>
	<link rel="shortcut icon" type="image/png" href="{{ asset('images/chotsale_favicon.png') }}"/>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	@yield('head')
	<link rel="stylesheet" type="text/css" href="{{ asset('client/css/main.css') }}">
	<link rel="stylesheet" href="{{ asset('client/css/setup.css') }}" type="text/css">
</head>
<body>
	<div id="root">
		
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
	<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
	<script type="text/javascript" src="{{ asset('plugins/notifyjs/notify.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('plugins/OwlCarousel/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('plugins/dropzone/dropzone.min.js') }}"></script>
	@yield('scripts')
	<script type="text/javascript" src="{{ asset('client/js/main.js') }}"></script>

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