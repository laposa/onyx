/**
 * Norbert Laposa, 2012/05/13
 */
 
/*revealPrimaryNavigation*/
function revealPrimaryNavigation() {
	
	if ($('#primaryNavigation').hasClass('open')) {
		$('#primaryNavigation').slideUp().removeClass('open');
	} else {
		$('#primaryNavigation').slideDown().addClass('open');
	}
}

$(function() {
	
	/**
	 * mobile navigation
	 */
	 
	$('#revealNavigationButton a').click(function() {
		revealPrimaryNavigation();
		return false;
	});
	
}