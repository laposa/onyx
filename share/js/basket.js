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
	});
	
}

function removeFromBasketAjaxAction(item_id) {

	$("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'remove': item_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
		popupMessage("#basket #basketWrapper div.onxshop_messages");
	});
	
}

function removeFromBasketVarietyAjaxAction(variety_id) {

	$("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'remove_variety_id': variety_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
		popupMessage("#basket #basketWrapper div.onxshop_messages");
	});
	
}

function addToBasketAjaxActionFromVarietyList(variety_id) {
    
    $('a.add_to_basket' + '.variety_id_' + variety_id).addClass('loading');
    $("#basket").addClass('loading');
    $("#basket #basketWrapper").load('/request/component/ecommerce/basket', {'add': variety_id, 'quantity': 1}, function (responseText, textStatus, XMLHttpRequest) {
        popupMessage("#basket #basketWrapper div.onxshop_messages");
        
        $("#basket").removeClass('loading');
        $('a.add_to_basket' + '.variety_id_' + variety_id).removeClass('loading').addClass('added');

    });
    
}
