/* Author: Norbert @ Laposa Ltd 2010 */

$(document).ready(function() {
	//$('#basket').mouseenter(function(){$(this).addClass("highlight"); $('#basket div.content').show('slow');}).mouseleave( function() {$(this).removeClass("highlight"); $('#basket div.content').hide('slow');});
	//$('#basket').mouseenter(function(){$(this).addClass("highlight"); $('#basket div.content').show('fast');});
	$('#basket a.remove').live('click', function() {
		var variety_id = $(this).attr('href').replace(/[^0-9]/g, '');
		removeFromBasketAjaxAction(variety_id);
		
		return false;
	});
});


function addToBasketAjaxAction(variety_id) {
	
	$("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'add': variety_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
		popupMessage("#basket #basketWrapper div.onxshop_messages");
        // update basket_edit component if present
        if ($('.basket_edit').length > 0) $('.basket_edit').load(window.location + ' .basket_edit');
	});
	
}

function removeFromBasketAjaxAction(item_id) {

	$("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'remove': item_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
		popupMessage("#basket #basketWrapper div.onxshop_messages");
        // update basket_edit component if present
        if ($('.basket_edit').length > 0) $('.basket_edit').load(window.location + ' .basket_edit');
	});
	
}

function removeFromBasketVarietyAjaxAction(variety_id) {

	$("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'remove_variety_id': variety_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
		popupMessage("#basket #basketWrapper div.onxshop_messages");
        // update basket_edit component if present
        if ($('.basket_edit').length > 0) $('.basket_edit').load(window.location + ' .basket_edit');
	});
	
}

function addToBasketAjaxActionFromVarietyList(variety_id, quantity) {
    
    $('a.add_to_basket' + '.variety_id_' + variety_id).addClass('loading');
    $("#basket").addClass('loading');
    quantity = quantity || 1;
    if (quantity < 1 && quantity > 20) quantity = 1;
    $("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'add': variety_id, 'quantity': quantity}, function (responseText, textStatus, XMLHttpRequest) {
        popupMessage("#basket #basketWrapper div.onxshop_messages");
        
        $("#basket").removeClass('loading');
        $('a.add_to_basket' + '.variety_id_' + variety_id).removeClass('loading').addClass('added');

        // update basket_edit component if present
        if ($('.basket_edit').length > 0) $('.basket_edit').load(window.location + ' .basket_edit');
    });
    
}

function trackBasketUpdate(action, sku, name, category, qty) {

	if (typeof(_gaq) == "undefined") return false;

	if (action == 'add' || action == 'Add') action = 'Add';
	else action = 'Remove';

	_gaq.push(['_trackEvent', 'Basket', action + '-SKU', sku, qty]);
	_gaq.push(['_trackEvent', 'Basket', action + '-Product', name, qty]);
	_gaq.push(['_trackEvent', 'Basket', action + '-Category', category, qty]);
}
