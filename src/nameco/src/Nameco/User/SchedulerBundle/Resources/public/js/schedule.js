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
		$('form#form-schedule').live('submit', function(){
			Schedule.submitScheduleForm(this);
			return false;
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
		Schedule.registRemove('ul#schedule_user');
		var now = new Date();
		$('#btn-participant').editable({
			sourceCache: false,
			unsavedclass: null,
			display: function(value, sourceData) {
				Schedule.setChecked('#schedule_user', 'schedule[user][]', value, sourceData);
				Schedule.registRemove('ul#schedule_user');
			},
			inputclass: 'schedule_user_value'
		})
		.on('shown', function(){
			Schedule.loadChecked('#btn-participant', 'schedule[user][]', '.schedule_user_value');
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
	submitScheduleForm:function(form)
	{
		var f = $(form);
		var formData = f.serialize();
		var target = $('#register-modal');
		target.find('.modal-body').html('<div class="progress progress-striped active"><div class="bar" style="width:100%;"></div></div>');
		var method = f.attr('method');
		var action = f.attr('action');
		$.ajax({
			type: method,
			url: action,
			data: formData,
			success: function(data, status){
				target.html(data);
				Schedule.formInit();
			},
		});
	},
	closeModal:function()
	{
		$('#register-modal').modal('hide');
	},
	setChecked:function(ulId, hiddenName, value, sourceData)
	{
		var $ul = $(ulId);
		var checked;
		$ul.empty();
		if (!value) {
			return;
		}
		checked = $.grep(sourceData, function(o){
			return $.grep(value, function(v){ 
				 return v == o.value; 
			}).length;
		});
		$.each(checked, function(i, v) {
			$ul.append('<li class="clearfix">' + $.fn.editableutils.escape(v.text) + '<input type="hidden" name="' + hiddenName + '" value="' + v.value + '"/><a class="pull-right btn btn-mini btn-danger" href="#"><i class="icon-remove icon-white"></i></a></li>');
		});
	},
	registRemove:function(selector)
	{
		$(selector).find('li>a').click(function(e){
			$(this).parent().remove();
		});
	},
	loadChecked:function(editableId, valuename, inputclass)
	{
		var selected = new Array();
		$("input:hidden[name^='" + valuename + "']").each(function(){
			selected.push($(this).val());
		});
		$(editableId).editable('setValue', selected, null);
		$(inputclass).each(function(){
			var $elem = $(this);
			if ($.inArray($elem.val(), selected) != -1) {
				$elem.attr('checked', 'checked');
			} else {
				$elem.removeAttr('checked');
			}
		});
	}
}

$(window).ready(function(){
	Schedule.init();
});