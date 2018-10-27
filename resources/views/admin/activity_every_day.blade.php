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
                        <form action="{{ route('admin.accounts.activitiesDays') }}" method="get" id="filterActions">
                            <div class="form-group">
                                <input type="hidden" id="timeFilter1" name="timeFrom">
                                <input type="hidden" id="timeFilter2" name="timeTo">
                                <label>Chọn khoảng thời gian</label>
                                <input type="text" class="form-control" id="timeRanger">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Hoạt động</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg"></span>
                                <span>Tổng</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="users-chart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thống kê tài khoản</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>Tên tài khoản</th>
                                <th>Hoạt động</th>
                                <th>Thời gian hoạt động</th>
                                <th>Nền tảng</th>
                                <th>Mô tả</th>
                            </tr>
                            @foreach ($activities as $action)
                            <tr>
                                <td>{{ $action->user_id }}</td>
                                <td>
                                    <a href="{{ route('admin.accounts.detail', $action->user_id) }}">
                                        {{ optional(DB::table('users')->find($action->user_id))->name }}
                                    </a>
                                </td>
                                <td>{{ $action->activity }}</td>
                                <td>{{ date('d-m-Y H:i', strtotime($action->created_at)) }}</td>
                                <td>{{ $action->from_platform }}</td>
                                <td>{{ $action->activity_desc }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $activities->links() }}
                    </div>
                    <div class="card-footer clearfix"> 
                        Tổng {{ $activities->total() }}
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
<script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>
<script type="text/javascript">
    var timeFrom = "{{ $timeFrom }}";
    var timeTo = "{{ $timeTo }}";
    var activities = <?php echo json_encode($activitiesRanger); ?>
</script>
<script src="{{ asset('admins/dist/js/pages/activity_eve.js') }}"></script>
@endsection