jQuery(document).ready(function() {
    $('#from').datetimepicker({
		dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
		separator: ' ',
        showHour: true,
        showMinute: true,
        showSecond: true
	});
});
