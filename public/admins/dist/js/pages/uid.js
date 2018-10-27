$(function() {
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
});