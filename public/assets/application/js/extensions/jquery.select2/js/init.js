$(init);

function init()
{
	$.each($('.select2'), function(){
		
		var content = $(this);
		var tags = content.data('tags');
		
		content.select2({
			tags: tags,
		});
		
	});
	
}