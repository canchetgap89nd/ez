@extends('admin.layout')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if(session('message'))
            <div class="col-12">
                <div class="alert alert-dismissible 
                @if (session('status') == 'success')
                alert-success
                @else
                alert-alert
                @endif
                ">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fa fa-ban"></i> Cảnh báo!</h5>
                    {{ session('message') }}
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách các hóa đơn</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>STT</th>
                                <th style="min-width: 225px;">Tên tài khoản</th>
                                <th>Mã thanh toán</th>
                                <th>Gói TK</th>
                                <th>Thời hạn (tháng)</th>
                                <th>Tổng thanh toán</th>
                                <th>Giảm giá</th>
                                <th>Tổng sau giảm giá</th>
                                <th>Nhân viên</th>
                                <th>Thời gian tạo</th>
                                <th>Thanh toán</th>
                                <th>Kích hoạt</th>
                            </tr>
                            @foreach ($payments as $index => $payment)
                            <tr>
                                <td>{{$payments->firstItem() + $index}}</td>
                                <td>
                                    {{$payment->user->name}}
                                    <div class="actionPostBox">
                                        <ul class="lsAcBx">
                                            <li>
                                                <a href="{{ route('payment.edit', $payment->id) }}" class="itemAc">Sửa</a>
                                            </li>
                                            @if(!$payment->is_active)
                                            <li>
                                                <a href="{{ route('user-package.create', 'paymentId=' . $payment->id) }}" class="itemAc">Kích hoạt tài khoản</a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    {{$payment->pay_code}}
                                </td>
                                <td>
                                    {{$payment->package->display_name}}
                                </td>
                                <td>
                                    {{ $payment->duration }}
                                </td>
                                <td>
                                    {{ $payment->amount }}
                                </td>
                                <td>
                                    {{ $payment->discount }}
                                </td>
                                <td>
                                    {{ $payment->total_after_discount }}
                                </td>
                                <td>
                                    {{ $payment->staff ? $payment->staff->email : '' }}
                                </td>
                                <td>
                                    {{ date('d-m-Y', strtotime($payment->created_at)) }}
                                </td>
                                <td>
                                    @if($payment->paid)
                                    <span class="badge bg-primary">Paid</span>
                                    @else
                                    <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->is_active)
                                    <span class="badge bg-primary">Active</span>
                                    @else
                                    <span class="badge bg-danger">Deactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="card-footer clearfix"> 
                        {{ $payments->links() }}
                    </div>
                    <div class="card-footer clearfix"> 
                        Tổng {{ $payments->total() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
    function destroyPack(id) {
        if (confirm('Bạn có chắc muốn xóa')) {
            $("#destroy_" + id).submit()
        }
    }
</script>
@endsection