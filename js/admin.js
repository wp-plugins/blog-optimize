var WPSimpleOptimize = {
    init: function() {
        WPSimpleOptimize.setOnOffFunc();
		WPSimpleOptimize.setCheckboxAndRadio();
		jQuery('.label_check, .label_radio').click(function(){
			WPSimpleOptimize.setCheckboxAndRadio();
		});
    },
    removeSavedMessage: function() {
        jQuery('#saved_box').slideUp('slow')
    },
    setOnOffFunc: function() {
        jQuery('div.on-off').each(function() {
            if (jQuery(this).siblings('input[type=hidden]:first').attr('value') === '1') {
                jQuery(this).find('span').css({
                    marginLeft: 49
                })
            }
        });
        jQuery('div.on-off').bind('click', 
        function() {
            var hiddenInput = jQuery(this).siblings('input[type=hidden]:first');
            if (hiddenInput.attr('value') == '1') {
                jQuery(this).find('span').animate({
                    marginLeft: 2
                });
                hiddenInput.attr('value', '')
            } else {
                jQuery(this).find('span').animate({
                    marginLeft: 49
                });
                hiddenInput.attr('value', '1')
            }
        })
    },
    setCheckboxAndRadio: function() {
		if (jQuery('.label_check input').length) {
			jQuery('.label_check').each(function(){ 
				jQuery(this).removeClass('c_on');
			});
			jQuery('.label_check input:checked').each(function(){ 
				jQuery(this).parent('label').addClass('c_on');
			});                
		};
		if (jQuery('.label_radio input').length) {
			jQuery('.label_radio').each(function(){ 
				jQuery(this).removeClass('r_on');
			});
			jQuery('.label_radio input:checked').each(function(){ 
				jQuery(this).parent('label').addClass('r_on');
			});
		};
    }
};
jQuery(document).ready(function($){
	WPSimpleOptimize.init();
	$(window).bind("hashchange", function(event){
		if($.isFunction($.param.fragment) && $.param.fragment()!="")
		{
			var hash = decodeURIComponent($.param.fragment());
			hashSplit = hash.split("_");
			var id1, id2=null;
			if(hashSplit.length>1)
			{
				id1 = hashSplit[0];
				id2 = hash;
			}
			else
				id1 = hash;
			var tab = $('.plugin_options .menu [href="#' + id1 + '"]');
			$(".plugin_options .menu a").removeClass("selected");
			tab.addClass("selected");
			if(id2!=null)
			{
				$('.plugin_options .submenu a').removeClass("selected");
				$('.plugin_options .submenu [href="#' + id2 + '"]').addClass("selected");
			}
			$(".plugin_options .submenu, .plugin_options .subsettings").css("display", "none");
			tab.next(".submenu").css("display", "block");
			$(".plugin_options .settings").css("display", "none");
			$('.plugin_options #' + id1).css("display", "block");
			if(id2!=null)
				$('.plugin_options #' + id2).css("display", "block");
			else if(tab.next(".submenu").length)
			{
				$('.plugin_options .submenu a').removeClass("selected");
				$('.plugin_options .menu [href="#' + id1 + '"]+.submenu li:first a').addClass("selected");
				$('.plugin_options #' + id1 + " .subsettings:first").css("display", "block");
			}
		}
	}).trigger("hashchange");
	$('.plugin_options .menu a').click(function(){
		$.bbq.pushState($(this).attr("href"));
	});
});
jQuery(window).load(function() {
    if (jQuery('#saved_box').length) {
        setTimeout('WPSimpleOptimize.removeSavedMessage()', 3000)
    }
});