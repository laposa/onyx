/**
 * backoffice and fe_edit
 * Author: Norbert @ Laposa Ltd 2010, 2012
 * TODO: move to global Onxshop object
 */
 
function openEdit(url, el, ajax) {
	if (ajax) {
		openAjaxRequestInDialog(url, '');
	} else {
		nOpenWin(url, 800, 800);
	}
	
}

function openAjaxRequestInDialog(url, title) {
	$('#dialog').dialog( "destroy" );
	$('#dialog').html(onxshop_load_indicator_html_snippet).load(url, '', function (responseText, textStatus, XMLHttpRequest) {
		//popupMessage("#dialog div.onxshop_messages");
	})
	.dialog({
		width: 560, 
		modal: true, 
		overlay: {
			opacity: 0.5, 
			background: 'black'
		}, 
		close: function() {
			$('#dialog').empty()
		},
		title: title
	})
	.dialog('open');
}


function refreshOpener(path, id) {
	newlocation = '/'+path+'#node_id_'+id;
	opener.window.location.href = newlocation;
	opener.window.location.reload(true);
}

function refreshOpenerAjax(path, id) {
	if (opener.window.document.getElementById('node_id_' + id)) {
		opener.$('#node_id_' + id).load('/request/node?id=' + id + ' #node_id_' + id + ' > *', function() {
			opener.refreshAddContent('#node_id_' + id + ' div.onxshop_layout_container');
		});
	} else {
		refreshOpener(path, id);
	}
}
