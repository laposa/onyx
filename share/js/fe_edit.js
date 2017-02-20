/**
 * Delete node icon
 */
function refreshDeleteContent() {
	$('a.onxshop-delete').live('click', function() {
		var node_id = $(this).attr('href').replace('#','');
		$("#dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			title: 'Delete content',
			modal: true,
			open: function() {
				$('#node-id-'+node_id).addClass('onxshop-highlight-edit')
			}, 
			close: function() {
				$('#node-id-'+node_id).removeClass('onxshop-highlight-edit');
				$('#dialog').empty();
			}
		});

		$('#dialog').load('/request/bo/component/node_delete~id=' + node_id + ':delete=1~');
		$('#dialog').dialog('open');
		return false;
		
	}).live('mouseover', function(){
		var node_id = $(this).attr('href').replace('#','');
		$('#node-id-'+node_id).addClass("onxshop-highlight-delete"); 
	}).live('mouseout', function(){ 
		var node_id = $(this).attr('href').replace('#','');
		$('#node-id-'+node_id).removeClass("onxshop-highlight-delete"); 
	});

}

/**
 * Duplicate content
 */


function duplicateNode(node_id) {
	$("#dialog").hide().load("/request/bo/component/node_duplicate~id="+node_id+"~", '', function (responseText, textStatus, XMLHttpRequest) {
			popupMessage("#dialog div.onxshop-messages");
			$('#node-id-'+node_id).after($("#dialog").html()).hide().slideDown("slow");
			//not perfect
			var inserted_node_id = $('#node-id-'+node_id).next().next().attr('id');
			refreshAddContent("#" + inserted_node_id + ' div.onxshop-layout-container');
	});
}

/**
 * Add new node icon
 */

function refreshAddContent(selector) {
	$(selector).append('<div class="onxshop-add-content new-node"><a class="onxshop-new-content" title="Add New Content" href="#"><span>New Content</span></a></div>');
	$(selector).each(function () {
		$("a.onxshop-new-content", this).attr("href", "#" + this.id);
	});
}

function addNode(parent_node_id) {
	$("#dialog").hide().load("/request/bo/component/node_add~parent="+parent_node_id+"~", '', function (responseText, textStatus, XMLHttpRequest) {
			popupMessage("#dialog div.onxshop-messages");
			$('#node-id-'+node_id).append($("#dialog").html()).hide().slideDown("slow");
	});
}

$('a.onxshop-new-content').live('click', function() {
	$($(this).attr('href') + ' > div.new-node').removeClass("onxshop-highlight-new");
	var temp = $(this).attr('href').replace('#onxshop-layout-container-','');
	var info = temp.split('-');
	var node_id = info[0];
	var container_id = info[1];
	//alert("node_id" + node_id + " container_id" + container_id);
	$($(this).attr('href') + ' > div.new-node').load('/request/bo/component/node_add~node_group=content:parent=' + node_id + ':container=' + container_id + '~', '', function() {
		var button = '#node-add-form-' + node_id + '_' + container_id + '-wrapper button';
		var container = '#onxshop-layout-container-' + node_id + '-' + container_id;
		$(button).after(' or <a href="#" onclick="$(\'' + container + ' div.new-node\').remove(); refreshAddContent(\'' + container + '\'); return false;"><span>cancel</span></a>');
		
		$('#node-add-form_'+node_id+'-'+container_id+'-wrapper form').ajaxForm({ 
			target: '#node-add-form-'+node_id+'-'+container_id+'-wrapper',
			success: function(responseText, statusText) {
				popupMessage("#node-add-form-"+node_id+"-"+container_id+"-wrapper div.onxshop-messages");
				var refresh_url = '/request/node~id='+node_id+'~';
				$('#node-id-'+node_id).load(refresh_url, '', function () {
					refreshAddContent('#node-id-'+node_id+' div.onxshop-layout-container');
				});
			}
		});
	});
	return false;
}).live('mouseover', function(){
	var temp = $(this).attr('href').replace('#onxshop-layout-container-','');
	var info = temp.split('-');
	var node_id = info[0];
	var container_id = info[1];
	$($(this).attr('href') + ' > div.new-node').addClass("onxshop-highlight-new"); 
}).live('mouseout', function(){ 
	var temp = $(this).attr('href').replace('#onxshop-layout-container-','');
	var info = temp.split('-');
	var node_id = info[0];
	var container_id = info[1];
	$($(this).attr('href') + ' > div.new-node').removeClass("onxshop-highlight-new"); 
});


function feEditStartDragDrop() {
	$(".onxshop-layout-container").sortable({
		connectWith: '.onxshop-layout-container',
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

	$(".onxshop-layout-container > div").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all");
	$(".onxshop-layout-container > div").disableSelection();
}

function feEditDragDrop(event, ui) {
	var source_node_id = $(ui.item).attr('id').replace('node-id-', '');
	var position = $(ui.item).parent().children().index(ui.item);
	var destination_id = $(ui.item).parent().attr('id');
	//var temp = $(event.target).attr('id').replace('onxshop-layout-container-', '').split('-');
	var temp = destination_id.replace('onxshop-layout-container-', '').split('-');
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
