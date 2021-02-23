/** EXAMPLE:
<div class="countdown" data-endline="2017/08/09 10:00:00">
	<span class="week"></span>
	<span class="day"></span>
	<span class="hour"></span>
	<span class="minute"></span>
	<span class="second"></span>
</div>
*/

$(init);

function init()
{
	$.each($('.countdown'), function(){
		
		var content = $(this);
		var endline = content.data('endline');
		
		content.countdown(endline, function(event) {
			
			var week = event.strftime('%w');
			var day = event.strftime('%d');
			var hour = event.strftime('%H');
			var minute = event.strftime('%M');
			var second = event.strftime('%S');
			
			content.find('.week').html(week);
			content.find('.day').html(day);
			content.find('.hour').html(hour);
			content.find('.minute').html(minute);
			content.find('.second').html(second);
		});
		
	});
}