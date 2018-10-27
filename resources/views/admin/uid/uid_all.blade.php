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
                            <label>Chọn tình trạng</label>
                            <select class="form-control" id="cateFilter">
                                <option value="">Tất cả</option>
                                <option value="1"
                                @if($status == 1)
                                selected
                                @endif
                                >Đã quét</option>
                                <option value="2"
                                @if($status == 2)
                                selected
                                @endif
                                >Chưa quét</option>
                            </select>
                        </div>
                    </div>
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
                        <h3 class="card-title">Thống kê UID</h3>
                        <div class="card-tools">
                            <form action="{{ route('admin.uid.all') }}" method="get" id="filterAccount">
                                <input type="hidden" id="timeFilter1" name="timeFrom">
                                <input type="hidden" id="timeFilter2" name="timeTo">
                                <input type="hidden" id="roleForFilter" name="status">
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
                                <th>Thời gian nhập</th>
                                <th>Tình trạng</th>
                            </tr>
                            @foreach ($uids as $ind => $uid)
                            <tr>
                                <td>{{ $uids->firstItem() + $ind }}</td>
                                <td>{{$uid->uid}}</td>
                                <td>{{ date('Y-m-d H:i:s', strtotime($uid->created_at)) }}</td>
                                <td>
                                    @if($uid->scanned)
                                    <span class="badge bg-success">Đã quét</span>
                                    @else
                                    <span class="badge bg-danger">Chưa quét</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $uids->links() }}
                    </div>
                    <div class="card-footer clearfix">
                        Từ {{ $uids->firstItem() }} đến {{ $uids->lastItem() }}
                    </div>
                    <div class="card-footer clearfix">
                        Tổng {{ $uids->total() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('admins/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admins/dist/js/pages/uid.js') }}"></script>
<script type="text/javascript">
     $(function() {
        $("#cateFilter").select2();
    });
    $("#cateFilter").change(function() {
        let role = $(this).val();
        $("#roleForFilter").val(role);
        $("#filterAccount").submit();
    })
    var timeFrom = "{{ $timeFrom }}";
    var timeTo = "{{ $timeTo }}";
</script>
@endsection