$(document).ready(function(){
	
	setTimeout(() => {
		$('.animar1').removeClass('hide').addClass('animated fadeInDown');
		setTimeout(() => {
			$('.animar2').removeClass('hide').addClass('animated pulse');
			setTimeout(() => {
				$('.animar2-1').removeClass('hide').addClass('animated pulse');
				$('.animar3').removeClass('hide').addClass('animated flipInY');
				setTimeout(() => {
					$('.animar4').removeClass('hide').addClass('animated bounceInUp');
				}, 250);
			}, 450);
		}, 450);
	}, 250);
	
//	//efeito do parte1
//	var eventFired = false, objectPositionTop = $('.posicaobanner').offset().top;
//	
//	$(window).on('scroll', function() {
//		var currentPosition = $(document).scrollTop();
//		if (currentPosition > objectPositionTop && eventFired === false)
//		{
//			eventFired = true;
//			$('.flip1').removeClass('hide flipOutX').addClass('animated flipInX');
//			time1 = setTimeout(() => {
//				$('.flip2').removeClass('hide flipOutX').addClass('animated flipInX');
//				time2 = setTimeout(() => {
//					$('.flip3').removeClass('hide flipOutX').addClass('animated flipInX');
//				}, 250);
//			}, 250);	
//		}
//		else if(currentPosition < objectPositionTop && eventFired === true)
//		{
//			$('.flip1').stop(true, true).removeClass('flipInX').addClass('flipOutX');
//			clearTimeout(time1);
//			$('.flip2').stop(true, true).removeClass('flipInX').addClass('flipOutX');
//			clearTimeout(time2);
//			$('.flip3').stop(true, true).removeClass('flipInX').addClass('flipOutX');
//			eventFired = false;
//		}
//	});
	
//	//efeito do parte2
//	var eventFired1 = false, objectPositionTop1 = $('.posicaoquadrobranco').offset().top;
//	
//	$(window).on('scroll', function() {
//		var currentPosition1 = $(document).scrollTop();
//		if (currentPosition1 > objectPositionTop1 && eventFired1 === false)
//		{
//			eventFired1 = true;
//			$('.fromleftatend').removeClass('hide fadeOutLeft').addClass('animated fadeInLeft');
//			$('.fromrightatend').removeClass('hide fadeOutRight').addClass('animated fadeInRight');
//			time3 = setTimeout(() => {
//				$('.sombraatend').css({'box-shadow':'0 0 5px 0 #00000030'});
//			}, 800);
//		}
//		else if(currentPosition1 < objectPositionTop1 && eventFired1 === true)
//		{
//			clearTimeout(time3);
//			$('.fromleftatend').removeClass('fadeInLeft').addClass('fadeOutLeft');
//			$('.fromrightatend').removeClass('fadeInRight').addClass('fadeOutRight');
//			$('.sombraatend').css({'box-shadow':'none'});
//			eventFired1 = false;
//		}
//	});
//	
//	//efeito do parte3
//	var eventFired2 = false, objectPositionTop2 = $('.posicaoatend').offset().top;
//	
//	$(window).on('scroll', function() {
//		var currentPosition2 = $(document).scrollTop();
//		if (currentPosition2 > objectPositionTop2 && eventFired2 === false)
//		{
//			eventFired2 = true;
//			$('.fromleftvendas').removeClass('hide fadeOutLeft').addClass('animated fadeInLeft');
//			$('.fromrightvendas').removeClass('hide fadeOutRight').addClass('animated fadeInRight');
//		}
//		else if(currentPosition2 < objectPositionTop2 && eventFired2 === true)
//		{
//			$('.fromleftvendas').removeClass('fadeInLeft').addClass('fadeOutLeft');
//			$('.fromrightvendas').removeClass('fadeInRight').addClass('fadeOutRight');
//			eventFired2 = false;
//		}
//	});
//	
//	//efeito do parte4
//	var eventFired3 = false, objectPositionTop3 = $('.posicaovendas').offset().top;
//	
//	$(window).on('scroll', function() {
//		var currentPosition3 = $(document).scrollTop();
//		if (currentPosition3 > objectPositionTop3 && eventFired3 === false)
//		{
//			eventFired3 = true;
//			$('.fadein').removeClass('hide fadeOut').addClass('animated fadeIn');
//			$('.fromleftescri').removeClass('hide fadeOutLeft').addClass('animated fadeInLeft');
//			$('.fromrightescri').removeClass('hide fadeOutRight').addClass('animated fadeInRight');
//			time4 = setTimeout(() => {
//				$('.sombraescri').css({'box-shadow':'0 0 5px 0 #00000030'});
//			}, 800);
//		}
//		else if(currentPosition3 < objectPositionTop3 && eventFired3 === true)
//		{
//			clearTimeout(time4);
//			$('.fadein').removeClass('fadeIn').addClass('fadeOut');
//			$('.fromleftescri').removeClass('fadeInLeft').addClass('fadeOutLeft');
//			$('.fromrightescri').removeClass('fadeInRight').addClass('fadeOutRight');
//			$('.sombraescri').css({'box-shadow':'none'});
//			eventFired3 = false;
//		}
//	});
//	
//	//efeito do parte4 mobile
//	var eventFired4 = false, objectPositionTop4 = $('.posicaovendasmob').offset().top;
//	
//	$(window).on('scroll', function() {
//		var currentPosition4 = $(document).scrollTop();
//		if (currentPosition4 > objectPositionTop4 && eventFired4 === false)
//		{
//			eventFired4 = true;
//			$('.fadein').removeClass('hide fadeOut').addClass('animated fadeIn');
//			$('.fromleftescri').removeClass('hide fadeOutLeft').addClass('animated fadeInLeft');
//			$('.fromrightescri').removeClass('hide fadeOutRight').addClass('animated fadeInRight');
//		}
//		else if(currentPosition4 < objectPositionTop4 && eventFired4 === true)
//		{
//			$('.fadein').removeClass('fadeIn').addClass('fadeOut');
//			$('.fromleftescri').removeClass('fadeInLeft').addClass('fadeOutLeft');
//			$('.fromrightescri').removeClass('fadeInRight').addClass('fadeOutRight');
//			$('.sombraescri').css({'box-shadow':'none'});
//			eventFired4 = false;
//		}
//	});
	
	//EFEITO ABRE SUBMENU DESKTOP
	function abrirSubMenu(opcao){
		$('form.busca').stop(true, true).hide();
		$('div.menubaixo').css({'marginTop':'30px', 'marginBottom':'37px'});
		$('div.menubaixo').css({'height':'122px'});
		$('div.menubaixo').show(600);
		$('ul.fechar').hide();
		$('ul'+opcao).show();
		$('form.busca').removeClass('open');
		$('div.boxcinza').delay(500).fadeIn(1000);
	}
	
	//EFEITO FECHA SUBMENU DESKTOP
	function fecharSubMenu(){
		$('form.busca').stop(true, true).hide();
		$('div.menubaixo').stop(true, true).hide();
		$('div.menubaixo').stop(true, true).css({'height':'0'});
		$('ul.fechar').stop(true, true).hide();
		$('div.boxcinza').stop(true, true).fadeOut();
		$('form.busca').removeClass('open');
	}
	
	//EFEITO ABRIR BUSCA DESKTOP
	function abreBusca(){
		$('div.boxcinza').hide();
		$('div.menubaixo').css({'marginTop':'14px', 'marginBottom':'0'});
		$('div.menubaixo').animate({'height':'54px'});
		$('div.menubaixo').show();
		$('ul.fechar').hide();
		$('form.busca').show();
		$('form.busca').addClass('open');
		$('input[name="busca"]').focus();
	}
	
	//QUANDO O MOUSE ENTRA NOS MENUS ABRE O SUBMENU
	$('a.produtos').mouseenter(function(){	
		abrirSubMenu('.produtos');
	});
	
	$('a.solucoes').mouseenter(function(){	
		abrirSubMenu('.solucoes');
	});
	$('a.planos').mouseenter(function(){	
		fecharSubMenu();
	});
	$('a.tecnologia').mouseenter(function(){
		fecharSubMenu();
	});
	$('a.telefonia').mouseenter(function(){	
		fecharSubMenu();
	});
	$('a.sobre').mouseenter(function(){	
		fecharSubMenu();
	});
	//FIM QUANDO O MOUSE ENTRA NOS MENUS ABRE O SUBMENU
	
	//QUANDO CLICA NA LUPA ABRE A BUSCA
	$('a.busca').click(function(){
		if ($('form.busca').hasClass('open'))		
			fecharSubMenu();
		else
			abreBusca();
	});	
	
	//QUANDO O MOUSE SAI DOS MENUS E ENTRA NA SECTION OU TOPO FECHA SUBMENU
	$('div.topo, section').mouseenter(function(){	
		fecharSubMenu();
	});
	
	//MOBILE
	//QUANDO CLICA NOS MENUS ABRE OS SUBMNEUS MOBILE
	$('a.produtosmobile, span.prod').click(function(){
		if ($('span.prod').hasClass('flaticon-arrow-down-sign-to-navigate'))
		{	
			$('span.prod').removeClass('flaticon-arrow-down-sign-to-navigate').addClass('flaticon-arrow');
			$('ul.prodmob').addClass('animated bounceInLeft').show();
			$('span.solu').removeClass('flaticon-arrow').addClass('flaticon-arrow-down-sign-to-navigate');
			$('ul.solumob').removeClass('bounceInLeft').hide();
		}
		else
		{
			$('span.prod').removeClass('flaticon-arrow').addClass('flaticon-arrow-down-sign-to-navigate');
			$('ul.prodmob').removeClass('bounceInLeft').hide();
		}
	});	
	$('a.solucoesmobile, span.solu').click(function(){
		if ($('span.solu').hasClass('flaticon-arrow-down-sign-to-navigate'))
		{
			$('span.solu').removeClass('flaticon-arrow-down-sign-to-navigate').addClass('flaticon-arrow');
			$('ul.solumob').addClass('animated bounceInLeft').show();
			$('span.prod').removeClass('flaticon-arrow').addClass('flaticon-arrow-down-sign-to-navigate');
			$('ul.prodmob').removeClass('bounceInLeft').hide();
		}
		else
		{
			$('span.solu').removeClass('flaticon-arrow').addClass('flaticon-arrow-down-sign-to-navigate');
			$('ul.solumob').removeClass('bounceInLeft').hide();
		}
	});	
	
	//QUANDO CLICA NA LUPAMOBILE ABRE A BUSCAMOBILE
	$('a.buscahide').click(function(){
		if ($('form.buscamob').hasClass('open'))		
		{
			$('form.buscamob').hide();
			$('form.buscamob').removeClass('open');
		}
		else
		{
			$('form.buscamob').show(600);
			$('form.buscamob').addClass('open');
		}
	});	
	
	
});