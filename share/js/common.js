/**
 * Norbert @ Laposa Ltd, 2009, 2012
 * TODO: create Onxshop global object and move all functions there
 *
 */

/**
 * open new window with unique name
 */
 
function nOpenWin(src, width, height) {
	window.open(src,'nWin'+unixtime(),'width='+width+',height='+height+',toolbar=0,directories=0,menubar=0,status=no,resizable=1,location=0,scrollbars=1,dialog=0,modal=0');
}

/**
 * open image in popup window
 */
 
function openImg(src) {
    url = '/popupimage/' + src;
    nOpenWin(url, 400, 300);
}

/**
 * unixtime used in nOpenWin
 */
 
function unixtime() {
	var unixtime = new Date().getTime();
	unixtime = unixtime/1000;
	unixtime = parseInt(unixtime);
	return unixtime;
}

/**
 * HTML snippet for AJAX loader
 */
 
var onxshop_load_indicator_html_snippet = "<div style='width: 100%; padding-top: 10px; text-align: center;'><img src='/share/images/ajax-indicator/indicator_facebook.gif' alt='Loading ...'/></div>";

/**
 * ajax loader
 */
 
function makeAjaxRequest(jquery_selector, url, complete_callback) {
    jQuery(jquery_selector).html(onxshop_load_indicator_html_snippet).load(url, '', function (responseText, textStatus, XMLHttpRequest) {
			popupMessage( jquery_selector + ' div.onxshop_messages');
			if (jQuery.isFunction(complete_callback)) complete_callback();
		}
	);
}

/**
 * make request using Ajax and initiate AjaxForm
 */

function makeAjaxRequestWithForm(selector, component) {
	makeAjaxRequest(selector, component, function(){initComponentAjaxForm(selector)});
}

function initComponentAjaxForm(component_selector) {
	var options = {
		target: component_selector,
		success: function(responseText, statusText) {
			initComponentAjaxForm(component_selector);
			popupMessage(component_selector + ' div.onxshop_messages');
		}
	};
    $(component_selector + ' form').ajaxForm(options);
}

function showModalOverlay() {
	var html = '<div id="modal_overlay" class="off">' +
		'<div id="model_click_zone" onclick="hideModalOverlay()"></div>' +
		'<div id="modal_overlay_window"></div></div>';
	$('html,body').addClass('noscroll');
	$('#backoffice').append(html);
	setTimeout(function() { $('#modal_overlay').removeClass('off'); }, 100);
}

function hideModalOverlay() {
	$('#modal_overlay').addClass('off');
	setTimeout(function() { 
		$('#modal_overlay').remove(); $('html,body').removeClass('noscroll');
	}, 150);
}

/*
IE6, IE7 but IE8 BUTTON FIX 
used in 
./component/ecommerce/address_edit.html
./component/ecommerce/basket_edit.html

idea has been given by articel: http://www.peterbe.com/plog/button-tag-in-IE
by Marc Pujol shadow@la3.org
8th February 2006

Modified by Norby, 14/03/2006
Customized for jQuery 16/09/2008
*/

//window.onload = button_fix();

function button_fix(onlyInt) {
    if (jQuery.browser.msie) {
        if (parseInt(jQuery.browser.version) < 8) {
            var btns = document.getElementsByTagName('button');
            for(var i=0;i<btns.length;i++) {
                btns[i].onclick = function() {
                    var btns = document.getElementsByTagName('button');
                    for (var i=0;i<btns.length;i++) {
                        if (btns[i] != this) btns[i].disabled = true;
                    }
                    this.style.visibility = "hidden";

                    if (onlyInt == true) this.innerHTML = parseInt(this.className);
                    else this.innerHTML = this.className;
                    return true;
                }
            }
        }
    }
}

/**
 * form input element placeholder support for IE8
 */
 
function placeholder_fix() {
	
	// Format all elements with the placeholder attribute and insert it as a value
	if(!('placeholder'in document.createElement("input"))){
		$('[placeholder]').each(function() {
			if ($(this).val() == '') {
				$(this).val($(this).attr('placeholder'));
				$(this).addClass('placeholder');
			}
			$(this).focus(function() {
				if ($(this).val() == $(this).attr('placeholder') && $(this).hasClass('placeholder')) {
					$(this).val('');
					$(this).removeClass('placeholder');
				}
			}).blur(function() {
				if($(this).val() == '') {
					$(this).val($(this).attr('placeholder'));
					$(this).addClass('placeholder');
				}
			});
		});
		
		// Clean up any placeholders if the form gets submitted
		$('[placeholder]').parents('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				if ($(this).val() == $(this).attr('placeholder') && $(this).hasClass('placeholder')) {
					$(this).val('');
				}
			});
		});
		
		// Clean up any placeholders if the page is refreshed
		window.onbeforeunload = function() {
			$('[placeholder]').each(function() {
				if ($(this).val() == $(this).attr('placeholder') && $(this).hasClass('placeholder')) {
					$(this).val('');
				}
			});
		};
	}
}

/*
 * implement indexOf for browsers without it (IE8)
 */
 
if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(obj, start) {
		for (var i = (start || 0), j = this.length; i < j; i++) {
			if (this[i] === obj) { return i; }
		}
		return -1;
	}
}

/**
 * ajax loader in Growl
 */
 
function openAjaxRequestInGrowl(url, title) {
	jQuery.jGrowl('<div class="onxshop_messages in_jGrowl"><img src="/share/images/ajax-indicator/ajax-loader-bar.gif" alt="Loading ..."/></div>', {
		beforeOpen: function(e, m, o) {
			jQuery("#dialog").hide().load(url, '', 
				function (responseText, textStatus, XMLHttpRequest) {
					popupMessage("#dialog div.onxshop_messages");
				});
		}
	});
	
}

/**
 * system messages
 */

function popupMessage(selector) {
	jQuery.each(jQuery(selector), function() {
		var message = jQuery(this).hide().html();
		if (message) growlMessage(message);
	});
}

function growlMessage(message) {
	var life = 30 * message.length; // 30ms per character
	if (life < 4000) life = 4000; // 4 sec at min.
	jQuery.jGrowl("<div class='onxshop_messages in_jGrowl' role='alert'>" + message + "</div>", {life: life})
}

/**
 * Animated scroll to a specific element
 */
function scrollToElement(element) {
	$('html, body').animate({
		scrollTop: $(element).offset().top
	}), 2000;
}

/**
 * get csrf token
 */
function getCSRFToken() {
	return $("head > meta[name=csrf_token]").attr("content");
}
