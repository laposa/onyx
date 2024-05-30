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
        //popupMessage("#onyx-dialog div.onyx-messages");
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

/**
 * Hook show advanced settings button click
 * and set its state as per saved state
 */
function initAdvancedSettingsButton() {

    if (window.localStorage) {
        if (localStorage.getItem("show-advanced-settings") == 'true') {
            $('div.page-content .advanced').show();
            $("a.show-advanced-settings span").html('Hide Advanced Settings');
        }
    }

    $("a.show-advanced-settings").click(function(e) {
        showAdvancedSettings(this);
        e.preventDefault();
        return false;
    });
}

/**
 * called on every init and update of backoffice forms
 */
 
function initBackofficeUI() {
    
    initAdvancedSettingsButton();

    // mark disabled options
    $('select option.disabled, select option.publish-0').append(' (not public)');

    $("#menu-back-office a, #menu-editing-mode a, #menu-actions a.logout").mousedown(function(e) { 
        if (!e.altKey && !e.ctrlKey && !e.shiftKey && !e.metaKey && e.which == 1) {
            var body = $("#onyx-cms-content");
            body.fadeOut(500, function() {
                body.html('<img src="/share/images/ajax-indicator/indicator_facebook.gif" alt="Loading..." style="position: fixed; width: 16px; height: 11px; top: 50%; left: 50%; margin: -5px 0 0 -8px;"/>');
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
    $(window).bind("popstate", function() {
        window.location = location.href
    });
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
