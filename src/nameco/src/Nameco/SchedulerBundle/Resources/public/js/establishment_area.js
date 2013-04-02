
var EstablishmentArea =
{
	init: function()
	{
		$('.remove').click(function(e)
		{
			e.preventDefault();
			$('#remove_confirm').modal('show');
			$('#form_id').val($(this).attr('delete_id'));
		});

		$('#execute_remove').click(function(e)
		{
			e.preventDefault();
			$('#delete_form').submit();
		});

		$('#remove_no').click(function(e){
			e.preventDefault();
			$('#remove_confirm').modal('hide');
		});
	}
};

$(document).ready(EstablishmentArea.init);
