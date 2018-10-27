@extends('admin.layout')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-4">
                        <form method="get" action="{{ route('admin.accounts.detail', $user->id) }}" id="filterAccount">
                            <input type="hidden" id="timeFilter1" name="timeFrom">
                            <input type="hidden" id="timeFilter2" name="timeTo">
                            <div class="form-group">
                                <label>Chọn khoảng thời gian</label>
                                <input type="text" class="form-control" id="timeRanger">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fa fa-info"></i> Thống kê chi tiết tài khoản: {{ $user->name }}</h5> 
                    <strong>ID:</strong> {{ $user->id }} <br>
                    <strong>ID Facebook:</strong> {{ $user->user_fb_id }} <br>
                    <strong>Email:</strong> {{ $user->user_email }} <br>
                    <strong>Email Facebook:</strong> {{ $user->user_fb_email }} <br>
                    <strong>Số ĐT:</strong> {{ $user->user_phone }} <br>
                    @if($user->parent)
                    <strong>Tài khoản nguồn:</strong> <a style="color: #000; font-weight: 700" href="{{ route('admin.accounts.detail', $user->parent_user_id) }}" target="_blank">{{ optional($user->parent)->name }}</a>
                    @endif
                </div>

                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-user"></i> {{ $user->name }}
                                <small class="float-right">Ngày khởi tạo: {{ date('d-m-Y H:i:s', strtotime($user->created_at)) }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-3 invoice-col">
                            <address>
                                <strong>Tổng số cuộc hội thoại</strong><br>
                                {{ $countConversations }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            <address>
                                <strong>Tổng số tin nhắn/bình luận</strong><br>
                                <strong>Tin nhắn: </strong> {{ $countMessages }} <br>
                                <strong>Bình luận: </strong> {{ $countComments }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            <address>
                                <strong>Tổng số khách hàng</strong><br>
                                {{ $countCustomers }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            <address>
                                <strong>Tổng số đơn hàng</strong><br>
                                Tất cả đơn: {{ $countOrders }} <br>
                                Số đơn thành công: {{ $countSuccessOrders }}
                            </address>
                        </div>
                    </div>
                    <!-- /.row -->
                    
                    <div class="row">
                        <!-- /.col -->
                        @if(count($user->accounts))
                        <div class="col-6">
                            <p class="lead">Tài khoản nhân viên</p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Tổng nhân viên:</th>
                                        <td>{{ $user->accounts->count() }}</td>
                                    </tr>
                                    @foreach($user->accounts as $k => $acc)
                                    <tr>
                                        <th>{{ $acc->name }}</th>
                                        <td>{{ $acc->roles[0]->display_name }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        @endif
                        <!-- /.col -->
                        <div class="col-6">
                            <p class="lead">Lịch sử hoạt động</p>

                            <div class="table-responsive">
                                <table class="table">
                                    @if($activities)
                                    <tr>
                                        <th style="width:50%">Hoạt động lần cuối:</th>
                                        <td>{{ $activities->activity }}</td>
                                        <td>{{ date('d-m-Y H:i:s', strtotime($activities->created_at)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <a target="_blank" href="{{ route('admin.accounts.activities', $user->id) }}">Xem chi tiết</a>
                                        </th>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                    @else
                                    Không có hoạt động
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>ID Fanpage</th>
                                        <th>Tên Fanpage</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->pages as $k => $page)
                                    <tr>
                                        <td>{{ $k + 1 }}</td>
                                        <td>{{ $page->fb_page_id }}</td>
                                        <td>
                                            <a target="_blank" href="https://www.facebook.com/{{ $page->fb_page_id }}" class="itemAc">{{ $page->page_name }}</a>
                                        </td>
                                        <td>
                                            @if($page->active)
                                            <span class="badge bg-primary">Active</span>
                                            @else
                                            <span class="badge bg-danger">Deactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td>Tổng: {{ $user->pages->count() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                </div>
                <!-- /.invoice -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
@endsection
@section('scripts')
<script src="{{ asset('admins/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admins/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ asset('admins/plugins/select2/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $("#cateFilter").select2();
    });
    $("#cateFilter").change(function() {
        let role = $(this).val();
        $("#roleForFilter").val(role);
        $("#filterAccount").submit();
    })
    let timeFrom = "{{ $timeFrom }}";
    let timeTo = "{{ $timeTo }}";
    if (timeFrom && timeTo) {
        $('#timeRanger').daterangepicker({
            showDropdowns: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePicker: true,
            startDate: moment(timeFrom).startOf('hour'),
            endDate: moment(timeTo).startOf('hour'),
            locale: {
                "format": "DD/MM/YYYY HH:mm:ss",
                "separator": " - ",
                "applyLabel": "Đồng ý",
                "cancelLabel": "Hủy",
                "fromLabel": "Từ",
                "toLabel": "đến",
                "customRangeLabel": "Tùy chỉnh",
                "weekLabel": "Tuần",
                "daysOfWeek": [
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7",
                    "CN"
                ],
                "monthNames": [
                    "Th1",
                    "Th2",
                    "Th3",
                    "Th4",
                    "Th5",
                    "Th6",
                    "Th7",
                    "Th8",
                    "Th9",
                    "Th10",
                    "Th11",
                    "Th12"
                ],
                "firstDay": 1
            }
        }, function(start, end, label) {
            var time1 = moment(start).format('YYYY-MM-DD HH:mm:ss')
            var time2 = moment(end).format('YYYY-MM-DD HH:mm:ss')
            $("#timeFilter1").val(time1)
            $("#timeFilter2").val(time2)
            $("#filterAccount").submit()
        });
    } else {
        $('#timeRanger').daterangepicker({
            showDropdowns: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePicker: true,
            locale: {
                "format": "DD/MM/YYYY HH:mm:ss",
                "separator": " - ",
                "applyLabel": "Đồng ý",
                "cancelLabel": "Hủy",
                "fromLabel": "Từ",
                "toLabel": "đến",
                "customRangeLabel": "Tùy chỉnh",
                "weekLabel": "Tuần",
                "daysOfWeek": [
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7",
                    "CN"
                ],
                "monthNames": [
                    "Th1",
                    "Th2",
                    "Th3",
                    "Th4",
                    "Th5",
                    "Th6",
                    "Th7",
                    "Th8",
                    "Th9",
                    "Th10",
                    "Th11",
                    "Th12"
                ],
                "firstDay": 1
            }
        }, function(start, end, label) {
            var time1 = moment(start).format('YYYY-MM-DD HH:mm:ss')
            var time2 = moment(end).format('YYYY-MM-DD HH:mm:ss')
            $("#timeFilter1").val(time1)
            $("#timeFilter2").val(time2)
            $("#filterAccount").submit()
        });
    }
</script>
@endsection