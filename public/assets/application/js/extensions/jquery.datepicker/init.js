/* Brazilian initialisation for the jQuery UI date picker plugin. */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( [ "../widgets/datepicker" ], factory );
	} else {
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {
	datepicker.regional[ "pt-BR" ] = {
		closeText: "Fechar",
		prevText: "Anterior",
		nextText: "Próximo",
		currentText: "Hoje",
		monthNames: [ "Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro" ],
		monthNamesShort: [ "Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez" ],
		dayNames: [ "Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado" ],
		dayNamesShort: [ "Dom","Seg","Ter","Qua","Qui","Sex","Sáb" ],
		dayNamesMin: [ "Dom","Seg","Ter","Qua","Qui","Sex","Sáb" ],
		weekHeader: "Sm",
		dateFormat: "dd/mm/yy",
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: "" ,
		changeMonth: true,
		changeYear: true,
	};
	datepicker.setDefaults( datepicker.regional[ "pt-BR" ] );
	return datepicker.regional[ "pt-BR" ];
}) 
);

/* calendar default */
function initCalendarDefault()
{
	$(document).on('focus', '.calendar', function(){
		
		//destroy
		if( $("#ui-datepicker-div").is(":visible") ) return false;
		$(this).datepicker("destroy");
		
		//desabilitar os sabados
		var disabledSaturday = ($(this).data('disabledsaturday') == true) ? true : false;
		
		//desabilitar os domingos
		var disabledSunday = ($(this).data('disabledsunday') == true) ? true : false;
		
		$(this).datepicker({
			'minDate': $(this).data('mindate'),
			'maxDate': $(this).data('maxdate'),
			'beforeShowDay': function(date){
				
				//define o dia da semana de 0 a 6
				var day = date.getDay();
				
				//desabilita o sabado
			    if( (disabledSaturday) && (day == 6) ) return [];
			    
			    //desabilita o domingo
			    if( (disabledSunday) && (day == 0) ) return [];
				
			    return [date];
			},
		});
		
	});
}

/* calendar range */
function initCalendarRange()
{
	$( ".calendar_range_from" ).datepicker({
		onClose: function( selectedDate ) {
			$("#" + $(this).data('parent')).datepicker( "option", "minDate", selectedDate );
		}
	});
	
	$( ".calendar_range_to" ).datepicker({
		onClose: function( selectedDate ) {
			$("#" + $(this).data('parent')).datepicker( "option", "maxDate", selectedDate );
		}
	});
}

//init
$(function(){
	
	initCalendarDefault();
	initCalendarRange();
	
});