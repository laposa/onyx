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
	jQuery.jGrowl("<div class='onxshop_messages in_jGrowl'>" + message + "</div>", {life: 4000})
}
