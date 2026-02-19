function initFrontofficeEditUI() {

    $('a.onyx-new-content').live('click', function() {
        $($(this).attr('href') + ' > div.new-node').removeClass("onyx-highlight-new");
        var temp = $(this).attr('href').replace('#onyx-layout-container-','');
        var info = temp.split('-');
        var node_id = info[0];
        var container_id = info[1];
        //alert("node_id" + node_id + " container_id" + container_id);
        window.location = "/component-library?add_to_node_id=" + node_id + "&add_to_container=" + container_id;
        return false;
    }).live('mouseover', function(){
        var temp = $(this).attr('href').replace('#onyx-layout-container-','');
        var info = temp.split('-');
        var node_id = info[0];
        var container_id = info[1];
        $($(this).attr('href') + ' > div.new-node').addClass("onyx-highlight-new"); 
    }).live('mouseout', function(){ 
        var temp = $(this).attr('href').replace('#onyx-layout-container-','');
        var info = temp.split('-');
        var node_id = info[0];
        var container_id = info[1];
        $($(this).attr('href') + ' > div.new-node').removeClass("onyx-highlight-new"); 
    });

}

/**
 * on ready
 */

$(function() {

    // all done in bo/component/fe_edit_mode

});

/**
 * Delete node icon
 */
function refreshDeleteContent() {
    
    $('a.onyx-trash').live('click', function() {
        var node_id = $(this).attr('href').replace('#','');
        $("#onyx-dialog").dialog({
            bgiframe: true,
            autoOpen: false,
            title: 'Move content to bin',
            modal: true,
            open: function() {
                $('#onyx-fe-edit-node-id-'+node_id).addClass('onyx-highlight-edit')
            }, 
            close: function() {
                $('#onyx-fe-edit-node-id-'+node_id).removeClass('onyx-highlight-edit');
                $('#onyx-dialog').empty();
            }
        });

        $('#onyx-dialog').load('/request/bo/component/node_bin~id=' + node_id + ':trash=1~');
        $('#onyx-dialog').dialog('open');
        return false;
        
    }).live('mouseover', function(){
        var node_id = $(this).attr('href').replace('#','');
        $('#onyx-fe-edit-node-id-'+node_id).addClass("onyx-highlight-delete"); 
    }).live('mouseout', function(){ 
        var node_id = $(this).attr('href').replace('#','');
        $('#onyx-fe-edit-node-id-'+node_id).removeClass("onyx-highlight-delete"); 
    });

}

/**
 * Duplicate content
 */


function duplicateNode(node_id) {
    $("#onyx-dialog").hide().load("/request/bo/component/node_duplicate~id="+node_id+"~", '', function (responseText, textStatus, XMLHttpRequest) {
            popupMessage("#onyx-dialog div.onyx-messages");
            $('#onyx-fe-edit-node-id-'+node_id).parent().after($("#onyx-dialog").html()).hide().slideDown("slow");
    });
}

/**
 * Add new node icon
 */

function refreshAddContent(selector) {
    $(selector).append('<div class="onyx-add-content new-node"><a class="onyx-new-content" title="Add New Content" href="#"><span>New Content</span></a></div>');
    $(selector).each(function () {
        $("a.onyx-new-content", this).attr("href", "#" + this.id);
    });
}

function feEditStartDragDrop() {
    $(".onyx-layout-container").sortable({
        connectWith: '.onyx-layout-container',
        forcePlaceholderSize: true,
        forceHelperSize: true,
        scroll: true,

        update: function(event, ui) {
            //feEditDragDrop(event, ui);
        },
        receive: function(event, ui) {
            //feEditDragDrop(event, ui);
        },
        over: function(event, ui) {
            //sortable_selected_id = $(event.target).attr('id');
        },
        change: function(event, ui) {
            //sortable_selected_id = $(event.target).attr('id');
        },
        stop: function(event, ui) {
            feEditDragDrop(event, ui);
        },
        activate: function(event, ui) {
            
        }

    });

    $(".onyx-layout-container > div").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all");
    $(".onyx-layout-container > div").disableSelection();
}

function feEditDragDrop(event, ui) {
    var source_node_id = $(ui.item).attr('id').replace('node-id-', '');
    var position = $(ui.item).parent().children().index(ui.item);
    var destination_id = $(ui.item).parent().attr('id');
    //var temp = $(event.target).attr('id').replace('onyx-layout-container-', '').split('-');
    var temp = destination_id.replace('onyx-layout-container-', '').split('-');
    var destination_node_id = temp[0];
    var destination_container_id = temp[1];

    console.log("receive: Source id " + source_node_id + ", Destination id " + destination_node_id + ", Destination container " + destination_container_id + ", Position " + position);
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
