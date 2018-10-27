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
                <div class="card" style="padding: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">Tải lên danh sách UID - số điện thoại</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <form class="form" action="{{ route('admin.import.phone') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>ID Group, Fanpage</label>
                                <input type="number" name="look_id" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="phoneList">Chọn tệp UID-Phone (.txt)</label>
                                <input id="phoneList" type="file" class="form-control" name="UidPhoneList">
                                <span class="help-block">Định dạng text trong tệp phải là UID|Phone</span>
                            </div>
                            <button class="btn btn-success" type="submit">Tải lên</button>
                        </form>
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
@section('scripts')

@endsection