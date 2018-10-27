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
                        <h3 class="card-title">Các gói trên Chốt Sale</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>STT</th>
                                <th>Tên tính năng</th>
                                <th>Name Code</th>
                                <th>Ngày tạo</th>
                                <th>Ngày cập nhật</th>
                            </tr>
                            @foreach ($features as $index => $feature)
                            <tr>
                                <td>{{$features->firstItem() + $index}}</td>
                                <td>
                                    {{$feature->display_name}}
                                    <div class="actionPostBox">
                                        <ul class="lsAcBx">
                                            <li>
                                                <a href="{{ route('feature.edit', $feature->id) }}" class="itemAc">Sửa</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="destroyFeature({{$feature->id}})" class="itemAc">Xóa</a>
                                                <form action="{{ route('feature.destroy', $feature->id) }}" id="destroy_{{ $feature->id }}" method="post">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $feature->name }}</td>
                                <td>
                                    {{ date('d-m-Y', strtotime($feature->created_at)) }}
                                </td>
                                <td>
                                    {{ date('d-m-Y', strtotime($feature->updated_at)) }}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="card-footer clearfix"> 
                        {{ $features->links() }}
                    </div>
                    <div class="card-footer clearfix"> 
                        Tổng {{ $features->total() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
    function destroyFeature(id) {
        if (confirm('Bạn có chắc muốn xóa')) {
            $("#destroy_" + id).submit()
        }
    }
</script>
@endsection