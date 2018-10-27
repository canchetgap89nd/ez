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
    <form role="form" action="{{ route('payment.update', $payment->id) }}" method="POST">
    	<input type="hidden" name="id" value="{{ $payment->id }}">
    	<div class="row">
			<div class="col-md-6">
				<div class="card card-primary">
				    <div class="card-header">
				        <h3 class="card-title">Thông tin hóa đơn {{ $payment->pay_code }}</h3>
				    </div>
				    <!-- /.card-header -->
				    <!-- form start -->
				    	{{csrf_field()}}
				    	{{ method_field('PUT') }}
				    	@if($payment->paid)
				    	<div class="card card-body">
				    		<div class="form-group">
			                    <label>Tên: </label>
			                    <span>{{ $payment->user->name }}</span>
		                  	</div>
		                  	<div class="form-group">
			                    <label>Gói tài khoản: </label>
			                    <span>{{ $payment->package->display_name }}</span>
		                  	</div>
		                  	<div class="form-group">
				                <label>Đơn giá (VNĐ): </label>
				                <span>{{ $payment->price }}</span>
				            </div>
				            <div class="form-group">
				                <label>Thời hạn (tháng): </label>
				                <span>{{ $payment->duration }}</span>
				            </div>
				            <div class="form-group">
				                <label>Tặng thêm (tháng): </label>
				                <span>{{ $payment->duration_bonus }}</span>
				            </div>
				            <div class="form-group">
				                <label>Tổng tiền: </label>
				                <span id="amount">{{ $payment->amount }}</span>
				            </div>
				    	</div>
				    	@else
				        <div class="card card-body">
				        	<div class="form-group">
			                    <label>Tên: </label>
			                    <span>{{ $payment->user->name }}</span>
		                  	</div>
		                  	<div class="form-group">
			                    <label>Gói tài khoản: </label>
			                    <span>{{ $payment->package->display_name }}</span>
		                  	</div>
		                  	<div class="form-group">
				                <label>Đơn giá (VNĐ): </label>
				                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="price" value="{{ $payment->price }}" min="0">
				            </div>
				            <div class="form-group">
				                <label>Thời hạn (tháng): </label>
				                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="duration" value="{{ $payment->duration }}" min="0">
				            </div>
				            <div class="form-group">
				                <label>Tặng thêm (tháng): </label>
				                <input onblur="totalAfterDiscount()" type="number" class="form-control" name="duration_bonus" value="{{ $payment->duration_bonus }}" min="0">
				            </div>
				            <div class="form-group">
				                <label>Tổng tiền: </label>
				                <span id="amount">{{ $payment->amount }}</span>
				            </div>
				        </div>
			        	@endif
				</div>
			</div>
			<div class="col-md-6">
				<div class="card card-primary">
					<div class="card card-header">
						<h3 class="card-title">Thông tin khoản thanh toán (đơn vị: VNĐ)</h3>
					</div>
					@if($payment->paid)
					<div class="card card-body">
						<div class="form-group">
			                <label>Tổng thanh toán: </label>
			                <span id="total_payable">{{ $payment->total_payable }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Thuế</label>
			            	<span>{{ $payment->tax }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Khoản khác</label>
			            	<span>{{ $payment->other_payment }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Giảm giá</label>
			            	<span>{{ $payment->discount }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Tổng sau giảm giá: </label>
			            	<span id="total_after_discount">{{ $payment->total_after_discount }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Thanh toán</label>
                            <span class="badge bg-primary">Paid</span>
			            </div>
					</div>
					@else
					<div class="card card-body">
						<div class="form-group">
			                <label>Tổng thanh toán: </label>
			                <span id="total_payable">{{ $payment->total_payable }}</span>
			            </div>
			            <div class="form-group">
			            	<label>Thuế</label>
			            	<input onblur="totalAfterDiscount()" type="number" name="tax" class="form-control" min="0" value="{{ $payment->tax }}">
			            </div>
			            <div class="form-group">
			            	<label>Khoản khác</label>
			            	<input onblur="totalAfterDiscount()" type="number" name="other_payment" class="form-control" min="0" value="{{ $payment->other_payment }}">
			            </div>
			            <div class="form-group">
			            	<label>Giảm giá</label>
			            	<input onblur="totalAfterDiscount()" type="number" name="discount" class="form-control" min="0" value="{{ $payment->discount }}">
			            </div>
			            <div class="form-group">
			            	<label>Tổng sau giảm giá: </label>
			            	<span id="total_after_discount">{{ $payment->total_after_discount }}</span>
			            </div>
			            <div class="form-group">
			            	<label class="radio-inline">
			            		<input type="radio" name="paid" value="{{1}}"
			            		@if($payment->paid)
			            		checked
			            		@endif
			            		>
			            		Đã thanh toán
			            	</label>
			            	<label class="radio-inline">
			            		<input type="radio" name="paid" value="{{0}}"
			            		@if(!$payment->paid)
			            		checked
			            		@endif
			            		>
			            		Chưa thanh toán
			            	</label>
			            </div>
					</div>
					<div class="card-footer">
			            <button type="submit" class="btn btn-primary">Xác nhận</button>
			            <button type="button" onclick="destroyPayment()" class="btn btn-danger">Hủy hóa đơn</button>
			        </div>
					@endif
				</div>
			</div>
    	</div>
    </form>
    <form action="{{ route('payment.destroy', $payment->id) }}" id="destroyPayment" method="post">
    	{{ csrf_field() }}
    	{{ method_field('DELETE') }}
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

    function destroyPayment() {
    	if (confirm('Bạn có chắc muốn xóa')) {
    		$("#destroyPayment").submit();
    	}
    }
</script>
@endsection