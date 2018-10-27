@extends('admin.layout')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lịch sử hoạt động tài khoản <strong>{{ $user->name }}</strong></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>Thời gian</th>
                                <th>Hoạt động</th>
                                <th>Mô tả hoạt động</th>
                            </tr>
                            @foreach ($activities as $action)
                            <tr>
                                <td>
                                    {{ date('d-m-Y H:i:s', strtotime($action->created_at)) }}
                                </td>
                                <td>
                                    {{ $action->activity }}
                                </td>
                                <td>
                                    {{ $action->activity_desc }}
                                </td>
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