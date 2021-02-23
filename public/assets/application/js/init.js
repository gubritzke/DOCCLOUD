$(document).ready(function(){
	
	//bloqueia scroll qnd menu tá aberto
	function setTopo(){
		if ($('div.fundo-preto-mobile').hasClass('active'))
	    $(window).scrollTop(0);
	}
	$(window).bind('scroll', setTopo);
	
	//start carousel
	$('.owl-carousel').owlCarousel({
	    loop:true,
	    nav:true,
	    dots: false,
	    responsiveClass:true,
	    responsive:{
			0:{
				margin:10,
				items:1,
				nav:true
			},
			800:{
				margin:10,
				items:2,
				nav:false
			},
			1200:{
				margin:23,
				items:3
			},
		}
	});
	
	//start carousel-mobile
	$('div.carousel-mobile').owlCarousel({
	    loop:true,
	    nav:true,
	    dots: false,
	    responsiveClass:true,
	    responsive:{
			0:{
				margin:0,
				items:1,
				nav:true
			},
			800:{
				margin:0,
				items:1,
				nav:true
			},
			1200:{
				margin:0,
				items:1,
				nav:true
			},
		}
	});
	
	//start carousel-mobile-1
	$('div.carousel-mobile-1').owlCarousel({
	    loop:true,
	    nav:false,
	    dots: false,
	    responsiveClass:true,
	    responsive:{
			0:{
				margin:40,
				items:1,
				stagePadding: 100,
				nav:false
			},
			800:{
				margin:40,
				items:1,
				stagePadding: 100,
				nav:false
			},
			1200:{
				margin:40,
				items:1,
				stagePadding: 100,
				nav:false
			},
		}
	});
	
	//start carousel-mobile-2
	$('div.carousel-mobile-2').owlCarousel({
	    loop:true,
	    nav:false,
	    dots: false,
	    responsiveClass:true,
	    responsive:{
			0:{
				margin:40,
				items:1,
				stagePadding: 140,
				nav:false
			},
			800:{
				margin:40,
				items:1,
				stagePadding: 140,
				nav:false
			},
			1200:{
				margin:40,
				items:1,
				stagePadding: 140,
				nav:false
			},
		}
	});
	
	//EFEITO ABRE MENU MOBILE
	function abrirMenu(){
		$('.menu-mobile').addClass('open');
		$('.menu-mobile').animate({'marginLeft':'0px'}, 300);
		$('.fundo-preto-mobile').addClass('active').show();
		$('.menu-mobile ul.primeira').show();
		setTimeout(function(){
			$('#menumobile a.fecharmenu').removeClass('fadeOut').addClass('animated fadeIn').show().delay(4000);
			$('.menu-mobile ul.primeira li.um').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
			setTimeout(function(){
				$('.menu-mobile ul.primeira li.dois').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
				setTimeout(function(){
					$('.menu-mobile ul.primeira li.tres').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
					setTimeout(function(){
						$('.menu-mobile ul.primeira li.quatro').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
						setTimeout(function(){
							$('.menu-mobile ul.primeira li.cinco').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
							setTimeout(function(){
								$('.menu-mobile ul.primeira li.seis').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
								setTimeout(function(){
									$('.menu-mobile ul.primeira li.sete').removeClass('hide flipOutX').addClass('animated flipInX').show(500);
								}, 250);
							}, 250);
						}, 250);
					}, 250);
				}, 250);
			}, 250);
	}, 400);
	}
	
	//EFEITO FECHA MENU MOBILE
	function fecharMenu() {
		$('div.menu-mobile').stop(true, true).animate({'marginLeft':'-550px'}, 300);
		$('.fundo-preto-mobile').stop(true, true).removeClass('active').hide();
		$('#menumobile a.fecharmenu').stop(true, true).removeClass('fadeIn').addClass('animated fadeOut').hide();
		$('div.menu-mobile ul.primeira').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.um').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.dois').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.tres').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.quatro').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.cinco').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.seis').stop(true, true).hide();
		$('.menu-mobile ul.primeira li.sete').stop(true, true).hide();
		$('div.menu-mobile > ul.primeira > li').stop(true, true).removeClass('flipInX').addClass('animated flipOutX').hide();
		$('div.menu-mobile').stop(true, true).removeClass('open');
		$('span.flaticon-arrow').stop(true, true).removeClass('flaticon-arrow').addClass('flaticon-arrow-down-sign-to-navigate');
		$('ul.esconder').stop(true, true).removeClass('bounceInLeft').hide();
	}
	
	//EFEITO ABRE MENU MOBILE
	$('.bars').click( function(){
		
		if ($('.menu-mobile').hasClass('open')) 
		{	
			fecharMenu();
		}
		else 
		{		
			abrirMenu();
		}
	});

	//EFEITO FECHA MENU MOBILE
	$('.fecharmenu').click(function(){ 
		fecharMenu()
	});
	
	//ANCHOR LINKS DESKTOP	
	$('div.topo a.vamos').click(function() {
		$("html, body").animate({ scrollTop: ($('.quadrolaran').offset().top)}, 800);
	});
	
});