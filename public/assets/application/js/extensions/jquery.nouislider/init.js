$( function(){
	
	//inicia o range com slider
	initSliderRange();
	
});

function initSliderRange()
{
	var content = $('#sliderRange');
	var min = content.data('min');
	var max = content.data('max');
	var range_min = content.data('range_min');
	var range_max = content.data('range_max');
	
	var input0 = document.getElementById('sliderRangeMin');
	var input1 = document.getElementById('sliderRangeMax');
	var inputs = [input0, input1];
	
	noUiSlider.create(content[0], {
		start: [min, max],
		tooltips: true,
		step: 10,
		connect: true,
		range: {
			'min': [ range_min ],
			'max': [ range_max ]
		}
	});
	
	content[0].noUiSlider.on('update', function( values, handle ) {
		inputs[handle].value = values[handle];
	});
	
	content[0].noUiSlider.on('change', function(){
		content.parents('form').submit();
	});
	
}