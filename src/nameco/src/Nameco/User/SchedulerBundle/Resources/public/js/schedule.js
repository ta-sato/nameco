var Schedule =
{
	init:function()
	{
		$('#btn-startDate').datepicker({weekStart: 1})
			.on('changeDate', function(ev){
				$(this).datepicker('hide');
				Schedule.onChangeDate(ev, '#schedule_startDateTime_date');
			})
			.on('show', function(ev){
				Schedule.onShow(this, ev, '#schedule_startDateTime_date')
			});;
		$('#btn-endDate').datepicker({weekStart: 1})
			.on('changeDate', function(ev){
				$(this).datepicker('hide');
				Schedule.onChangeDate(ev, '#schedule_endDateTime_date');
			})
			.on('show', function(ev){
				Schedule.onShow(this, ev, '#schedule_endDateTime_date')
			});
	},
	onChangeDate:function(ev, selector)
	{
		var selectedDate = new Date(ev.date);
		var date = selectedDate.getFullYear() + '/' + (selectedDate.getMonth() + 1) + '/' + selectedDate.getDate();
		$(selector).val(date)
	},
	onShow:function(elem, ev, selector)
	{
		$(elem).datepicker('setValue', $(selector).val());
	}
}

$(window).load(function(){
	Schedule.init();
});