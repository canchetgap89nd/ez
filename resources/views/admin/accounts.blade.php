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
                            <label>Chọn chức vụ</label>
                            <select class="form-control" id="cateFilter">
                                <option value="">Chức vụ</option>
                                @isset($roles)
                                @foreach($roles as $item)
                                <option value="{{ $item->id }}"
                                @if($role == $item->id)
                                selected
                                @endif
                                >{{$item->display_name}}</option>
                                @endforeach
                                @endisset
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
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Người dùng</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                @php
                                $total = 0;
                                foreach ($usersInRanger as $item) {
                                    $total += $item['total'];   
                                }
                                @endphp
                                <span class="text-bold text-lg">{{ $total }}</span>
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

                        <div class="card-tools">
                            <form action="{{ route('admin.accounts') }}" method="get" id="filterAccount">
                                <input type="hidden" id="timeFilter1" name="timeFrom">
                                <input type="hidden" id="timeFilter2" name="timeTo">
                                <input type="hidden" name="role" id="roleForFilter" value="">
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
                                <th>ID</th>
                                <th>ID Facebook</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Số ĐT</th>
                                <th>Số Fanpage</th>
                                <th>Số Fanpage Active</th>
                                <th>Số hội thoại</th>
                                <th>Chức danh</th>
                                <th>Tài khoản nguồn</th>
                                <th>Số NV</th>
                                <th>Thời gian ĐK</th>
                                <th>Trạng thái</th>
                            </tr>
                            @foreach ($accounts as $account)
                            <tr>
                                <td>
                                    {{$account->id}}
                                </td>
                                <td>
                                    <a href="{{ route('admin.getLinkFace', $account->user_fb_id) }}" target="_blank" title="Đi tới Facebook cá nhân">
                                        {{$account->user_fb_id}}
                                    </a>
                                    <div class="actionPostBox">
                                        <ul class="lsAcBx">
                                            <li>
                                                <a target="_blank" href="{{ route('admin.accounts.detail', $account->id) }}" class="itemAc">Chi tiết</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    {{$account->name}}
                                </td>
                                <td>{{$account->user_email}}</td>
                                <td>{{$account->user_phone}}</td>
                                <td>{{$account->pagesTotal}}</td>
                                <td>{{$account->pagesActive}}</td>
                                <td>{{$account->count_conversations}}</td>
                                <td>{{optional($account->roles[0])->display_name}}</td>
                                <td>{{$account->parent_user_id}}</td>
                                <td>{{$account->accounts_count}}</td>
                                <td style="width: 100px; display: flex;">{{ date('d-m-Y H:i', strtotime($account->created_at)) }}</td>
                                <td>
                                    @if($account->blocked)
                                    <span class="badge bg-danger">Block</span>
                                    @else
                                    <span class="badge bg-primary">Active</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $accounts->links() }}
                    </div>
                    <div class="card-footer clearfix"> 
                        Tổng {{ $accounts->total() }}
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
<script src="{{ asset('admins/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('admins/dist/js/pages/accounts.js') }}"></script>
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
    var accounts = <?php echo json_encode($usersInRanger) ?>
</script>
@endsection