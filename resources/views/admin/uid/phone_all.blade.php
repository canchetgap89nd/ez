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
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Chọn khoảng thời gian</label>
                            <input type="text" class="form-control" id="timeRanger">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thống kê Số ĐT quét được</h3>
                        <div class="card-tools">
                            <form action="{{ route('admin.phone.all') }}" method="get" id="filterAccount">
                                <input type="hidden" id="timeFilter1" name="timeFrom">
                                <input type="hidden" id="timeFilter2" name="timeTo">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="keyword" value="{{ $keyword }}" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>STT</th>
                                <th>UID Facebook</th>
                                <th>Số ĐT</th>
                                <th>Thời gian nhập</th>
                            </tr>
                            @foreach ($phones as $ind => $phone)
                            <tr>
                                <td>{{ $phones->firstItem() + $ind }}</td>
                                <td>{{$phone->uid}}</td>
                                <td>{{ $phone->phone }}</td>
                                <td>{{ date('Y-m-d H:i:s', strtotime($phone->created_at)) }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $phones->links() }}
                    </div>
                    <div class="card-footer clearfix">
                        Từ {{ $phones->firstItem() }} đến {{ $phones->lastItem() }}
                    </div>
                    <div class="card-footer clearfix">
                        Tổng {{ $phones->total() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admins/dist/js/pages/uid.js') }}"></script>
<script type="text/javascript">
    var timeFrom = "{{ $timeFrom }}";
    var timeTo = "{{ $timeTo }}";
</script>
@endsection