/*

Another In Place Editor - a jQuery edit in place plugin

Copyright (c) 2009 Dave Hauenstein

Authors:
	Dave Hauenstein
	Martin Häcker <spamfaenger [at] gmx [dot] de>

To minify use the google closure compiler web interface at <http://closure-compiler.appspot.com/>

Patches welcomed! As long as they come with testcase included. For guidance see the tests at </spec/unit/spec.js>. To submit, just mail them to me. --Martin

License:
This source file is subject to the BSD license bundled with this package.
Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
If you did not receive a copy of the license, and are unable to obtain it,
email davehauenstein@gmail.com, and I will send you a copy.

Project home:
http://code.google.com/p/jquery-in-place-editor/

Version 1.1

TODO: 
- Support overriding individual options with the metadata plugin
- expand the interface to submit to functions to make it easier to integrate into custom applications
  (fold in show progress, offer callbacks for different lifecycle events, ...)
- support live events to trigger inline editing to ease highly dynamic websites better
- select on choosing if no buttons are shown (should be able to disable this if wanted)
- Demo should work without a server component
- Demo seems to be buggy as it discards content when the server does not answer
- Option to load URL for editor content
- Allow the editor to show up on hoverig above it (make sure to do it in a way that doesn't crash IE 7)
- Expand the function commit interface so it offers more controll (would be great if the 'saving' animation of editor could be controlled remotely - perhpas by giving a callback as one of the parameters?)
- custom animations during submit: show text, show spinner, show pulsing background animation
- Allow the user to chose if html source should be edited or text (default to text). Probably needs a way
  for the user to decide how the result should be embedded (think about editing wiki-text and inserting rendered html)
- Allow continous validation / transformation while the user types. I.e. wiki-edit with live preview (from server / function)
- allow aditional_params to be set as json/object that is then serialized with correct encoding on submit
- consider size parameter for the inline edit box (but this could also be accomplished through css I guess)
- consider to pass the original dom element instead of it's id in the callback function. (However $('#' + original_id) also accomplishes this)
- add validation callback to be asked before any submitting (this could also be called continuous while editing 
  (for each character?) maybe a dedicated callback for each would be valuable)
- Unify usage of text and html to take and deliver content to the inline editor and enable the user to choose with a setting (document carefully as that will change the default behaviour)

REFACT:
- include spinner image as data url into javascript
- consider to extract the inline error function
- consider to enable the client to specify a prefix / namespace for all classes in the inplace editor to make it easier to avoid clashes with outside css
- Rename settings so they make more sense (e.g. saving_image -> saving_image_url) but keep backwards compatible to old settings
  also make all settings consistent in their underscore usage (ie. don't use any. :)
  params -> extra_submit_parameters / context_parametes
- add hover-class so that it's easier to specify the class somewhere else

*/

