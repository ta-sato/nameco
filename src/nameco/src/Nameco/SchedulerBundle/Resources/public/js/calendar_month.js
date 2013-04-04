
var Month =
{
	init : function()
	{
		var items = $('#schedules').find('li');
		items.each(function()
		{
			var item = {
				'id' : $(this).children('.id').text(),
				'start_date' : $(this).children('.start_date').text(),
				'end_date' : $(this).children('.end_date').text(),
				'start_time' : $(this).children('.start_time').text(),
				'end_time' : $(this).children('.end_time').text(),
				'title' : $(this).children('.title').text()
			};

			// 開始日が前月
			if ($('#date_' + item.start_date).length == 0)
			{
				item.start_date = $('.weekday:first-child')[0].id.replace('date_', '');
			}
			// 終了日が来月
			if ($('#date_' + item.end_date).length == 0)
			{
				var th = $('.weekday:last-child');
				item.end_date = th[th.length - 1].id.replace('date_', '');
			}

			var start = $('#date_' + item.start_date);
			var end   = $('#date_' + item.end_date);

			var t_start = start.closest('table')[0];
			var t_end   = end.closest('table')[0];
			var w_start = Number(t_start.id.replace('table_', ''));
			var w_end   = Number(t_end.id.replace('table_', ''));

			var start_date = item.start_date;
			var end_date   = item.end_date;

			for ( var i = w_start; i <= w_end; i++)
			{
				var s = $('#date_' + start_date)[0];
				var e = $('#date_' + end_date)[0];

				if (i != w_start)
				{
					s = $('#table_' + i + ' .weekday:first-child')[0];
				}
				if (i != w_end)
				{
					e = $('#table_' + i + ' .weekday:last-child')[0];
				}

				item.start_date = s.id.replace('date_', '');
				item.end_date = e.id.replace('date_', '');
				item.days = $(e).index() - $(s).index() + 1;
				Month.setCalendar(item);
			}
		});

		$('#calender_bg').height($('#grid_pane').height());
	},

	setCalendar : function(item)
	{
		// 対象のテーブルを特定する		 
		var table = $('#date_' + item.start_date).closest('table');
		var tbody = table.children('tbody');

		// 曜日のインデックスを取得
		var week_eq = $('#date_' + item.start_date).index();

		// 対象の行を特定する
		var rows = tbody.children('tr');
		var tr = null;
		var td = null;

		for ( var i = 0; i < rows.length; i++)
		{
			var r = rows[i];
			var eq = week_eq;

			var spans = $(r).children('td[colspan!="1"]');
			if (spans.length > 0)
			{
				var span = 0;
				for ( var n = 0; n < spans.length; n++)
				{
					span += Number($(spans[n]).attr("colspan"));
				}
				eq = week_eq - span + spans.length;

				if (eq < 0)
				{
					continue;
				}
			}

			var d = $(r).children('td:eq(' + eq + ')');
			if (d.find('div').length == 0)
			{
				tr = r;
				td = d;
				break;
			}
		}

		if (tr == null)
		{
			tr = $('<tr></tr>');

			for ( var i = 0; i <= 6 - item.days + 1; i++)
			{
				var d = $('<td colspan="1"></td');
				tr.append(d);
				if (i == week_eq)
				{
					td = d;
				}
			}
			tbody.append(tr);
		}

		var schedule = $('<div class="schedule"></div>');
		var link = $('<a id="schedule_' + item.id + '" href="#">'
				+ item.start_time + '～' + item.end_time + '<br />'
				+ '<span class="shcedule_title">' + item.title + '</span>'
				+ '</a>');
		schedule.append(link);
		td.attr('colspan', item.days);
		
		// はみ出たtdを調整
		var tds = $(tr).children('td');
		var total_span = 0;
		for ( var n = 0; n < tds.length; n++) {
			total_span += Number($(tds[n]).attr('colspan'));
		}
		if (total_span > 7) {
			for (total_span; total_span != 7; total_span--) {
				$(tr).find('td:last-child').remove();
			}
		}

		td.append(schedule);

		// タイトルの省略
		var suffix = '...';
		var span = link.find('.shcedule_title');
		while (schedule.outerWidth(false) <= span.outerWidth(false)) {
			var txt = span.text();
			span.text(txt.substr(0, txt.length - 2 - suffix.length));
			span.text(span.text() + '...');
		}
	}
};

$(document).ready(Month.init);
