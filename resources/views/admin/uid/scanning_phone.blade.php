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
            @if($count == 0)
            <div class="col-12">
                <div class="alert alert-dismissible alert-warning">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fa fa-ban"></i> Cảnh báo!</h5>
                    Không tìm thấy UID nào
                </div>
            </div>
            @endif
            <div class="col-12">
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
            <div class="col-12">
                @if($count > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Đang quét lấy số ĐT</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0 text-center" style="min-height: 100px;">
                        <div id="scanning" style="padding-top: 20px">
                            <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <form action="{{ route('admin.scan.phone.start') }}" method="get" id="scanningForm">
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
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@if($count > 0)
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            $("#scanningForm").submit()
        }, {{ $timeRange * 1000 }})
    })
</script>
@endsection
@endif