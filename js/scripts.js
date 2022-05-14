jQuery(document).ready(function(){
	// Read more product category desc
	var closeHeight = '90px';
	var moreText 	= '';
	var lessText	= '';
	var duration	= '10000';
	var easing = 'linear';

	jQuery('.woocommerce-products-header > .category-description-top, .description-bottom-another').each(function() {
		var current = jQuery(this);
		current.data('fullHeight', current.height()).css('height', closeHeight);
		current.after('<a href="javascript:void(0);" class="more-link closed">' + moreText + '</a>');
	});

	var openSlider = function() {
		link = jQuery(this);
		var openHeight = link.prev('.woocommerce-products-header > .category-description-top, .description-bottom-another').data('fullHeight') + 'px';
		link.prev('.woocommerce-products-header > .category-description-top, .description-bottom-another').animate({'height': openHeight}, {duration: duration }, easing);
		link.text(lessText).addClass('open').removeClass('closed');
		link.unbind('click', openSlider);
		link.bind('click', closeSlider);
	}

	var closeSlider = function() {
		link = jQuery(this);
		link.prev('.woocommerce-products-header > .category-description-top, .description-bottom-another').animate({'height': closeHeight}, {duration: duration }, easing);
		link.text(moreText).addClass('closed').removeClass('open');
		link.unbind('click');
		link.bind('click', openSlider);
	}
	jQuery('.more-link').bind('click', openSlider);


	// Filter sidebar
	jQuery(".filter-box").simplerSidebarCss3({
		align: "right",
		toggler: ".filter-btn",
		quitter: ".quit-sidebar",
		freezePage: false,
	});


	jQuery(".account-reg-btn-box .custom-btn").click(function(){
		event.preventDefault();
		jQuery("#customer_login .u-column1").addClass("hide");
		jQuery("#customer_login .u-column2").removeClass("hide");
	});





	size_li = jQuery(".inv-list .wpb_wrapper .vc_row").size();
    x=3;
    jQuery('.inv-list .wpb_wrapper .vc_row:lt('+x+')').css('display', 'flex');
    jQuery('.loadMore').click(function () {
        x= (x+2 <= size_li) ? x+2 : size_li;
        jQuery('.inv-list .wpb_wrapper .vc_row:lt('+x+')').css('display', 'flex');
         jQuery('#showLess').css('display', 'flex');
        if(x == size_li){
            jQuery('.loadMore').css('display', 'none');
        }
    });
    jQuery('#showLess').click(function () {
        x=(x-2<0) ? 3 : x-2;
        jQuery('.inv-list .wpb_wrapper .vc_row').not(':lt('+x+')').css('display', 'none');
        jQuery('.loadMore').show();
         jQuery('#showLess').show();
        if(x == 3){
            jQuery('#showLess').hide();
        }
    });





if(jQuery(window).width() < 768) {
	jQuery("footer .widget .custom-menu").hide();
	jQuery("footer .widget_block .widgettitle").click(function (event) {
		event.preventDefault();
		if (jQuery(this).next().is(":hidden")) {
			jQuery("footer .widget_block .widgettitle").removeClass("active").next().slideUp();
			jQuery(this).toggleClass("active").next().slideDown()
		} else {
			jQuery("footer .widget_block .widgettitle").removeClass("active");
			jQuery(this).next().slideUp()
		}
	});
};

});
