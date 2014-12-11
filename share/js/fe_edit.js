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
			/*buttons: {
				'Remove': function() {
					alert('remove');
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},*/
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
		var node_id = $(this).attr('href').replace('#','');
		$('#node_id_' + node_id).addClass("onxshop_highlight_delete"); 
	}).live('mouseout', function(){ 
		var node_id = $(this).attr('href').replace('#','');
		$('#node_id_' + node_id).removeClass("onxshop_highlight_delete"); 
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
	//alert("node_id" + node_id + " container_id" + container_id);
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
}).live('mouseover', function(){
	var temp = $(this).attr('href').replace('#onxshop_layout_container_','');
	var info = temp.split('_');
	var node_id = info[0];
	var container_id = info[1];
	$($(this).attr('href') + ' > div.new_node').addClass("onxshop_highlight_new"); 
}).live('mouseout', function(){ 
	var temp = $(this).attr('href').replace('#onxshop_layout_container_','');
	var info = temp.split('_');
	var node_id = info[0];
	var container_id = info[1];
	$($(this).attr('href') + ' > div.new_node').removeClass("onxshop_highlight_new"); 
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
	$.post("/request/bo/component/node_move", {
		csrf_token: getCSRFToken(),
		source_node_id: source_node_id,
		destination_node_id: destination_node_id,
		container: destination_container_id,
		position: position}, 
		function (data) {
			popupMessage(data);
		});
	return false;
}
