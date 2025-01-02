/**
 * backoffice and fe_edit
 * Author: Norbert @ Laposa Limited 2010-2020
 */
 
function openEdit(url, el, ajax) {
    if (ajax) {
        openAjaxRequestInDialog(url, '');
    } else {
        nOpenWin(url, 825, 800);
    }
    
}

function openAjaxRequestInDialog(url, title) {
    $('#onyx-dialog').html(onyx_load_indicator_html_snippet).load(url, '', function (responseText, textStatus, XMLHttpRequest) {
        htmx.process('#onyx-dialog');
    })
    .dialog({
        width: 560, 
        position: { my: "center", at: "top+20%", of: window},
        modal: true, 
        close: function() {
            $('#onyx-dialog').empty()
        },
        title: title
    })
    .dialog('open');
}


function refreshOpener(path, id) {
    newlocation = '/'+path+'#node-id-'+id;
    opener.window.location.href = newlocation;
    opener.window.location.reload(true);
}

function refreshOpenerAjax(path, id) {
    if (opener.window.document.getElementById('fe-edit-node-id-' + id)) {
        opener.$('#fe-edit-node-id-' + id).load('/request/node?id=' + id + ' #fe-edit-node-id-' + id + ' > *', function() {
            opener.refreshAddContent('#fe-edit-node-id-' + id + ' div.onyx-layout-container');
        });
    } else {
        refreshOpener(path, id);
    }
}
 
function clearOnyxCache(button) {
    var buttonIcon = $(button).find('a');
    
    $(button).addClass('onyx-effect-spin');
    jQuery("#onyx-dialog").hide().load('/request/bo/component/tools~tool=flush_cache~', '', 
        function (responseText, textStatus, XMLHttpRequest) {
            popupMessage("#onyx-dialog div.onyx-messages");
            $(button).removeClass('onyx-effect-spin');
        });
}

function showAdvancedSettings(source) {
    var span = $(source).find('span');
    var label = span.text();
    if (label.indexOf("Show") >= 0) {
        $('div.page-content .advanced').slideDown(600);
        span.html('Hide Advanced Settings');
        if (window.localStorage) localStorage.setItem('show-advanced-settings', 'true');
    } else {
        $('div.page-content .advanced').slideUp(600);
        span.html('Show Advanced Settings');
        if (window.localStorage) localStorage.setItem('show-advanced-settings', 'false');
    }
    return false;
}

function initAdvancedSettingsButton() {
    if (window.localStorage) {
        if (localStorage.getItem("show-advanced-settings") == 'true') {
            $('div.page-content .advanced').show();
            $("a.show-advanced-settings span").html('Hide Advanced Settings');
        }
    }
};

/**
 * called on every init and update of backoffice forms
 */
 
function initBackofficeUI() {
    /**
     * Hook show advanced settings button click
     * and set its state as per saved state
     */

    initAdvancedSettingsButton();
    $(document).on('click', 'a.show-advanced-settings', function(e) {
        e.preventDefault();
        showAdvancedSettings(this);
        return false;
    });

    // mark disabled options
    $('select option.disabled, select option.publish-0').append(' (not public)');

    $("#menu-back-office a, #menu-editing-mode a, #menu-actions a.logout").mousedown(function(e) { 
        if (!e.altKey && !e.ctrlKey && !e.shiftKey && !e.metaKey && e.which == 1) {
            var body = $("#onyx-cms-content");
            body.fadeOut(500, function() {
                body.html('<img src="/share/images/loading.svg" alt="Loading..." style="position: fixed; width: 16px; height: 11px; top: 50%; left: 50%; margin: -5px 0 0 -8px;"/>');
                body.fadeIn(300);
            }); 
            var targetUrl = $(this).attr("href");
            setTimeout(function() { window.location = targetUrl; }, 5000); // try again after 5 seconds
        }
    });
    
    // add feedback on save button
    $('button.save').on('click', function() {
        console.log('SAVING CLASS : A');
        $(this).addClass('saving');
    });

    // create dialog
    $("<div/>").attr('id','onyx-dialog').appendTo('body');

    // fix for history.pushState so page refreshes on back button
    // TODO temporarily disabled, causes "Maximum call stack size exceeded" error   
    // $(window).bind("popstate", function() {
    //     window.location = location.href
    // });
}

