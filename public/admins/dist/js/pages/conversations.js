$(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var conversArr = []
  var commentsArr = []
  var messagesArr = []
  var labels = []
  for (var i = 0; i < conversationsOfRangers.length; i++) {
    conversArr.push(conversationsOfRangers[i].total)
    labels.push(conversationsOfRangers[i].date)
  }
  for (var i = 0; i < commentsOfRangers.length; i++) {
    commentsArr.push(commentsOfRangers[i].total)
  }
  for (var i = 0; i < messagesOfRangers.length; i++) {
    messagesArr.push(messagesOfRangers[i].total)
  }

  var mode      = 'index'
  var intersect = true

  var $visitorsChart = $('#users-chart')
  var visitorsChart  = new Chart($visitorsChart, {
    data   : {
      	labels  : labels,
      	datasets: [{
	        type                : 'line',
	        data                : conversArr,
	        backgroundColor     : 'transparent',
	        borderColor         : '#007bff',
	        pointBorderColor    : '#007bff',
	        pointBackgroundColor: '#007bff',
	        fill                : false
	        // pointHoverBackgroundColor: '#007bff',
	        // pointHoverBorderColor    : '#007bff'
      	},
      	{
	        type                : 'line',
	        data                : commentsArr,
	        backgroundColor     : 'transparent',
	        borderColor         : '#c51010',
	        pointBorderColor    : '#c51010',
	        pointBackgroundColor: '#c51010',
	        fill                : false
	        // pointHoverBackgroundColor: '#007bff',
	        // pointHoverBorderColor    : '#007bff'
      	},
      	{
	        type                : 'line',
	        data                : messagesArr,
	        backgroundColor     : 'transparent',
	        borderColor         : '#d6c60a',
	        pointBorderColor    : '#d6c60a',
	        pointBackgroundColor: '#d6c60a',
	        fill                : false
	        // pointHoverBackgroundColor: '#007bff',
	        // pointHoverBorderColor    : '#007bff'
      	}]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero : true
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })

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
        $("#filterConversations").submit()
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
        $("#filterConversations").submit()
    });
  }
})
