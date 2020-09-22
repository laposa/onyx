/**
 * Norbert @ Laposa Ltd, 2015/06/24
 */

/*reveal-primary-navigation*/
function revealPrimaryNavigation() {

    if ($('#primary-navigation').hasClass('open')) {
        $('#primary-navigation').removeClass('open');
    } else {
        $('#primary-navigation').addClass('open');
    }
}

$(function() {
    
    /**
     * show standard Onyx messages in a popup
     */
     
    popupMessage("div.onyx-messages");
    
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

    $('#reveal-navigation-button').click(function() {
        revealPrimaryNavigation();
        return false;
    });
    
    /**
     * write your own actions below
     */
    
    /* YOUR CODE HERE */
    
});