/**
 * on ready
 */

$(function() {

    initBackofficeUI();

});
    
/**
 * jQuery UI comboxo widget
 */
$.widget("custom.combobox", {
    _create: function() {
        this.wrapper = $("<span>").addClass("custom-combobox").insertAfter(this.element);
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
    },

    _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
        value = selected.val() ? selected.text() : "";

        this.input = $("<input>").appendTo(this.wrapper).val( value).attr("title", "").addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left").autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source"),
        }).tooltip({tooltipClass: "ui-state-highlight"});

        this.input.data("ui-autocomplete")._renderItem = function(ul, item) {
            var c = '';
            if (item.option.disabled) c += 'disabled ';
            if ($(item.option).attr("data-class") == 'notpublic') c += 'notpublic ';
            return $("<li>").append("<a class=\"" + c + "\">" + 
                '<img src="/thumbnail/25x25/' + item.image + '" width="25" height="25" alt=""/>&nbsp;' +
                item.label + "</a>").appendTo(ul);
        };

        this._on(this.input, {
            autocompleteselect: function(event, ui) {
                ui.item.option.selected = true;
                this._trigger("select", event, {item: ui.item.option});
            },

            autocompletechange: "_removeIfInvalid"
        });
    },

    _createShowAllButton: function() {
        var input = this.input,
        wasOpen = false;

        $("<a>").attr("tabIndex", -1).appendTo(this.wrapper).button({
            icons: { primary: "ui-icon-triangle-1-s" },
            text: false
        }).removeClass("ui-corner-all").addClass("custom-combobox-toggle ui-corner-right").mousedown(function() {
            wasOpen = input.autocomplete("widget").is( ":visible" );
        }).click(function() {
            input.focus();
            if (wasOpen) return;
            input.autocomplete("search", "");
        });
    },

    _source: function(request, response) {
        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
        response(this.element.children("option").map(function() {
            var text = $(this).text();
            var image = $(this).attr("data-image");
            if (this.value && (!request.term || matcher.test(text))) return {
                label: text,
                value: text,
                image: image,
                option: this
            };
        }) );
    },

    _removeIfInvalid: function( event, ui ) {

        if ( ui.item ) return;

        var value = this.input.val(),
        valueLowerCase = value.toLowerCase(),
        valid = false;
        this.element.children("option").each(function() {
            if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                this.selected = valid = true;
                return false;
            }
        });

        if ( valid ) return;

        this.input.val("").attr("title", value + " didn't match any item").tooltip("open");
        this.element.val("");
        this._delay(function() { this.input.tooltip("close").attr("title", ""); }, 2500 );
        this.input.data("ui-autocomplete").term = "";
    },

    _destroy: function() {
        this.wrapper.remove();
        this.element.show();
    }
});


/** 
*   Onyx Node Actions
*/

function duplicateNode(id, parent_id, node_group) {
    $.get('/request/bo/component/node_duplicate~id='+id+'~', function(data) {
        popupMessage($(data).find("div.onyx-messages"));
        refreshNodeList(parent_id, node_group);
    });
    return false;
}

function trashNode(event, id) {
    event.preventDefault();
    $('#onyx-dialog').empty();
    $('#onyx-dialog').dialog({
        width: 500, 
        modal: true, 
        overlay: {
            opacity: 0.5, 
            background: 'black'
        }, 
        title: 'Move node to bin', 
        close: function() {
            $('#onyx-dialog').empty();
        },
    });

    makeAjaxRequest('#onyx-dialog', '/request/bo/component/node_bin~id='+id+':trash=1~');
    $('#onyx-dialog').dialog('open');
    return false;
}

