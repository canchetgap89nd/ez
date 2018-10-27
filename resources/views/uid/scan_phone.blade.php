<!DOCTYPE html>
<html>
<head>
	<title>Quét số điện thoại</title>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" href="{{ asset('/images/chotsale_favicon.png') }}">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,700,700i" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('admins/plugins/font-awesome/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}" />
	<meta name="robots" content="noindex"/>
</head>
<body>
<div class="container">
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
	</div>
	<div class="row">
		<h3>Cài đặt quét</h3>
		<form action="{{ route('uid.scan.phone') }}" method="get">
			<div class="form-group">
				<label>Nhập số UID trong một lần quét</label>
				<input type="number" class="form-control" name="count_of_time" min="1">
			</div>
			<div class="form-group">
                <label>Nhập khoảng thời gian giữa mỗi lần quét (s)</label>
                <input type="number" class="form-control" name="time_range" min="1">
            </div>
            <button class="btn btn-success" type="submit">Bắt đầu quét</button>
		</form>
	</div>
</div>
</body>
</html>