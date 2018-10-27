@extends('admin.layout')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Thống kê tổng quan</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $accounts['total'] }}</h3>

                        <p>Người dùng</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('admin.accounts') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $conversations['total'] }}</h3>

                        <p>Cuộc hội thoại</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('admin.conversations') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $posts }}</h3>

                        <p>Bài viết</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('blog.all.post') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $customers }}</h3>

                        <p>Khách hàng</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Người dùng</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">{{ $accounts['total'] }}</span>
                                <span>Tất cả thời gian</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                            	@php
                            	$totalTw = 0;
                            	$totalLw = 0;
                            	foreach ($accounts['thisWeek'] as $item) {
                            		$totalTw += $item['total'];
                            	}
                            	foreach ($accounts['lastWeek'] as $item) {
                            		$totalLw += $item['total'];
                            	}
                            	$percent = $totalLw != 0 ? ($totalTw - $totalLw)/$totalLw * 100 : 100;
                            	@endphp
                            	@if ($percent > 0)
                                <span class="text-success">
			                      <i class="fa fa-arrow-up"></i> {{$percent}}%
			                    </span>
			                    @else
								<span class="text-danger">
			                      <i class="fa fa-arrow-down"></i> {{abs($percent)}}%
			                    </span>
			                    @endif
                                <span class="text-muted">So với tuần trước</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="visitors-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
			                    <i class="fa fa-square text-primary"></i> Tuần này
		                  	</span>

                            <span>
			                    <i class="fa fa-square text-gray"></i> Tuần trước
	                  		</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
				    <div class="card-header no-border">
				        <div class="d-flex justify-content-between">
				            <h3 class="card-title">Hội thoại</h3>
				        </div>
				    </div>
				    <div class="card-body">
				        <div class="d-flex">
				            <p class="d-flex flex-column">
				                <span class="text-bold text-lg">{{$conversations['total']}}</span>
				                <span>Tất cả thời gian</span>
				            </p>
				            <p class="ml-auto d-flex flex-column text-right">
				                @php
                            	$totalCoTw = 0;
                            	$totalCoLw = 0;
                            	foreach ($conversations['thisWeek'] as $item) {
                            		$totalCoTw += $item['total'];
                            	}
                            	foreach ($conversations['lastWeek'] as $item) {
                            		$totalCoLw += $item['total'];
                            	}
                            	$percentCo = $totalCoLw != 0 ? ($totalCoTw - $totalCoLw)/$totalCoLw * 100 : 100;
                            	@endphp
                            	@if ($percentCo > 0)
                                <span class="text-success">
			                      <i class="fa fa-arrow-up"></i> {{$percentCo}}%
			                    </span>
			                    @else
								<span class="text-danger">
			                      <i class="fa fa-arrow-down"></i> {{abs($percentCo)}}%
			                    </span>
			                    @endif
				                <span class="text-muted">So với tuần trước</span>
				            </p>
				        </div>

				        <div class="position-relative mb-4">
				            <canvas id="sales-chart" height="200"></canvas>
				        </div>

				        <div class="d-flex flex-row justify-content-end">
				            <span class="mr-2">
			                    <i class="fa fa-square" style="color: #007bff"></i> Hội thoại
		                  	</span>
				            <span>
			                    <i class="fa fa-square" style="color: #ced4da"></i> Bình luận
		                  	</span>
		                  	<span>
			                    <i class="fa fa-square" style="color: #e50b0b"></i> Tin nhắn
		                  	</span>
				        </div>
				    </div>
				</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	var accounts = <?php echo json_encode($accounts) ?>;
	var conversations = <?php echo json_encode($conversations) ?>;
	var comments = <?php echo json_encode($comments) ?>;
	var messages = <?php echo json_encode($messages) ?>;
</script>
<script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('admins/dist/js/pages/dashboard3.js') }}"></script>
@endsection