var Navigation =
{
	init:function()
	{

		$('#selectDateButton').datepicker({
			weekStart : 1,
			language: 'ja'
		}).on('changeDate', function(ev){
			Navigation.onChangeDate(ev);
		});
	},

	/**
	 * 日付変更時処理
	 */
	onChangeDate:function(ev)
	{
		$('#selectDateButton').datepicker('hide');
		var selectDate = new Date(ev.date);
		$('#salectDateForm [name=y]').val(selectDate.getFullYear());
		$('#salectDateForm [name=m]').val(selectDate.getMonth() + 1);
		$('#salectDateForm').submit();
	}
};

$(document).ready(Navigation.init);
