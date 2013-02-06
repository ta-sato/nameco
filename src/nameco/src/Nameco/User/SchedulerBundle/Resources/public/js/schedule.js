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
			});
		$('#btn-endDate').datepicker({weekStart: 1})
			.on('changeDate', function(ev){
				$(this).datepicker('hide');
				Schedule.onChangeDate(ev, '#schedule_endDateTime_date');
			})
			.on('show', function(ev){
				Schedule.onShow(this, ev, '#schedule_endDateTime_date')
			});
		$('#submit-schedule').click(function(ev){
			Schedule.submitScheduleForm(ev);
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
	},
	submitScheduleForm:function(ev)
	{
		var $form = $('form#form-schedule');
		var $target = $('#register-modal');
		$target.find('.modal-body').html('<div class="progress progress-striped active"><div class="bar" style="width:100%;"></div></div>');
		$.ajax({
			type: $form.attr('method'),
			url: $form.attr('action'),
			data: $form.serialize(),
			success: function(data, status){
				$target.html(data);
				Schedule.init();
			}
		});
		ev.preventDefault();
	},
	closeModal:function()
	{
		$('#register-modal').modal('hide');
	}
}