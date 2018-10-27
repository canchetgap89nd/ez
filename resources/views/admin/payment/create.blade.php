@extends('admin.layout')
@section('title', 'Cập nhật hóa đơn')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/iCheck/all.css') }}">
@endsection
@section('content')
<section class="content">
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
    <form role="form" action="{{ route('payment.store') }}" method="POST">
    	<div class="row">
			<div class="col-md-6">
				<div class="card card-primary">
				    <div class="card-header">
				        <h3 class="card-title">Thông tin hóa đơn</h3>
				    </div>
				    <!-- /.card-header -->
				    <!-- form start -->
				    	{{csrf_field()}}
			        <div class="card card-body">
			        	<div class="form-group">
		                    <label>ID người dùng: </label>
		                    <input type="number" class="form-control" placeholder="Nhập ID người dùng" name="user_id">
	                  	</div>
	                  	<div class="form-group">
		                    <label>Gói tài khoản: </label>
		                    <select class="form-control" name="package_id">
		                    	@foreach($packages as $pack)
								<option value="{{ $pack->id }}">{{ $pack->display_name }}</option>
		                    	@endforeach
		                    </select>
	                  	</div>
	                  	<div class="form-group">
			                <label>Đơn giá (VNĐ): </label>
			                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="price" min="0" value="{{ isset($packages[0]->price) ? $packages[0]->price : 0 }}">
			            </div>
			            <div class="form-group">
			                <label>Thời hạn (tháng): </label>
			                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="duration" min="1" value="0">
			            </div>
			            <div class="form-group">
			                <label>Tặng thêm (tháng): </label>
			                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="duration_bonus" min="0" value="0">
			            </div>
			            <div class="form-group">
			                <label>Tổng tiền: </label>
			                <span id="amount">0</span>
			            </div>
			        </div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card card-primary">
					<div class="card card-header">
						<h3 class="card-title">Thông tin khoản thanh toán (đơn vị: VNĐ)</h3>
					</div>
					<div class="card card-body">
						<div class="form-group">
			                <label>Tổng thanh toán: </label>
			                <span id="total_payable">0</span>
			            </div>
			            <div class="form-group">
			            	<label>Thuế</label>
			            	<input onblur="totalAfterDiscount()" value="0" type="number" name="tax" class="form-control" min="0">
			            </div>
			            <div class="form-group">
			            	<label>Khoản khác</label>
			            	<input onblur="totalAfterDiscount()" value="0" type="number" name="other_payment" class="form-control" min="0">
			            </div>
			            <div class="form-group">
			            	<label>Giảm giá</label>
			            	<input onblur="totalAfterDiscount()" value="0" type="number" name="discount" class="form-control" min="0">
			            </div>
			            <div class="form-group">
			            	<label>Tổng sau giảm giá: </label>
			            	<span id="total_after_discount">0</span>
			            </div>
			            <div class="form-group">
			            	<label class="radio-inline">
			            		<input type="radio" name="paid" value="{{1}}">
			            		Đã thanh toán
			            	</label>
			            	<label class="radio-inline">
			            		<input type="radio" name="paid" value="{{0}}" checked>
			            		Chưa thanh toán
			            	</label>
			            </div>
					</div>
					<div class="card-footer">
			            <button type="submit" class="btn btn-primary">Tạo hóa đơn</button>
			        </div>
				</div>
			</div>
    	</div>
    </form>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('admins/plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript">
	window.onbeforeunload = function () {
 		return 'Any string';
	};
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      	checkboxClass: 'icheckbox_flat-green',
      	radioClass   : 'iradio_flat-green'
    })
    function totalAfterDiscount() {
    	var price = parseInt($("input[name='price']").val());
    	var duration = parseInt($("input[name='duration']").val());
    	var durationBonus = parseInt($("input[name='duration_bonus']").val());
    	var tax = parseInt($("input[name='tax']").val());
    	var otherPay = parseInt($("input[name='other_payment']").val());
    	var discount = parseInt($("input[name='discount']").val());
    	var amount = price * duration;
    	$("#amount").text(amount);
    	var totalPay = amount + tax + otherPay;
    	$("#total_payable").text(totalPay);
    	var totalAfterDiscount = totalPay - discount;
    	$("#total_after_discount").text(totalAfterDiscount);
    }
</script>
@endsection