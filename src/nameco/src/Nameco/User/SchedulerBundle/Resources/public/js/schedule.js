var Schedule =
{
	init:function()
	{
		$("th[id^='date_']").click(function(ev){
			ev.preventDefault();
			var date = $(this).attr('id').slice(5);
			var url = $('#schedule_new_prefix').text() + '/' + date.slice(0, 4) + '/' + date.slice(4, 6) + '/' + date.slice(6, 8);
			$.get(url, function(data){
				var $elem = $('#register-modal');
				$elem.html(data);
				$elem.modal();
			}).success(function(){
				Schedule.formInit();
			});
		});
		$('#register-modal')
			.on('hidden', function(ev){
				Schedule.formDispose();
			});
	},
	formInit:function()
	{
		$('#btn-startDate').datepicker({weekStart: 1, language:'ja'})
			.on('changeDate', function(ev){
				$(this).datepicker('hide');
				Schedule.onChangeDate(ev, '#schedule_startDateTime_date');
			})
			.on('show', function(ev){
				Schedule.onShow(this, ev, '#schedule_startDateTime_date')
			});
		$('#btn-endDate').datepicker({weekStart: 1, language:'ja'})
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
	formDispose:function()
	{
		$('#btn-startDate').datepicker('remove');
		$('#btn-endDate').datepicker('remove');
	},
	onChangeDate:function(ev, selector)
	{
		var selectedDate = new Date(ev.date);
		var date = selectedDate.getFullYear() + '/' + (selectedDate.getMonth() + 1) + '/' + selectedDate.getDate();
		$(selector).val(date)
	},
	onShow:function(elem, ev, selector)
	{
		$(elem).datepicker('setDate', new Date($(selector).val()));
		$(elem).datepicker('update');
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
				Schedule.formInit();
			}
		});
		ev.preventDefault();
	},
	closeModal:function()
	{
		$('#register-modal').modal('hide');
	}
}

$(window).ready(function(){
	Schedule.init();
});