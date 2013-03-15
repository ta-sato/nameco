window.onload = function() {
	User.init();
};

var User =
{
	init: function()
	{
		var target = '';
		$('.user_remove').click(function(e)
		{
			e.stopPropagation();
			$('#remove_confirm').modal('show');
			target = $('#' + this.id +'_url').text();
			return false;
		});

		$('#execute_remove').click(function()
		{
			location.href = target;
		});

		$('#remove_no').click(function(e){
			$('#remove_confirm').modal('hide');
			return false;
		});


	}
}