function deleteNode(event, id) {
    event.preventDefault();
    $('#onyx-dialog').empty();
    $('#onyx-dialog').dialog({
        width: 500, 
        modal: true, 
        overlay: {
            opacity: 0.5, 
            background: 'black'
        }, 
        title: 'Delete node', 
        close: function() {
            $('#onyx-dialog').empty();
        },
    });

    makeAjaxRequest('#onyx-dialog', '/request/bo/component/node_delete~id='+id+':delete=1~');
    $('#onyx-dialog').dialog('open');
    return false;
}

function addNode(event, node_id, parent_node_group, specific_node_group = '') {
    event.preventDefault();
    $('#onyx-dialog').empty();
    if (parent_node_group == 'layout') {
        var container_id = prompt("Please enter container number you want to use for the new content.", "1");
        if (isNaN(container_id)) {
            alert(container_id + ' is not a valid number. It should be 1 or 2 for two columns layout.');
            return false;
        }
    } else {
        var container_id = 0;
    }

    var url = '/request/bo/component/node_add~node_group='+parent_node_group+':parent=' + node_id + ':container=' + container_id + ':expand_all=1:only_group=' + specific_node_group + '~';

    makeAjaxRequest('#onyx-dialog', url, function() {
        var button = '#node-add-form-' + node_id + '-' + container_id + '-wrapper button';
        $(button).after(' <a href="#" class="button remove" onclick="$(\'#onyx-dialog\').empty().dialog(\'close\'); return false;"><span>Cancel</span></a>');
    });
    $('#onyx-dialog').dialog({width: 500, modal: true, overlay: {opacity: 0.5, background: 'black'}, title: 'Add new node'}).dialog('open');
    return false;
}

function refreshNodeList(id, node_group) {

    switch(node_group) {
        case 'content':
            refreshCards(id);
            break;
        case 'page':
            refreshPages(id);
            break;
        default:
            break;
    }
    refreshNodes(id);
}

function refreshCards(id) {
    if ($('#content-list-' + id).length > 0) {
        var pods_refresh_url = '/request/bo/component/node_list_cards~id=' + id + ':node_group=content~';
        makeAjaxRequest('#content-list-' + id, pods_refresh_url);
    }
}

function refreshNodes(id) {
    if($('#child-list-' + id).length > 0) {
        var refresh_url = '/request/bo/component/node_list~id=' + id + '~';
        makeAjaxRequest('#child-list-' + id, refresh_url);
    }
}

function refreshPages(id) {
    if ($('#page-list-' + id).length > 0) {
        var pages_refresh_url = '/request/bo/component/node_list_pages~id=' + id + ':node_group=page~';
        makeAjaxRequest('#page-list-' + id, pages_refresh_url, function() {
            makeAjaxRequest('#pages-node-menu', '/request/bo/component/node_menu~id=0:open=0:expand_all=1:publish=0~');
        });
    }
}

function repositionNode(event, ui, node_group = '') {
    var source_node_id = $(ui.item).find('.fakelink').attr('href').match("[0-9]{1,}$");
    var destination_node_id = $(ui.item).closest('.root').find('.fakelink').attr('href').match("[0-9]{1,}$");
    var position = $(ui.item).parent().children().index(ui.item);
    
    $.post(
        "/request/bo/component/node_move", 
        {
            csrf_token: getCSRFToken(),
            source_node_id: source_node_id[0],
            destination_node_id: destination_node_id[0],
            position: position
        }, 
        function (data) {
            popupMessage(data);
            refreshNodeList(destination_node_id, node_group);
        }
    );
}

$(document).on('click', '.fakelink, #content-list button, #page-list button, #node-list button', function(e) {
    e.preventDefault();
});
