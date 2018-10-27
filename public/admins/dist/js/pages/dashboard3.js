$(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var accountsThisWeek = []
  var labels = []
  var accountsLastWeek = []
  for (var i = accounts.thisWeek.length - 1; i >= 0; i--) {
    accountsThisWeek.push(accounts.thisWeek[i].total)
    labels.push(accounts.thisWeek[i].date)
  }
  for (var i = accounts.lastWeek.length - 1; i >= 0; i--) {
    accountsLastWeek.push(accounts.lastWeek[i].total)
  }

  var converThisWeek = []
  var converLastWeek = []
  for (var i = accounts.thisWeek.length - 1; i >= 0; i--) {
    converThisWeek.push(conversations.thisWeek[i].total)
  }
  for (var i = accounts.lastWeek.length - 1; i >= 0; i--) {
    converLastWeek.push(conversations.lastWeek[i].total)
  }

  var commentThisWeek = []
  var commentLastWeek = []
  for (var i = accounts.thisWeek.length - 1; i >= 0; i--) {
    commentThisWeek.push(comments.thisWeek[i].total)
  }
  for (var i = accounts.lastWeek.length - 1; i >= 0; i--) {
    commentLastWeek.push(comments.lastWeek[i].total)
  }

  var messageThisWeek = []
  var messageLastWeek = []
  for (var i = accounts.thisWeek.length - 1; i >= 0; i--) {
    messageThisWeek.push(messages.thisWeek[i].total)
  }
  for (var i = accounts.lastWeek.length - 1; i >= 0; i--) {
    messageLastWeek.push(messages.lastWeek[i].total)
  }

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#sales-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : labels,
      datasets: [
        {
          backgroundColor: '#007bff',
          borderColor    : '#007bff',
          data           : converThisWeek
        },
        {
          backgroundColor: '#ced4da',
          borderColor    : '#ced4da',
          data           : commentThisWeek
        },
        {
          backgroundColor: '#e50b0b',
          borderColor    : '#e50b0b',
          data           : messageThisWeek
        }
      ]
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
            beginAtZero: true
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

  var $visitorsChart = $('#visitors-chart')
  var visitorsChart  = new Chart($visitorsChart, {
    data   : {
      labels  : labels,
      datasets: [{
        type                : 'line',
        data                : accountsThisWeek,
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
          data                : accountsLastWeek,
          backgroundColor     : 'tansparent',
          borderColor         : '#ced4da',
          pointBorderColor    : '#ced4da',
          pointBackgroundColor: '#ced4da',
          fill                : false
          // pointHoverBackgroundColor: '#ced4da',
          // pointHoverBorderColor    : '#ced4da'
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
})
