@extends('admin.layout')
@section('styleHead')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admins/plugins/datatables/dataTables.bootstrap4.css') }}">

@endsection
@section('content')
<div class="content">
	<div class="container-fluid">
	    <div class="row">
	       <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fa fa-info"></i> Tổng số</h5> 
                    <strong>Cuộc hội thoại: </strong> {{ $totalConversation }} <br>
                    <strong>Tin nhắn: </strong> {{ $totalMessage }} <br>
                    <strong>Bình luận: </strong> {{ $totalComment }}
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-4">
                    	<form action="{{ route('admin.conversations') }}" method="get" id="filterConversations">
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
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Hội thoại trong khoảng thời gian</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                @php
                                $totalConverRanger = 0;
                                $totalCommentRanger = 0;
                                $totalMessageRanger = 0;
                                foreach ($conversationsOfRangers as $item) {
                                	$totalConverRanger += $item['total'];
                                }
                                foreach ($commentsOfRangers as $item) {
                                	$totalCommentRanger += $item['total'];
                                }
                                foreach ($messagesOfRangers as $item) {
                                	$totalMessageRanger += $item['total'];
                                }
                                @endphp
                                <span class="text-bold text-lg">Cuộc hội thoại: {{ $totalConverRanger }}</span>
                                <span class="text-bold text-lg">Tin nhắn: {{ $totalMessageRanger }}</span>
                                <span class="text-bold text-lg">Bình luận: {{ $totalCommentRanger }}</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="users-chart" height="200"></canvas>
                        </div>
                        <div class="d-flex flex-row justify-content-end">
				            <span class="mr-2">
			                    <i class="fa fa-square" style="color: #007bff"></i> Cuộc hội thoại
		                  	</span>
				            <span>
			                    <i class="fa fa-square" style="color: #c51010"></i> Bình luận
		                  	</span>
		                  	<span>
			                    <i class="fa fa-square" style="color: #d6c60a"></i> Tin nhắn
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
<script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admins/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admins/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('admins/dist/js/pages/conversations.js') }}"></script>
<script type="text/javascript">
	var totalConversation = <?php echo json_encode($totalConversation); ?>;
	var totalComment = <?php echo json_encode($totalComment); ?>;
	var totalMessage = <?php echo json_encode($totalMessage); ?>;
	var timeFrom = <?php echo json_encode($timeFrom); ?>;
	var timeTo = <?php echo json_encode($timeTo); ?>;
	var conversationsOfRangers = <?php echo json_encode($conversationsOfRangers); ?>;
	var commentsOfRangers = <?php echo json_encode($commentsOfRangers); ?>;
	var messagesOfRangers = <?php echo json_encode($messagesOfRangers); ?>;
	var timeFrom = "{{ $timeFrom }}";
    var timeTo = "{{ $timeTo }}";
</script>
@endsection