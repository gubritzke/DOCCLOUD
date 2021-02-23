if( typeof jQuery.fn.fancybox === 'function' )
{
	$( function(){
		$(".fancybox").fancybox();
	});
}
function fancybox_open(url,width,height)
{
	$.fancybox.open({
	    type: 'iframe',
	    href: url, 
	    minWidth: width,
	    maxWidth: width,
	    minHeight: height,
	    maxHeight: height,
	    helpers: {
	    	overlay: {
	            locked: false
	        }
	    }
	});
}
function fancybox_content(content,width,height)
{
	$.fancybox.open({
		type: 'iframe',
	    content: $(content).show(), 
	    minWidth: width,
	    maxWidth: width,
	    minHeight: height,
	    maxHeight: height,
	    helpers: {
	    	overlay: {
	            locked: false
	        }
	    }
	});
}