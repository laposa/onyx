/**
 * Dropdown Navigation
 *
 * Norbert @ Laposa Ltd, 2012/01/08
 * 
 */
 
$(document).ready( function(){

    $('#primaryNavigation ul li').hover(
        function() {
            $('ul', this).css('display', 'block');
            var pos = $(this).offset();
            var width = $(this).width();
            var height = $(this).height();
            $('ul', this).css( { "left": pos.left + "px", "top": (pos.top + height) + "px" } );

        },
        function() {
            $('ul', this).css('display', 'none');
        }
    );

});