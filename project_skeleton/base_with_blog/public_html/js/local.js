/**
 * Norbert @ Laposa Ltd, 2015/06/24
 */

/*revealPrimaryNavigation*/
function revealPrimaryNavigation() {

    if ($('#primaryNavigation').hasClass('open')) {
        $('#primaryNavigation').removeClass('open');
    } else {
        $('#primaryNavigation').addClass('open');
    }
}

$(function() {
	
	/**
	 * show standard Onxshop messages in a popup
	 */
	 
	popupMessage("div.onxshop_messages");
	
	/**
	 * remove white spaces between selected items to allow precise sizing with inline-block elements
	 */
	 
	/* 
	$("div.stack_list div.list").contents().filter(function() {
		return this.nodeType = Node.TEXT_NODE && /\S/.test(this.nodeValue) === false;
	}).remove();
	*/
	
	/**
     * mobile navigation
     */

    $('#revealNavigationButton').click(function() {
        revealPrimaryNavigation();
        return false;
    });
    
	/**
	 * write your own actions below
	 */
	
	/* YOUR CODE HERE */
	
});
