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
		<div class="col-md-6">
			<h3>Kết quả quét:</h3>
            @foreach($uids as $uid)
            <div class="text-default">{{ $uid->uid }}</div>
            @endforeach
            @if($countPhone > 0)
            <div class="alert alert-dismissible alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fa fa-ban"></i> Cảnh báo!</h5>
                Tìm thấy {{ $countPhone }} số điện thoại
            </div>
            @endif
		</div>
	</div>
	@if($count > 0)
	<div class="row">
		<h3>Đang quét lấy số ĐT</h3>
		<div id="scanning" style="padding-top: 20px">
            <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
	</div>
	<div class="row">
		<form action="{{ route('uid.scan.phone') }}" method="get" id="scanningForm">
            <div class="form-group col-3">
                <label>Số lượng UID Mỗi lần quét</label>
                <input type="number" class="form-control" name="count_of_time" min="1" disabled value="{{ $countOfTime }}">
            </div>
            <div class="form-group col-3">
                <label>Khoảng thời gian giữa mỗi lần quét</label>
                <input type="number" class="form-control" name="time_range" min="1" disabled value="{{ $timeRange }}">
            </div>
        </form>
	</div>
	@endif
</div>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
@if($count > 0)
<script type="text/javascript">
	$(document).ready(function() {
        setTimeout(() => {
            $("#scanningForm").submit()
        }, {{ $timeRange * 1000 }})
    })
</script>
@endif
</body>
</html>