(function($){

$.fn.editInPlace = function(options) {
	
	var settings = $.extend({}, $.fn.editInPlace.defaults, options);
	
	preloadImage(settings.saving_image);
	
	return this.each(function() {
		var dom = $(this);
		// This won't work with live queries as there is no specific element to attach this
		// one way to deal with this could be to store a reference to self and then compare that in click?
		if (dom.data('editInPlace'))
			return; // already an editor here
		dom.data('editInPlace', true);
		
		new InlineEditor(settings, dom).init();
	});
};

/// Switch these through the dictionary argument to $(aSelector).editInPlace(overideOptions)
$.fn.editInPlace.defaults = {
	url:				"", // string: POST URL to send edited content
	bg_over:			"#ffc", // string: background color of hover of unactivated editor
	bg_out:				"transparent", // string: background color on restore from hover
	hover_class:		"",  // string: class added to root element during hover. Will override bg_over and bg_out
	show_buttons:		false, // boolean: will show the buttons: cancel or save; will automatically cancel out the onBlur functionality
	save_button:		'<button class="inplace_save">Save</button>', // string: image button tag to use as “Save” button
	cancel_button:		'<button class="inplace_cancel">Cancel</button>', // string: image button tag to use as “Cancel” button
	params:				"", // string: example: first_name=dave&last_name=hauenstein extra paramters sent via the post request to the server
	field_type:			"text", // string: "text", "textarea", or "select";  The type of form field that will appear on instantiation
	default_text:		"(Click here to add text)", // string: text to show up if the element that has this functionality is empty
	textarea_rows:		10, // integer: set rows attribute of textarea, if field_type is set to textarea
	textarea_cols:		25, // integer: set cols attribute of textarea, if field_type is set to textarea
	select_text:		"Choose new value", // string: default text to show up in select box
	select_options:		"", // string or array: Used if field_type is set to 'select'. Can be comma delimited list of options 'textandValue,text:value', Array of options ['textAndValue', 'text:value'] or array of arrays ['textAndValue', ['text', 'value']]. The last form is especially usefull if your labels or values contain colons)
	saving_text:		"Saving...", // string: text to be used when server is saving information
	saving_image:		"", // string: uses saving text specify an image location instead of text while server is saving
	value_required:		false, // boolean: if set to true, the element will not be saved unless a value is entered
	element_id:			"element_id", // string: name of parameter holding the id or the editable
	update_value:		"update_value", // string: name of parameter holding the updated/edited value
	original_html:		"original_html", // string: name of parameter holding original_html value of the editable
	save_if_nothing_changed:	false,  // boolean: submit to function or server even if the user did not change anything
	on_blur:			"save", // string: "save" or null; what to do on blur; will be overridden if show_buttons is true
	callback:			null, // function: function to be called when editing is complete; cancels ajax submission to the url param. Prototype: function(idOfEditor, enteredText, orinalHTMLContent, settingsParams). The function needs to return the value that should be shown in the dom. Returning undefined means cancel and will restore the dom and trigger an error.
	success:			null, // function: this function gets called if server responds with a success
	error:				function(request) { // function: this function gets called if server responds with an error
							this.reportError("Failed to save value: " + request.responseText || 'Unspecified Error');
						},
	error_sink:			function(idOfEditor, errorString) { alert(errorString); } // function: gets id of the editor and the error. Make sure the editor has an id, or it will just be undefined. If set to null, no error will be reported
};


function InlineEditor(settings, dom) {
	this.settings = settings;
	this.dom = dom;
	this.originalHTML = null; // REFACT: rename, not sure what a better name would be though, preEditorHTML, savedHTML
	this.originalText = null; // REFACT: rename.. not sure about the best name yet
	this.didInsertDefaultText = false;
};
$.fn.editInPlace.InlineEditor = InlineEditor;

$.extend(InlineEditor.prototype, {
	
	init: function() {
		this.setDefaultTextIfNeccessary();
		this.connectEvents();
	},
	
	setDefaultTextIfNeccessary: function() {
		if('' !== this.dom.html())
			return;
		
		this.dom.html(this.settings.default_text);
		this.didInsertDefaultText = true;
	},
	
	connectEvents: function() {
		var that = this;
		this.dom
			.bind('mouseenter.editInPlace', function(){ that.mouseEnter(); })
			.bind('mouseleave.editInPlace', function(){ that.mouseLeave(); })
			.bind('click.editInPlace', function(){ that.handleClickOnClosedEditor(); });
	},
	
	mouseEnter: function() {
		this.addHover();
	},
	
	mouseLeave: function() {
		this.removeHover();
	},
	
	addHover: function() {
		if (this.settings.hover_class)
			this.dom.addClass(this.settings.hover_class);
		else
			this.dom.css("background-color", this.settings.bg_over);
	},
	
	removeHover: function() {
		if (this.settings.hover_class)
			this.dom.removeClass(this.settings.hover_class);
		else
			this.dom.css("background-color", this.settings.bg_out);
	},
	
	handleClickOnClosedEditor: function() {
		// prevent re-opening the editor when it is already open
		this.dom.unbind('.editInPlace');
		
		// deactivate hover while editor is open
		this.removeHover();
		
		if (this.didInsertDefaultText 
			&& this.dom.html() === this.settings.default_text) {
			this.dom.html('');
			this.didInsertDefaultText = false;
		}
		// save original text - for cancellation functionality
		this.originalHTML = this.dom.html();
		this.originalText = trim(this.dom.text());
		
		this.replaceContentWithEditor();
		this.connectEventsToEditor();
	},
	
	replaceContentWithEditor: function() {
		var buttons_html  = (this.settings.show_buttons) ? this.settings.save_button + ' ' + this.settings.cancel_button : '';
		var editorElement = this.createEditorElement(); // needs to happen before anything is replaced
		/* insert the new in place form after the element they click, then empty out the original element */
		this.dom.html('<form class="inplace_form" style="display: inline; margin: 0; padding: 0;"></form>')
			.find('form')
				.append(editorElement)
				.append(buttons_html);
	},
	
	createEditorElement: function() {
		if (-1 === $.inArray(this.settings.field_type, ['text', 'textarea', 'select']))
			throw "Unknown field_type <fnord>, supported are 'text', 'textarea' and 'select'";
		
		if ("select" === this.settings.field_type)
			return this.createSelectEditor();
		
		var editor = null;
		if ("text" === this.settings.field_type)
			editor = $('<input type="text"' + this.inputNameAndClass() + '/>');
		else if ("textarea" === this.settings.field_type)
			editor = $('<textarea' + this.inputNameAndClass() 
				+ 'rows="' + this.settings.textarea_rows + '" cols="' 
				+ this.settings.textarea_cols + '"></textarea>');
		
		editor.val(this.originalText);
		return editor;
	},
	
	inputNameAndClass: function() {
		return ' name="inplace_value" class="inplace_field" ';
	},
	
	createSelectEditor: function() {
		var editor = $('<select' + this.inputNameAndClass() + '>'
			+	'<option disabled="true" value="">' + this.settings.select_text + '</option>'
			+ '</select>');
		
		var optionsArray = this.settings.select_options;
		if ( ! $.isArray(optionsArray))
			optionsArray = optionsArray.split(',');
			
		for (var i=0; i<optionsArray.length; i++) {
			
			var currentTextAndValue = optionsArray[i];
			if ( ! $.isArray(currentTextAndValue))
				currentTextAndValue = currentTextAndValue.split(':');
			
			var value = trim(currentTextAndValue[1] || currentTextAndValue[0]);
			var text = trim(currentTextAndValue[0]);
			
			var selected = (value == this.originalText) ? 'selected="selected" ' : '';
			var option = $('<option ' + selected + ' ></option>').val(value).text(text);
			editor.append(option);
		}
		return editor;
		
	},
	
	connectEventsToEditor: function() {
		var that = this;
		function cancelEditorAction() {
			that.handleCancelEditor();
			return false; // stop event bubbling
		}
		function saveEditorAction() {
			that.handleSaveEditor();
			return false; // stop event bubbling
		}
		
		var form = this.dom.find("form");
		
		form.find(".inplace_field").focus().select();
		form.find(".inplace_cancel").click(cancelEditorAction);
		form.find(".inplace_save").click(saveEditorAction);
		
		if ( ! this.settings.show_buttons) {
				// TODO: Firefox has a bug where blur is not reliably called when focus is lost 
				//       (for example by another editor appearing)
			if ("save" === this.settings.on_blur)
				form.find(".inplace_field").blur(saveEditorAction);
			else
				form.find(".inplace_field").blur(cancelEditorAction);
			
			// workaround for firefox bug where it won't submit on enter if no button is shown
			if ($.browser.mozilla) {
				form.keyup(function(event) {
					if (13 === event.which)
						saveEditorAction();
				});
			}
		}
		
		// allow canceling with escape
		form.keyup(function(event){
			if (27 === event.which) { // escape
				return cancelEditorAction();
			}
		});
		
		
		form.submit(saveEditorAction);
	},
	
	handleCancelEditor: function() {
		this.init();
		this.dom.html(this.originalHTML);
	},
	
	handleSaveEditor: function() {
		var enteredText = this.dom.find(':input').val();
		
		// no changes - no commit
		if ( ! this.settings.save_if_nothing_changed
			&& this.originalText === enteredText) {
			this.handleCancelEditor();
			return;
		}
		
		if (this.settings.value_required 
			&& ("" === enteredText || undefined === enteredText)) {
			this.dom.html(this.originalHTML);
			this.init();
			this.reportError("Error: You must enter a value to save this field");
			return;
		}
		
		this.showSavingMessage();
		
		if (this.settings.callback)
			this.handleSubmitToCallback(enteredText);
		else
			this.handleSubmitToServer(enteredText);
	},
	
	showSavingMessage: function() {
		var saving_message = this.settings.saving_text;
		if("" !== this.settings.saving_image)
			// REFACT: alt should be the configured saving message
			saving_message = '<img src="' + this.settings.saving_image + '" alt="Saving..." />';
		this.dom.html(saving_message);
	},
	
	handleSubmitToCallback: function(enteredText) {
		// REFACT: consider to encode enteredText and originalHTML before giving it to the callback
		var newHTML = this.settings.callback(this.id(), enteredText, this.originalHTML, this.settings.params);
		this.init();
		if (undefined === newHTML) {
			/* failure; put original back */
			this.reportError("Error: Failed to save value: " + enteredText);
			this.dom.html(this.originalHTML);
			return;
		}
		
		this.dom.html(newHTML);
	},
	
	handleSubmitToServer: function(enteredText) {
		var data = this.settings.update_value + '=' + encodeURIComponent(enteredText) 
			+ '&' + this.settings.element_id + '=' + this.dom.attr("id") 
			+ (('' !== this.settings.params) ? '&' + this.settings.params : '')
			+ '&' + this.settings.original_html + '=' + encodeURIComponent(this.originalHTML);
		
		var that = this;
		$.ajax({
			url: that.settings.url,
			type: "POST",
			data: data,
			dataType: "html",
			complete: function(request){
				that.init();
			},
			success: function(html){
				/* if the text returned by the server is empty, */
				/* put a marker as text in the original element */
				var new_text = html || that.settings.default_text;
				
				/* put the newly updated info into the original element */
				that.dom.html(new_text);
				if (that.settings.success)
					that.settings.success.apply(that, [html, that.dom]);
			},
			error: function(request) {
				that.dom.html(that.originalHTML); // REFACT: what about a restorePreEditingContent()
				if (that.settings.error)
					that.settings.error.apply(that, [request, that.dom]);
			}
		});
	},
	
	// Utilities .........................................................
	
	reportError: function(anErrorString) {
		if (this.settings.error_sink)
			this.settings.error_sink.apply(this, [this.id(), anErrorString]);
	},
	
	id: function() {
		return this.dom.attr('id');
	},
	
	missingCommaErrorPreventer:''
});



// Private helpers .......................................................

/* preload the loading icon if it is configured */
function preloadImage(anImageURL) {
	if ('' === anImageURL)
		return;
	
	var loading_image = new Image();
	loading_image.src = anImageURL;
}

function trim(aString) {
	return aString
		// trim
		.replace(/^\s+/, '')
		.replace(/\s+$/, '');
}

})(jQuery);