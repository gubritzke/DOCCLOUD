$( function(){		$('.carousel-1').owlCarousel({		items: 1,		nav: true,        navText: ["",""],        dots: true	});		$('.carousel-2').owlCarousel({		items: 2,		nav: true,        navText: ["",""],        dots: true,        slideSpeed : 4000,        autoplay: true,        loop: true,        responsive : {        	0: {            	items: 1,            },        	680: {            	items: 2,            }        }	});	$('.carousel-3').owlCarousel({		items: 3,		nav: true,        navText: ["",""],        dots: false,        responsive : {        	0: {            	items: 1,            },        	680: {            	items: 2,            },            900: {            	items: 3,            }        }    });		$('.carousel-4').owlCarousel({		items: 4,		nav: true,        navText: ["",""],        dots: false,        responsive : {        	0: {            	items: 2,            },        	680: {            	items: 2,            },            900: {            	items: 3,            },            1100: {            	items: 4,            }        }    });	$('.carousel-5').owlCarousel({		items: 5,		nav: true,        navText: ["",""],        dots: false,        responsive : {        	0: {            	items: 2,            },        	680: {            	items: 2,            },            900: {            	items: 3,            },            1000: {            	items: 4,            },            1100: {            	items: 5,            }        }    });	});