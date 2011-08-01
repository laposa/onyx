/**
 * Norbert @ Laposa Ltd, 2009
 *
 */

function nOpenWin(src, width, height) {
	window.open(src,'nWin'+unixtime(),'width='+width+',height='+height+',toolbar=0,directories=0,menubar=0,status=no,resizable=1,location=0,scrollbars=1,dialog=0,modal=0');
}

function openImg(src) {
    url = '/popupimage/' + src;
    nOpenWin(url, 400, 300);
}

function unixtime() {
	var unixtime = new Date().getTime();
	unixtime = unixtime/1000;
	unixtime = parseInt(unixtime);
	return unixtime;
}

function makeAjaxRequest(jquery_selector, url, complete_callback) {
    $(jquery_selector).html("<img src='/share/images/ajax-indicator/indicator_verybig.gif' alt='Loading ...'/>").load(url, '', function (responseText, textStatus, XMLHttpRequest) {
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


function openAjaxRequestInGrowl(url, title) {
	$.jGrowl('<div class="onxshop_messages in_jGrowl"><img src="/share/images/ajax-indicator/ajax-loader-bar.gif" alt="Loading ..."/></div>', {
		beforeOpen: function(e, m, o) {
			$("#dialog").hide().load(url, '', 
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
	var message = $(selector).hide().html();
	if (message) growlMessage(message);
}

function growlMessage(message) {
	$.jGrowl("<div class='onxshop_messages in_jGrowl'>" + message + "</div>", {life: 4000})
}

$(function () {
	//disabled, requires fix IE CSS style and to display all message containers on page 
	//popupMessage("div.onxshop_messages");
});
