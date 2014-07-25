/**
 * Highlight given element
 */
function feHighlightElement(element, type) {

	var highlight = $('#feHighlight');
	var offset = element.position();

	element.addClass('feHighlighted');

	var top = offset.top;
	var height = element.height();

	if (type == 'content' || type == 'layout') height -= 23;
	if (type == 'layout') top += 23;

	highlight.css('left', offset.left);
	highlight.css('top', top);
	highlight.css('width', element.width());
	highlight.css('height', height);

	highlight.show();
}

/**
 * Unhighlight given element
 */
function feUnhighlightElement(element) {

	var highlight = $('#feHighlight');
	element.removeClass('feHighlighted');
	highlight.hide();

}

/**
 * Set highlight type
 */
function feSetHighlightType(type) {

	var highlight = $('#feHighlight');

	if (type) highlight.addClass(type);
	else highlight.removeClass('delete edit add duplicate');

}

/**
 * Set handlers
 */
$(function() {
	$('body').addClass('feEditable').append('<div id="feHighlight"></div>');
	$('.onxshop_edit_content').live('mouseover', function() {
		var node_id = $(this).attr('data-node-id');
		var node = $('#node_id_' + node_id);
		feHighlightElement(node, 'content');
	});
	$('.onxshop_edit_layout').live('mouseover', function() {
		var node_id = $(this).attr('data-node-id');
		var node = $('#node_id_' + node_id);
		feHighlightElement(node, 'layout');
	});
	$('.onxshop_edit_content, .onxshop_edit_layout').live('mouseout', function() {
		var node_id = $(this).attr('data-node-id');
		var node = $('#node_id_' + node_id);
		feUnhighlightElement(node);
	});
});

$(document).keydown(function (e) {
    if (e.keyCode == 27) {
        var body = $(document.body);
        if (body.hasClass('feEditable')) body.removeClass('feEditable');
        else body.addClass('feEditable');
    }
});
/**
 * Delete node icon
 */
function refreshDeleteContent() {
	$('a.onxshop_delete').live('click', function() {
		var node_id = $(this).attr('href').replace('#','');
		$("#dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			title: 'Delete content',
			modal: true,
			open: function() {
				$('#node_id_' + node_id).addClass('onxshop_highlight_edit')
			}, 
			close: function() {
				$('#node_id_' + node_id).removeClass('onxshop_highlight_edit');
				$('#dialog').empty();
			}
		});

		$('#dialog').load('/request/bo/component/node_delete~id=' + node_id + ':delete=1~');
		$('#dialog').dialog('open');
		return false;
	}).live('mouseover', function(){
		feSetHighlightType('delete');
	}).live('mouseout', function(){ 
		feSetHighlightType();
	});

}

/**
 * Duplicate content
 */
function duplicateNode(node_id) {
	$("#dialog").hide().load("/request/bo/component/node_duplicate~id="+node_id+"~", '', function (responseText, textStatus, XMLHttpRequest) {
			popupMessage("#dialog div.onxshop_messages");
			$("#node_id_" + node_id).after($("#dialog").html()).hide().slideDown("slow");
			//not perfect
			var inserted_node_id = $("#node_id_" + node_id).next().next().attr('id');
			refreshAddContent("#" + inserted_node_id + ' div.onxshop_layout_container');
	});
}

/**
 * Add new node icon
 */

function refreshAddContent(selector) {
	$(selector).append('<div class="onxshop_add_content new_node"><a class="onxshop_new_content" title="Add New Content" href="#"><span>New Content</span></a></div>');
	$(selector).each(function () {
		$("a.onxshop_new_content", this).attr("href", "#" + this.id);
	});
}

function addNode(parent_node_id) {
	$("#dialog").hide().load("/request/bo/component/node_add~parent="+parent_node_id+"~", '', function (responseText, textStatus, XMLHttpRequest) {
			popupMessage("#dialog div.onxshop_messages");
			$("#node_id_" + parent_node_id).append($("#dialog").html()).hide().slideDown("slow");
	});
}

$('a.onxshop_new_content').live('click', function() {
	$($(this).attr('href') + ' > div.new_node').removeClass("onxshop_highlight_new");
	var temp = $(this).attr('href').replace('#onxshop_layout_container_','');
	var info = temp.split('_');
	var node_id = info[0];
	var container_id = info[1];
	$($(this).attr('href') + ' > div.new_node').load('/request/bo/component/node_add~node_group=content:parent=' + node_id + ':container=' + container_id + '~', '', function() {
		var button = '#node_add_form_' + node_id + '_' + container_id + '_wrapper button';
		var container = '#onxshop_layout_container_' + node_id + '_' + container_id;
		$(button).after(' or <a href="#" onclick="$(\'' + container + ' div.new_node\').remove(); refreshAddContent(\'' + container + '\'); return false;"><span>cancel</span></a>');
		
		$('#node_add_form_'+node_id+'_'+container_id+'_wrapper form').ajaxForm({ 
			target: '#node_add_form_'+node_id+'_'+container_id+'_wrapper',
			success: function(responseText, statusText) {
				popupMessage("#node_add_form_"+node_id+"_"+container_id+"_wrapper div.onxshop_messages");
				var refresh_url = '/request/node~id='+node_id+'~';
				$('#node_id_'+node_id).load(refresh_url, '', function () {
					refreshAddContent('#node_id_'+node_id+' div.onxshop_layout_container');
				});
			}
		});
	});
	return false;
});


function feEditStartDragDrop() {
	$(".onxshop_layout_container").sortable({
		connectWith: '.onxshop_layout_container',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		scroll: true,

		update: function(event, ui) {
			//growlMessage("update " + $(event.target).attr('id')); 
			//feEditDragDrop(event, ui);
		},
		receive: function(event, ui) {
			//growlMessage("receive " + $(event.target).attr('id'));
			//feEditDragDrop(event, ui);
		},
		over: function(event, ui) {
			//sortable_selected_id = $(event.target).attr('id');
			//growlMessage("over " + $(event.target).attr('id'));
		},
		change: function(event, ui) {
			//sortable_selected_id = $(event.target).attr('id');
			//growlMessage("change " + $(event.target).attr('id'));
		},
		stop: function(event, ui) {
			//growlMessage(sortable_selected_id);
			feEditDragDrop(event, ui);
			//growlMessage("stop " + $(event.target).attr('id'));
		},
		activate: function(event, ui) {
			//growlMessage("activate " + $(event.target).attr('id'));
		}

	});

	$(".onxshop_layout_container .node").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all");
	$(".onxshop_layout_container .node").disableSelection();
}

function feEditDragDrop(event, ui) {
	var source_node_id = $(ui.item).attr('id').replace('node_id_', '');
	var position = $(ui.item).parent().children().index(ui.item);
	var destination_id = $(ui.item).parent().attr('id');
	//var temp = $(event.target).attr('id').replace('onxshop_layout_container_', '').split('_');
	var temp = destination_id.replace('onxshop_layout_container_', '').split('_');
	var destination_node_id = temp[0];
	var destination_container_id = temp[1];

	//alert("receive: Source id " + source_node_id + ", Destination id " + destination_node_id + ", Destination container " + destination_container_id + ", Position " + position);
	feEditNodeMove(source_node_id, destination_node_id, destination_container_id, position);
	
	return false;
}


function feEditNodeMove(source_node_id, destination_node_id, destination_container_id, position) {
	openAjaxRequestInGrowl('/request/bo/component/node_move~source_node_id='+source_node_id+':destination_node_id='+destination_node_id+':container='+destination_container_id+':position='+position+'~', 'Move node');
	return false;
}
