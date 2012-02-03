/**
 * Dropdown Navigation
 *
 * Norbert @ Laposa Ltd, 2012/01/08
 * 
 */
 
$(document).ready( function(){

    $('#primaryNavigation ul li').hover(
        function() {
            $(this).addClass('dropdown_open');
            $('ul', this).slideDown(350);
            var pos = $(this).offset();
            var width = $(this).width();
            var height = $(this).height();
            $('ul', this).css( { "left": pos.left + "px", "top": (pos.top + height) + "px" } );

        },
        function() {
        	$(this).removeClass('dropdown_open');
            $('ul', this).css('display', 'none');
        }
    );

	$('#primaryNavigation a').attr('title', '');
	
});