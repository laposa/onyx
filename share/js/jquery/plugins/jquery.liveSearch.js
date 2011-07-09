/**
 * LiveSearch (requires the dimensions plug-in)
 *
 * Applies "live search" to input-fields
 *
 * Usage: jQuery('#q').liveSearch({ajaxURL: '/ajax/search/?q='});
 *
 * @class liveSearch
 * @param {Object} conf, custom config-object
 *
 * Copyright (c) 2008 Andreas Lagerkvist (andreaslagerkvist.com)
 * Released under a GNU General Public License v3 (http://creativecommons.org/licenses/by/3.0/)
 */
// Hide all search-results if you click outside them
$(document.body).click(function(event) {
	if(!$(event.target).parents('div.live-search-results').length) {
		jQuery('div.live-search-results').slideUp(300);
	}
});
jQuery.fn.liveSearch = function(conf) {
	var config = jQuery.extend({
		ajaxURL: '/mod/search-results.php?q='
	}, conf);

	return this.each(function() {
		var input		= jQuery(this);
		var tmpOffset	= input.offset();
		var inputDim	= {
			left:	tmpOffset.left, 
			top:	tmpOffset.top, 
			width:	input.outerWidth(), 
			height:	input.outerHeight()
		};
		var results			= jQuery('<div class="live-search-results"></div>').appendTo(document.body).hide().slideUp(0);
		var resultsShit		= parseInt(results.css('paddingLeft'), 10) + parseInt(results.css('paddingRight'), 10) + parseInt(results.css('borderLeftWidth'), 10) + parseInt(results.css('borderRightWidth'), 10);
		inputDim.topNHeight	= inputDim.top + inputDim.height;
		inputDim.widthNShit	= inputDim.width - resultsShit;
		results.css({
			position:	'absolute', 
			left:		inputDim.left +'px', 
			top:		inputDim.topNHeight +'px',
			width:		inputDim.widthNShit +'px'
		});

		input.keyup(function() {
			if(this.value != this.lastValue) {
				input.addClass('ajax-loading');

				var q = this.value;

				if(this.timer) {
					clearTimeout(this.timer);
				}

				this.timer = setTimeout(function() {
					jQuery.get(config.ajaxURL +q, function(data) {
						input.removeClass('ajax-loading');

						if(data.length) {
							results.html(data).slideDown(300);
						}
						else {
							results.slideUp(300);
						}
					});
				}, 200);

				this.lastValue = this.value;
			}
		});
	});
};