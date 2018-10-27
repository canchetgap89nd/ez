@extends('admin.layout')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quét lấy số ĐT</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <div class="col-4">
                            <form action="{{ route('admin.scan.phone.start') }}" method="get">
                                <div class="form-group">
                                    <label>
                                        Nhập số lượng UID cho mỗi lần quét
                                    </label>
                                    <input type="number" class="form-control" name="count_of_time" min="1">
                                </div>
                                <div class="form-group">
                                    <label>Nhập khoảng thời gian giữa mỗi lần quét</label>
                                    <input type="number" class="form-control" name="time_range" min="1">
                                </div>
                                <div style="padding: 20px;" id="beginScan">
                                    <button class="btn btn-success" type="submit">Bắt đầu quét</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection