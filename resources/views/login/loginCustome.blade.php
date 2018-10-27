<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<form class="form" action="{{ asset('login-submit') }}" method="post">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Tên đăng nhập</label>
						<input type="text" class="form-control" placeholder="Nhập tên đăng nhập" name="username">
					</div>
					<div class="form-group">
						<label>Mật khẩu</label>
						<input type="password" class="form-control" placeholder="Nhập password" name="password">
					</div>
					<button type="submit" class="btn btn-primary">Đăng nhập</button>
				</form>
			</div>
		</div>
	</div>

</body>
</html>