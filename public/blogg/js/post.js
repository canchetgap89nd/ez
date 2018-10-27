$(function() {
	var url = $("#routerUrl").val()
	setTimeout(function() {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			success: function(res) {
				console.log(res)
			}
		})
	}, 6000)
})